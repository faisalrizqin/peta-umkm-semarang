# generate_data_umkm.py
import random
import mysql.connector
from faker import Faker

# Inisialisasi Faker untuk Bahasa Indonesia
fake = Faker('id_ID')

# Koneksi Database
db = mysql.connector.connect(
    host="localhost",
    user="root",
    password="",
    database="db_umkm_semarang"
)

cursor = db.cursor()

# Data Master
kecamatan_data = {
    'Semarang Tengah': {'lat': -6.9833, 'lon': 110.4167},
    'Semarang Utara': {'lat': -6.9533, 'lon': 110.4167},
    'Semarang Timur': {'lat': -6.9900, 'lon': 110.4417},
    'Semarang Selatan': {'lat': -7.0167, 'lon': 110.4250},
    'Semarang Barat': {'lat': -6.9833, 'lon': 110.3833},
    'Gayamsari': {'lat': -6.9667, 'lon': 110.4500},
    'Genuk': {'lat': -6.9333, 'lon': 110.4833},
    'Pedurungan': {'lat': -7.0167, 'lon': 110.4583},
    'Candisari': {'lat': -7.0167, 'lon': 110.4083},
    'Gajahmungkur': {'lat': -7.0417, 'lon': 110.4000},
    'Tembalang': {'lat': -7.0500, 'lon': 110.4417},
    'Banyumanik': {'lat': -7.0667, 'lon': 110.4333},
    'Gunungpati': {'lat': -7.0833, 'lon': 110.3750},
    'Mijen': {'lat': -7.0583, 'lon': 110.3167},
    'Ngaliyan': {'lat': -7.0333, 'lon': 110.3500},
    'Tugu': {'lat': -6.9167, 'lon': 110.3667}
}

