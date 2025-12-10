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
</html>