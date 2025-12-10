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

            <div class="alert">
                <strong>Info!</strong> Sistem ini dikembangkan untuk keperluan pemetaan UMKM di Kota Semarang.
            </div>
        </div>
    </div>
</body>

<p>
    Lorem ipsum dolor sit amet consectetur adipisicing elit. Cum magni enim voluptate commodi ratione deserunt eos ipsam quos veritatis aperiam tempore obcaecati, dicta error omnis dolorum unde? Maxime nihil officiis voluptatum, molestias id expedita quo praesentium voluptate atque! Veniam mollitia fuga veritatis obcaecati consectetur dolor tempore minima, ipsa nihil molestiae enim animi sunt accusantium, molestias minus nisi fugit perspiciatis quae perferendis non saepe temporibus repellat fugiat corporis? Exercitationem ea aperiam modi distinctio amet facere natus expedita, itaque illum, labore odio non? Qui optio fugiat ipsa voluptate animi omnis eveniet, voluptatum culpa sunt, deleniti non et tenetur ipsam suscipit natus fuga, ea deserunt quibusdam eum totam aperiam dolorem sint temporibus quae! Soluta nemo ut illum debitis eum fugit, possimus doloremque facere iure ea rem? Dolorum ea omnis reprehenderit sunt atque a nesciunt, aspernatur debitis saepe iusto porro voluptatem eius ex aliquam impedit accusamus consequatur quae ab voluptas velit cum repudiandae libero cupiditate et! Velit sit rem nobis, fugiat et, saepe alias, at praesentium incidunt perferendis dolorum corporis temporibus illum natus quam libero dolorem laudantium fugit rerum! Saepe explicabo eligendi quisquam, ut libero ad architecto? Ipsum tempore tenetur, quaerat neque fuga dolores earum sequi mollitia minima iste delectus exercitationem placeat cupiditate quas officiis id rem explicabo dolorum officia sed quisquam magni accusantium. Tenetur, reiciendis? Nihil placeat deleniti, quae cum veniam, facilis ea illum suscipit doloremque eum soluta iure, voluptatem repellat nobis culpa quaerat perspiciatis. Veniam, ex. Architecto saepe deleniti vitae doloremque beatae, fugiat tempora rerum esse libero totam recusandae quasi itaque harum facilis consectetur. Doloremque quam ullam assumenda distinctio laudantium pariatur porro expedita animi optio rem. Nisi optio quasi sint maiores ducimus voluptatum, dolor a molestias delectus. Eveniet ipsa recusandae fugit ut. Saepe esse explicabo sit a accusantium dolores tenetur praesentium blanditiis quod in delectus consequuntur totam fugit odio autem officia laborum, earum dicta? Doloremque nostrum voluptatem eaque possimus quae cupiditate nihil inventore nesciunt vero veritatis ut quia illo vel debitis, ab, aspernatur maxime aut. Impedit asperiores rem dolores debitis fugit, earum assumenda eius ab doloribus vel, sapiente veritatis eum harum quidem. Non quas reprehenderit, minus nobis laboriosam minima error maiores odio. Officiis error molestiae harum debitis consectetur rerum nisi aliquam voluptas fugit perspiciatis et, architecto ex illo corrupti? Soluta dolore nemo quae ipsam minus temporibus possimus rerum placeat ex natus cupiditate eveniet itaque eaque quos deserunt nobis sapiente, laudantium amet voluptatum ipsa quam a expedita cum adipisci! Dolores ratione delectus itaque unde dolore repellat, quos ipsum labore eos alias quis soluta id eligendi et ex quaerat ut dolorum, officiis culpa possimus? Ea quo ex, minus dolorum autem ducimus delectus, asperiores non accusamus doloribus, nulla animi explicabo iure ipsum sapiente enim. Reiciendis explicabo incidunt magni, provident, ad excepturi voluptatem veniam animi eius tempore quibusdam cum totam dolores, enim accusamus. Distinctio exercitationem earum voluptatum nisi, aliquam quia dicta, explicabo facere sequi accusamus veritatis laborum est temporibus qui commodi sapiente non. Unde consectetur ipsa vero praesentium possimus, soluta accusantium, velit aliquam deserunt reiciendis rerum nulla dolor animi aspernatur. Provident dicta cumque aliquid dolorum illum soluta. Deserunt assumenda vel debitis atque? Iste quibusdam distinctio eius rem, aut nihil mollitia expedita voluptatibus neque sint molestias nam error dolorem architecto eveniet dolores aspernatur. Porro maiores laudantium ex exercitationem sed sunt vitae tenetur, laborum placeat beatae rem dolorum ut ea quaerat assumenda eaque aliquam quod reiciendis id, labore facere? Voluptatum, ipsa vero optio esse, placeat molestias aliquam eius voluptatem impedit magnam, recusandae quos quasi nihil soluta officiis quisquam sint quis tenetur. Ullam totam quas soluta nesciunt. Accusamus optio dolores ipsa quidem, facere possimus beatae delectus nobis voluptates consequuntur sit. Numquam quia pariatur distinctio possimus magni fuga corrupti voluptatum praesentium molestias, tempore earum obcaecati ullam laboriosam voluptatibus dolorum dolor velit id impedit sit laudantium porro nisi autem blanditiis? Totam, odit, expedita sequi ullam suscipit cupiditate dolore quos earum, cum excepturi accusantium ipsam libero eveniet exercitationem assumenda autem itaque? Amet blanditiis vel nostrum sit obcaecati veniam labore aut velit non, nemo quidem quam in corporis commodi impedit illo eligendi iure accusantium deserunt repudiandae numquam! Totam ad laborum assumenda eveniet blanditiis quaerat, voluptates doloremque repellat ipsa culpa velit a aperiam suscipit hic in nostrum ducimus! Voluptate, odio repellendus dolores nesciunt nostrum obcaecati, veniam distinctio animi, maxime id est! Illo aliquam, consequatur tenetur, dignissimos nemo voluptatibus esse praesentium ipsa dolorem corrupti blanditiis nostrum, maiores quis itaque voluptatum non consectetur? Ratione repellendus labore tempore facere distinctio, sint inventore quidem, corrupti quas asperiores voluptatem, ad corporis impedit iste mollitia tempora saepe ipsum eius? Culpa praesentium in tenetur, autem facere eaque sunt vitae id et doloribus nihil aliquid omnis quia voluptates. Provident consectetur, maxime consequatur blanditiis fugiat quisquam recusandae modi delectus itaque! Dolores voluptatum, deleniti quas cum laborum aut quo corporis delectus, vel aliquam expedita possimus ad cupiditate consequatur quibusdam consequuntur non. Maxime quibusdam eius harum dolorum saepe nobis, dolores veritatis atque qui, earum sit? Dolor, consequuntur. Impedit molestiae quisquam voluptatum voluptatem blanditiis quas eligendi modi facere deserunt accusamus sed iste corrupti reiciendis laboriosam cupiditate, asperiores dolores repellendus nemo dignissimos aliquam accusantium in fugit! Esse minima dicta ab autem animi doloremque iure assumenda quisquam quaerat nihil fugiat cum aliquam reiciendis voluptatum, earum aspernatur. Unde optio, expedita maxime minima officiis, consequatur sunt quos quibusdam sit et iure, saepe quod illo voluptatem culpa tempora? Cum ea accusantium totam in voluptatem saepe, reiciendis dolore similique veniam dolorem quis ducimus laudantium minima porro est sit magnam deserunt? Sequi, odio magnam? Id, placeat illo sint ullam architecto veniam sunt reiciendis amet dolores sit adipisci maiores repellendus aperiam. Fugit consequatur tempore quibusdam delectus consectetur asperiores, itaque voluptatum perferendis accusamus qui eligendi eius porro doloribus odit mollitia at. Debitis ea itaque voluptatum necessitatibus sit aperiam quam minima deleniti velit magni veritatis delectus eius quisquam beatae repellendus laborum reiciendis, asperiores aspernatur earum eveniet hic, consequatur quae tenetur enim? Saepe similique culpa alias deserunt dolores tempora nisi hic incidunt quibusdam enim cumque ratione quasi recusandae ipsum mollitia aliquid reiciendis doloribus, magni amet explicabo modi autem! Labore nulla quae odit? Obcaecati, repellat suscipit? Eveniet non consequuntur amet dolorem iste omnis eaque error totam et labore?
</p>
</html>