# Data Kategori dengan ID
kategori_data = {
    1: {
        'nama': 'Kuliner',
        'jenis_usaha': [
            'Warung Makan', 'Bakso', 'Soto', 'Nasi Goreng', 'Ayam Goreng',
            'Pecel Lele', 'Sate', 'Gado-gado', 'Mie Ayam', 'Bubur Ayam',
            'Martabak', 'Kue Basah', 'Kue Kering', 'Roti', 'Donat',
            'Es Teh', 'Jus Buah', 'Kopi', 'Catering', 'Snack'
        ],
        'bahan_baku': [
            'Beras', 'Tepung terigu', 'Daging ayam', 'Daging sapi', 'Ikan',
            'Sayuran', 'Bumbu dapur', 'Telur', 'Gula', 'Minyak goreng',
            'Santan', 'Buah-buahan', 'Coklat', 'Keju', 'Susu'
        ],
        'alat_produksi': [
            'Kompor gas', 'Wajan', 'Panci', 'Rice cooker', 'Blender',
            'Mixer', 'Oven', 'Kulkas', 'Freezer', 'Etalase kaca',
            'Mesin giling', 'Penggorengan', 'Dandang', 'Steamer', 'Food processor'
        ]
    },
    2: {
        'nama': 'Fashion',
        'jenis_usaha': [
            'Konveksi Baju', 'Batik', 'Tas', 'Sepatu', 'Hijab',
            'Kaos', 'Gamis', 'Kemeja', 'Celana', 'Aksesoris',
            'Daster', 'Mukena', 'Kebaya', 'Baju Anak', 'Pakaian Adat'
        ],
        'bahan_baku': [
            'Kain katun', 'Kain sutra', 'Kain jersey', 'Kulit', 'Benang jahit',
            'Kancing', 'Resleting', 'Kain batik', 'Kain wolfis', 'Payet',
            'Manik-manik', 'Pita', 'Kain flanel', 'Busa', 'Dakron'
        ],
        'alat_produksi': [
            'Mesin jahit', 'Obras', 'Mesin bordir', 'Gunting kain', 'Pola',
            'Mesin cutting', 'Setrika', 'Mannequin', 'Meja cutting', 'Meteran',
            'Jarum jahit', 'Mesin lubang kancing', 'Mesin obras portable', 'Papan setrika', 'Hanger'
        ]
    },
    3: {
        'nama': 'Kerajinan',
        'jenis_usaha': [
            'Anyaman Bambu', 'Keramik', 'Patung Kayu', 'Miniatur', 'Rajutan',
            'Boneka', 'Souvenir', 'Lilin', 'Lampu Hias', 'Lukisan',
            'Pot Tanaman', 'Frame Foto', 'Tas Rajut', 'Bros', 'Gantungan Kunci'
        ],
        'bahan_baku': [
            'Bambu', 'Rotan', 'Kayu jati', 'Kayu meranti', 'Tanah liat',
            'Parafin', 'Benang wol', 'Kain flanel', 'Cat akrilik', 'Lem',
            'Kertas', 'Kawat', 'Manik-manik', 'Resin', 'Batu alam'
        ],
        'alat_produksi': [
            'Pahat', 'Gergaji', 'Amplas', 'Jarum rajut', 'Hakken',
            'Tungku pembakaran', 'Mesin putar keramik', 'Kuas', 'Cetakan',
            'Tang', 'Gunting', 'Lem tembak', 'Bor', 'Gerinda', 'Kompor'
        ]
    },
    4: {
        'nama': 'Jasa',
        'jenis_usaha': [
            'Salon', 'Barbershop', 'Cuci Motor', 'Cuci Mobil', 'Service Elektronik',
            'Las Besi', 'Fotocopy', 'Print', 'Les Privat', 'Catering',
            'Laundry', 'Desain Grafis', 'Reparasi HP', 'Tukang Bangunan', 'Bengkel'
        ],
        'bahan_baku': [
            'Sabun cuci', 'Shampoo', 'Cat rambut', 'Vitamin rambut', 'Wax',
            'Detergen', 'Pewangi', 'Tinta printer', 'Kertas', 'Buku',
            'Elektroda las', 'Komponen elektronik', 'Oli', 'Ban', 'Spare part'
        ],
        'alat_produksi': [
            'Gunting rambut', 'Catok', 'Hair dryer', 'Kompresor', 'Selang air',
            'Mesin fotocopy', 'Printer', 'Laptop', 'Mesin las', 'Gerinda',
            'Multitester', 'Solder', 'Kunci', 'Obeng', 'Tang'
        ]
    },
    5: {
        'nama': 'Furniture',
        'jenis_usaha': [
            'Mebel Jati', 'Kursi Rotan', 'Lemari', 'Sofa', 'Tempat Tidur',
            'Meja Belajar', 'Kitchen Set', 'Rak Buku', 'Bufet', 'Nakas'
        ],
        'bahan_baku': [
            'Kayu jati', 'Kayu mahoni', 'Kayu pinus', 'Rotan', 'MDF',
            'HPL', 'Kaca', 'Busa sofa', 'Kain pelapis', 'Pernis',
            'Paku', 'Sekrup', 'Engsel', 'Handle', 'Cat kayu'
        ],
        'alat_produksi': [
            'Gergaji mesin', 'Mesin profil', 'Mesin ketam', 'Bor listrik', 'Mesin amplas',
            'Kompresor cat', 'Spray gun', 'Palu', 'Meteran', 'Waterpass',
            'Mesin router', 'Mesin bor duduk', 'Stapler tembak', 'Gergaji circular', 'Mesin skrol'
        ]
    },
    6: {
        'nama': 'Pertanian',
        'jenis_usaha': [
            'Keripik Singkong', 'Keripik Pisang', 'Jamur Crispy', 'Emping', 'Kacang',
            'Sale Pisang', 'Manisan Buah', 'Dodol', 'Selai', 'Sirup'
        ],
        'bahan_baku': [
            'Singkong', 'Pisang', 'Jamur tiram', 'Kacang tanah', 'Melinjo',
            'Gula', 'Garam', 'Bumbu', 'Tepung', 'Minyak goreng',
            'Pengawet alami', 'Buah-buahan', 'Ketan', 'Kelapa', 'Jahe'
        ],
        'alat_produksi': [
            'Pisau perajang', 'Mesin perajang', 'Penggorengan', 'Kompor gas', 'Spinner',
            'Mesin vacuum', 'Timbangan', 'Kemasan plastik', 'Sealer', 'Label',
            'Baskom', 'Tampah', 'Oven pengering', 'Blender', 'Saringan'
        ]
    },
    7: {
        'nama': 'Kecantikan',
        'jenis_usaha': [
            'Kosmetik', 'Skincare', 'Body Care', 'Sabun Herbal', 'Lulur',
            'Masker Wajah', 'Serum', 'Lip Tint', 'Bedak', 'Foundation'
        ],
        'bahan_baku': [
            'Minyak esensial', 'Ekstrak tanaman', 'Glycerin', 'Vitamin E', 'Aloe vera',
            'Kojic acid', 'Niacinamide', 'Hyaluronic acid', 'Pewarna kosmetik', 'Pengawet kosmetik',
            'Minyak kelapa', 'Shea butter', 'Beeswax', 'Parfum', 'Alkohol'
        ],
        'alat_produksi': [
            'Mixer kosmetik', 'Timbangan digital', 'Gelas ukur', 'Spatula', 'Botol pump',
            'Jar kaca', 'Tube kemasan', 'Label produk', 'Kompor', 'Panci stainless',
            'Cetakan sabun', 'Pengaduk', 'Saringan halus', 'Pipet', 'Sarung tangan'
        ]
    },
    8: {
        'nama': 'Teknologi',
        'jenis_usaha': [
            'Service Laptop', 'Aksesori HP', 'Powerbank', 'Casing HP', 'Kabel Data',
            'Speaker Bluetooth', 'Earphone', 'Software Development', 'Web Development', 'Desain UI/UX'
        ],
        'bahan_baku': [
            'Komponen elektronik', 'PCB', 'IC', 'Resistor', 'Kapasitor',
            'Kabel', 'Plastik casing', 'Baterai lithium', 'LED', 'Solder timah',
            'Thermal paste', 'Lisensi software', 'Domain', 'Hosting', 'SSL'
        ],
        'alat_produksi': [
            'Laptop', 'PC', 'Monitor', 'Keyboard', 'Mouse',
            'Solder station', 'Hot air gun', 'Multitester', 'Oscilloscope', 'Power supply',
            'Obeng set', 'Pinset', 'Kaca pembesar', 'Tool kit', 'ESD mat'
        ]
    }
}

