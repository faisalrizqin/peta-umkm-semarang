<?php
// index.php
session_start();

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['login'])) {
    header("Location: dashboard.php");
    exit;
}

// Proses login
$error = '';
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Login sederhana (nanti bisa dikembangkan dengan database user)
    if ($username == 'admin' && $password == 'admin123') {
        $_SESSION['login'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['nama_lengkap'] = 'Staff Ahli Walikota';
        header("Location: dashboard.php");
        exit;
    } else {
        $error = 'Username atau password salah!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Peta UMKM Semarang</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body class="login-page">
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <h1>🗺️ Peta UMKM Semarang</h1>
                <p>Sistem Informasi Pemetaan UMKM Kota Semarang</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" required autofocus>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <button type="submit" name="login" class="btn btn-primary btn-block">
                    Login
                </button>

                <div class="login-info">
                    <small>Default: admin / admin123</small>
                </div>
            </form>
        </div>
    </div>
</body>

<p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Iste error nemo perferendis soluta, porro rem dicta quam
    aperiam maiores qui quisquam, fugiat facere aspernatur doloremque quae rerum aut ratione. Exercitationem natus quam
    recusandae, illo dolorem blanditiis iusto corporis minima provident, deleniti mollitia nisi nihil libero temporibus
    ducimus pariatur delectus itaque, modi molestias incidunt. Sequi est mollitia libero nisi quo eos rem esse magnam
    iure a, reiciendis accusamus. Tempora aliquid illo dolorum provident eum sint expedita voluptatibus fugit animi
    dolor obcaecati earum, debitis et quisquam non vitae ullam saepe veritatis blanditiis quas omnis accusantium rerum,
    odit iure! Voluptatum quisquam, mollitia dolorum explicabo minus tempora vitae hic magnam ab pariatur illum
    quibusdam cumque quam aspernatur facilis recusandae ducimus labore a, dolore sint ratione? Accusantium animi
    possimus dolores alias consectetur a quisquam quibusdam at unde corrupti, libero voluptatum suscipit. Eligendi
    voluptate quia iure minus ab, quam dolor aliquid ratione, magnam atque facere exercitationem adipisci vero modi
    beatae aut. Deleniti modi suscipit fugiat amet id, tempora aliquam ullam veritatis officia quidem natus labore
    reiciendis voluptas, nesciunt error tenetur excepturi, ea ducimus neque ut officiis dicta facilis accusamus? Culpa,
    vitae cum odio provident saepe dolor at similique, accusantium minima animi aliquid placeat deleniti ut non optio
    quas velit! Assumenda voluptates incidunt explicabo suscipit aliquid iure provident? Dolorum ipsa et velit
    aspernatur illum quam nobis enim amet optio nemo, pariatur eaque cupiditate beatae blanditiis rerum voluptate error?
    Rem tempora, recusandae non totam fugiat, eius sit error obcaecati unde molestiae, quasi ab dolorum? Delectus, nam
    tenetur. Pariatur qui officiis possimus dignissimos nulla esse fugit maiores et libero? Excepturi repellendus
    quibusdam incidunt dolor nobis labore in similique cumque dolores quas, nihil nostrum possimus illo fugit eaque
    dicta provident explicabo earum assumenda porro. Neque molestias dolore, a quis quos error voluptatem perferendis
    expedita, maxime eius libero laudantium magni quo. Corrupti temporibus, magni eius rerum molestiae omnis accusamus
    explicabo dolorum nesciunt! Ipsa sequi delectus consequuntur, officiis error facere qui officia deleniti adipisci
    similique id ipsum commodi iure architecto quis vel consectetur magni maiores omnis nesciunt. Minus minima quis
    aliquid saepe officiis molestias. Culpa quaerat recusandae eaque architecto itaque aut consectetur. Dignissimos
    nobis quas dolore quis sed aspernatur temporibus ut enim accusamus aliquam, eveniet fugit tempora recusandae ea
    ipsam ex sequi exercitationem qui minus, maxime facilis odio! Ut sed voluptate eius laboriosam quidem dolorem rem
    aperiam autem quam, ratione quo unde similique sunt iure id dolor cupiditate aspernatur reiciendis minima! Harum
    dolore minima ducimus corrupti. Laudantium, inventore laborum? Odit, obcaecati deleniti dolor commodi beatae esse
    autem hic reprehenderit quod ipsa voluptates nesciunt ex dignissimos deserunt modi cum vero iure recusandae?
    Mollitia ab repellendus eius veritatis velit. Voluptatibus odit architecto sapiente provident esse non cupiditate
    animi quidem enim distinctio! Aspernatur esse, magnam incidunt, voluptas delectus maxime enim obcaecati ratione
    natus libero nesciunt officiis ipsum asperiores vitae. Totam magnam repellat esse quisquam nam aut quam distinctio.
    Saepe exercitationem, quaerat dolorem debitis velit sit cum? Deserunt ratione, ad animi illum voluptate quaerat
    saepe facilis quo, iure alias adipisci nulla non praesentium cumque dolores repudiandae.</p>

</html>