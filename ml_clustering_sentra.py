# ml_clustering_sentra.py
import mysql.connector
import pandas as pd
import numpy as np
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.cluster import KMeans
from sklearn.decomposition import PCA
import json
import pickle
from collections import Counter
from decimal import Decimal

print("=" * 70)
print("ANALISIS CLUSTERING SENTRA PRODUKSI UMKM KOTA SEMARANG")
print("=" * 70)

# Fungsi untuk convert Decimal ke float
def decimal_to_float(obj):
    if isinstance(obj, Decimal):
        return float(obj)
    raise TypeError

# Koneksi Database
db = mysql.connector.connect(
    host="localhost",
    user="root",
    password="",
    database="db_umkm_semarang"
)

cursor = db.cursor(dictionary=True)

# Ambil semua data UMKM
print("\n[1/6] Mengambil data dari database...")
query = """
    SELECT u.id_umkm, u.nama_umkm, u.kecamatan, u.kelurahan,
           u.bahan_baku_utama, u.alat_produksi_utama, 
           k.nama_kategori, u.latitude, u.longitude
    FROM umkm u
    LEFT JOIN kategori k ON u.id_kategori = k.id_kategori
"""
cursor.execute(query)
data = cursor.fetchall()
df = pd.DataFrame(data)

print(f"✅ Data berhasil diambil: {len(df)} UMKM")

# Gabungkan bahan baku dan alat produksi untuk analisis
print("\n[2/6] Preprocessing data...")
df['combined_features'] = df['bahan_baku_utama'] + ' ' + df['alat_produksi_utama']
df['combined_features'] = df['combined_features'].fillna('')

# TF-IDF Vectorization untuk mengubah text menjadi angka
print("\n[3/6] Melakukan TF-IDF Vectorization...")
tfidf = TfidfVectorizer(
    max_features=100,  # Ambil 100 fitur paling penting
    stop_words=None,
    ngram_range=(1, 2)  # Unigram dan bigram
)

tfidf_matrix = tfidf.fit_transform(df['combined_features'])
print(f"✅ Matrix TF-IDF berukuran: {tfidf_matrix.shape}")

# Tentukan jumlah cluster optimal menggunakan Elbow Method
print("\n[4/6] Menentukan jumlah cluster optimal...")
inertias = []
K_range = range(3, 15)

for k in K_range:
    kmeans_temp = KMeans(n_clusters=k, random_state=42, n_init=10)
    kmeans_temp.fit(tfidf_matrix)
    inertias.append(kmeans_temp.inertia_)

# Pilih K optimal (dalam kasus ini kita gunakan 8 sesuai jumlah kategori)
optimal_k = 8
print(f"✅ Jumlah cluster optimal: {optimal_k}")

# K-Means Clustering
print(f"\n[5/6] Melakukan K-Means Clustering dengan {optimal_k} cluster...")
kmeans = KMeans(n_clusters=optimal_k, random_state=42, n_init=10, max_iter=300)
clusters = kmeans.fit_predict(tfidf_matrix)

# Tambahkan hasil cluster ke dataframe
df['cluster'] = clusters

# Analisis setiap cluster
print("\n[6/6] Menganalisis karakteristik setiap cluster...")
print("=" * 70)

cluster_analysis = []

for cluster_id in range(optimal_k):
    cluster_data = df[df['cluster'] == cluster_id]
    
    # Analisis bahan baku paling umum
    all_bahan = ' '.join(cluster_data['bahan_baku_utama'].fillna('')).split(',')
    all_bahan = [b.strip() for b in all_bahan if b.strip()]
    bahan_common = Counter(all_bahan).most_common(5)
    
    # Analisis alat produksi paling umum
    all_alat = ' '.join(cluster_data['alat_produksi_utama'].fillna('')).split(',')
    all_alat = [a.strip() for a in all_alat if a.strip()]
    alat_common = Counter(all_alat).most_common(5)
    
    # Analisis kecamatan dominan
    kecamatan_dist = cluster_data['kecamatan'].value_counts().head(3)
    
    # Analisis kategori dominan
    kategori_dist = cluster_data['nama_kategori'].value_counts().head(3)
    
    # Hitung centroid geografis
    avg_lat = cluster_data['latitude'].mean()
    avg_lon = cluster_data['longitude'].mean()
    
    # Tentukan nama sentra berdasarkan karakteristik
    if len(kategori_dist) > 0:
        kategori_utama = kategori_dist.index[0]
    else:
        kategori_utama = "Campuran"
    
    if len(kecamatan_dist) > 0:
        kecamatan_utama = kecamatan_dist.index[0]
    else:
        kecamatan_utama = "Multi Kecamatan"
    
    nama_sentra = f"Sentra {kategori_utama} {kecamatan_utama}"
    
    # Convert umkm_list dengan konversi Decimal
    umkm_list = []
    for _, row in cluster_data[['id_umkm', 'nama_umkm', 'kecamatan', 'latitude', 'longitude']].iterrows():
        umkm_list.append({
            'id_umkm': int(row['id_umkm']),
            'nama_umkm': str(row['nama_umkm']),
            'kecamatan': str(row['kecamatan']),
            'latitude': float(row['latitude']) if row['latitude'] is not None else None,
            'longitude': float(row['longitude']) if row['longitude'] is not None else None
        })
    
    analysis = {
        'cluster_id': int(cluster_id),
        'nama_sentra': nama_sentra,
        'jumlah_umkm': len(cluster_data),
        'kategori_dominan': kategori_dist.to_dict() if len(kategori_dist) > 0 else {},
        'kecamatan_dominan': kecamatan_dist.to_dict() if len(kecamatan_dist) > 0 else {},
        'bahan_baku_umum': [{'nama': item[0], 'frekuensi': item[1]} for item in bahan_common],
        'alat_produksi_umum': [{'nama': item[0], 'frekuensi': item[1]} for item in alat_common],
        'centroid_lat': float(avg_lat),
        'centroid_lon': float(avg_lon),
        'umkm_list': umkm_list
    }
    
    cluster_analysis.append(analysis)
    
    # Print hasil
    print(f"\n🎯 CLUSTER {cluster_id}: {nama_sentra}")
    print("-" * 70)
    print(f"   Jumlah UMKM: {len(cluster_data)}")
    print(f"\n   📊 Kategori Dominan:")
    for kat, count in kategori_dist.items():
        print(f"      - {kat}: {count} UMKM ({count/len(cluster_data)*100:.1f}%)")
    
    print(f"\n   📍 Kecamatan Dominan:")
    for kec, count in kecamatan_dist.items():
        print(f"      - {kec}: {count} UMKM ({count/len(cluster_data)*100:.1f}%)")
    
    print(f"\n   🧰 Bahan Baku Umum:")
    for item in bahan_common[:3]:
        print(f"      - {item[0]}: {item[1]}x")
    
    print(f"\n   🔧 Alat Produksi Umum:")
    for item in alat_common[:3]:
        print(f"      - {item[0]}: {item[1]}x")
    
    print(f"\n   🗺️  Pusat Geografis: ({avg_lat:.4f}, {avg_lon:.4f})")