# Fungsi untuk random koordinat dengan variasi
def random_koordinat(base_lat, base_lon):
    # Variasi ±0.02 derajat (sekitar ±2km)
    lat = base_lat + random.uniform(-0.02, 0.02)
    lon = base_lon + random.uniform(-0.02, 0.02)
    return round(lat, 6), round(lon, 6)

# Fungsi untuk generate nama UMKM
def generate_nama_umkm(kategori_id):
    jenis = random.choice(kategori_data[kategori_id]['jenis_usaha'])
    
    # Variasi nama
    prefix = ['', 'CV ', 'UD ', 'Toko ', 'Warung ', 'Usaha ', 'Bengkel ', '']
    suffix = ['', ' Jaya', ' Makmur', ' Sejahtera', ' Berkah', ' Mandiri', 
              ' Sukses', ' Abadi', ' Mulya', ' Sentosa', ' Indah']
    
    if random.random() > 0.5:
        # Gunakan nama pemilik
        nama = f"{prefix[random.randint(0, len(prefix)-1)]}{jenis} {fake.first_name()}{suffix[random.randint(0, len(suffix)-1)]}"
    else:
        # Gunakan nama kreatif
        nama = f"{prefix[random.randint(0, len(prefix)-1)]}{jenis} {random.choice(['Prima', 'Citra', 'Mega', 'Bintang', 'Cahaya', 'Permata', 'Mulia', 'Agung'])}{suffix[random.randint(0, len(suffix)-1)]}"
    
    return nama.strip()

# Fungsi untuk generate kelurahan
kelurahan_list = [
    'Pandanaran', 'Sekayu', 'Kembangsari', 'Peterongan', 'Kauman',
    'Kranggan', 'Gabahan', 'Jagalan', 'Miroto', 'Purwodinatan',
    'Tanjung Mas', 'Bandarharjo', 'Kuningan', 'Dadapsari', 'Panggung Lor',
    'Bugangan', 'Mlatiharjo', 'Karangturi', 'Rejomulyo', 'Kemijen',
    'Lamper Kidul', 'Lamper Lor', 'Mugassari', 'Randusari', 'Pleburan',
    'Krobokan', 'Cabean', 'Bongsari', 'Krapyak', 'Ngemplak Simongan',
    'Kaligawe', 'Sambirejo', 'Gayamsari', 'Sawah Besar', 'Tambakrejo',
    'Terboyo Kulon', 'Terboyo Wetan', 'Bangetayu Kulon', 'Bangetayu Wetan', 'Muktiharjo Kidul',
    'Pedurungan Kidul', 'Pedurungan Lor', 'Pedurungan Tengah', 'Tlogosari Kulon', 'Tlogosari Wetan',
    'Gajahmungkur', 'Lempongsari', 'Sampangan', 'Bendan Dhuwur', 'Bendan Ngisor',
    'Tembalang', 'Bulusan', 'Jangli', 'Sendangmulyo', 'Tandang',
    'Banyumanik', 'Gedawang', 'Padangsari', 'Pedalangan', 'Srondol Kulon',
    'Gunungpati', 'Sekaran', 'Sukorejo', 'Ngijo', 'Sadeng',
    'Mijen', 'Bubakan', 'Cangkiran', 'Jatibarang', 'Kedungpane',
    'Ngaliyan', 'Podorejo', 'Purwoyoso', 'Wonosari', 'Wates',
    'Tugurejo', 'Karanganyar', 'Mangkang Kulon', 'Mangkang Wetan', 'Randugarut'
]

# Generate 5000 data
print("Mulai generate 5000 data UMKM...")
print("=" * 60)

batch_size = 100
total_data = 5000
generated = 0

for batch in range(total_data // batch_size):
    values = []
    
    for i in range(batch_size):
        # Random kategori
        kategori_id = random.randint(1, 8)
        
        # Random kecamatan
        kecamatan = random.choice(list(kecamatan_data.keys()))
        base_lat = kecamatan_data[kecamatan]['lat']
        base_lon = kecamatan_data[kecamatan]['lon']
        
        # Generate koordinat dengan variasi
        lat, lon = random_koordinat(base_lat, base_lon)
        
        # Generate data
        nama_umkm = generate_nama_umkm(kategori_id)
        nama_pemilik = fake.name()
        alamat = f"{fake.street_name()} No. {random.randint(1, 999)}"
        rt = str(random.randint(1, 15)).zfill(3)
        rw = str(random.randint(1, 10)).zfill(3)
        kelurahan = random.choice(kelurahan_list)
        
        # Random bahan baku (2-5 item)
        bahan_baku = ', '.join(random.sample(
            kategori_data[kategori_id]['bahan_baku'], 
            random.randint(2, min(5, len(kategori_data[kategori_id]['bahan_baku'])))
        ))
        
        # Random alat produksi (2-5 item)
        alat_produksi = ', '.join(random.sample(
            kategori_data[kategori_id]['alat_produksi'], 
            random.randint(2, min(5, len(kategori_data[kategori_id]['alat_produksi'])))
        ))
        
        # Deskripsi
        deskripsi_list = [
            f"Usaha {kategori_data[kategori_id]['nama'].lower()} yang melayani masyarakat Semarang",
            f"Produk berkualitas dengan harga terjangkau",
            f"Melayani pesanan dalam jumlah besar maupun eceran",
            f"Berpengalaman lebih dari {random.randint(2, 20)} tahun",
            f"Menggunakan bahan baku pilihan dan berkualitas",
            f"Produk higienis dan terjamin kebersihannya",
            f"Melayani dengan ramah dan profesional"
        ]
        deskripsi = random.choice(deskripsi_list)
        
        values.append((
            nama_umkm, nama_pemilik, alamat, rt, rw, kelurahan, kecamatan,
            kategori_id, deskripsi, bahan_baku, alat_produksi, lat, lon
        ))
    
    # Bulk insert
    sql = """INSERT INTO umkm 
             (nama_umkm, nama_pemilik, alamat_lengkap, rt, rw, kelurahan, kecamatan, 
              id_kategori, deskripsi, bahan_baku_utama, alat_produksi_utama, latitude, longitude) 
             VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)"""
    
    cursor.executemany(sql, values)
    db.commit()
    
    generated += batch_size
    print(f"Progress: {generated}/{total_data} data ({(generated/total_data)*100:.1f}%)")

print("=" * 60)
print(f"✅ Berhasil generate {total_data} data UMKM!")
print("=" * 60)

# Tampilkan statistik
cursor.execute("SELECT COUNT(*) FROM umkm")
total = cursor.fetchone()[0]

cursor.execute("""
    SELECT k.nama_kategori, COUNT(u.id_umkm) as total
    FROM kategori k
    LEFT JOIN umkm u ON k.id_kategori = u.id_kategori
    GROUP BY k.id_kategori
""")

print("\nStatistik per Kategori:")
print("-" * 40)
for row in cursor.fetchall():
    print(f"{row[0]:<20} : {row[1]:>5} UMKM")

cursor.close()
db.close()

print("\n✅ Selesai! Database siap untuk analisis.")