# Simpan hasil analisis ke JSON
print("\n" + "=" * 70)
print("💾 Menyimpan hasil analisis...")

with open('cluster_analysis.json', 'w', encoding='utf-8') as f:
    json.dump(cluster_analysis, f, ensure_ascii=False, indent=2)

print("✅ Hasil analisis disimpan di: cluster_analysis.json")

# Simpan model untuk digunakan di aplikasi web
print("💾 Menyimpan model Machine Learning...")

with open('tfidf_model.pkl', 'wb') as f:
    pickle.dump(tfidf, f)

with open('kmeans_model.pkl', 'wb') as f:
    pickle.dump(kmeans, f)

print("✅ Model ML disimpan: tfidf_model.pkl, kmeans_model.pkl")

# Update database dengan hasil cluster
print("\n💾 Menyimpan hasil clustering ke database...")

# Buat tabel untuk menyimpan hasil clustering
cursor.execute("""
    CREATE TABLE IF NOT EXISTS cluster_sentra (
        id_cluster INT PRIMARY KEY,
        nama_sentra VARCHAR(200),
        jumlah_umkm INT,
        kategori_dominan TEXT,
        kecamatan_dominan TEXT,
        bahan_baku_umum TEXT,
        alat_produksi_umum TEXT,
        centroid_lat DECIMAL(10, 6),
        centroid_lon DECIMAL(11, 6),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )
""")

# Hapus data lama
cursor.execute("TRUNCATE TABLE cluster_sentra")

# Insert hasil cluster
for cluster in cluster_analysis:
    sql = """INSERT INTO cluster_sentra 
             (id_cluster, nama_sentra, jumlah_umkm, kategori_dominan, 
              kecamatan_dominan, bahan_baku_umum, alat_produksi_umum,
              centroid_lat, centroid_lon)
             VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s)"""
    
    values = (
        cluster['cluster_id'],
        cluster['nama_sentra'],
        cluster['jumlah_umkm'],
        json.dumps(cluster['kategori_dominan'], ensure_ascii=False),
        json.dumps(cluster['kecamatan_dominan'], ensure_ascii=False),
        json.dumps(cluster['bahan_baku_umum'], ensure_ascii=False),
        json.dumps(cluster['alat_produksi_umum'], ensure_ascii=False),
        cluster['centroid_lat'],
        cluster['centroid_lon']
    )
    
    cursor.execute(sql, values)

db.commit()

# Update tabel UMKM dengan cluster_id
print("💾 Mengupdate tabel UMKM dengan cluster_id...")

# Tambah kolom cluster_id jika belum ada
try:
    cursor.execute("ALTER TABLE umkm ADD COLUMN cluster_id INT")
    db.commit()
except:
    pass  # Kolom sudah ada

# Update cluster_id untuk setiap UMKM
for idx, row in df.iterrows():
    cursor.execute(
        "UPDATE umkm SET cluster_id = %s WHERE id_umkm = %s",
        (int(row['cluster']), int(row['id_umkm']))
    )

db.commit()

print("✅ Database berhasil diupdate dengan hasil clustering")

# Summary
print("\n" + "=" * 70)
print("📊 RINGKASAN ANALISIS")
print("=" * 70)
print(f"Total UMKM dianalisis    : {len(df)}")
print(f"Jumlah Sentra teridentifikasi : {optimal_k}")
print(f"Fitur TF-IDF             : {tfidf_matrix.shape[1]}")
print(f"\n✅ Analisis Machine Learning selesai!")
print("=" * 70)

cursor.close()
db.close()