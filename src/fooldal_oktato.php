<?php
session_start();

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != "1") {
    // Ellenőrizze, hogy a felhasználó be van-e jelentkezve és oktató-e
    header("Location: login.php");
    exit();
}

// Adatbázis kapcsolat
$servername = "localhost";
$username = "Admin";
$password = "_K*uqlR2qRzexuzw";
$dbname = "SZD_jatekositas";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Kapcsolódási hiba: " . $conn->connect_error);
}

// Felhasználó adatainak lekérdezése
$username = $_SESSION['username'];
$sql = "SELECT avatar, neptun_kod FROM users WHERE neptun_kod = '$username'";
$result = $conn->query($sql);

if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
    $avatar = $row['avatar']; // Avatar elérési útvonala az adatbázisból
    $username = $row['neptun_kod']; // Neptun kód az adatbázisból
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="hu">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Főoldal</title>
    <link href="style.css" rel="stylesheet" />
</head>

<body>

    <table class="main-table">
        <tr>
            <td colspan="5" class="banner">
                <div class="avatar-info">
                    <div class="avatar">
                        <!-- Az avatar kép megjelenítése
                    <img src="data:image/png;base64,<?php echo base64_encode($row['avatar']); ?>" width="150" height="150"> -->
                        <img src="<?php echo $avatar; ?>" alt="Avatar" width="150" height="150">
                    </div>
                    <div class="user-info">
                        <div class="neptun-kod">
                            <!-- A Neptun kód megjelenítése -->
                            Neptun kód: <?php echo $username; ?>
                        </div>
                        <div class="profile-link">
                            <a href="profil_oktato.php">Profilom</a>
                        </div>
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="5" class="menu">

                <div class="nav-menu">
                    <div class="left-menu"><a href=fooldal_oktato.php target="_blank">Főoldal</a></div>
                    <div class="left-menu"><a href=kurzusok.php target="_blank">Kurzusaim</a></div>
                    <div class="right-menu"><a href=logout.php>Kijelentkezés</a></div>
                </div>
                </div>
            </td>
        </tr>
        <tr>

        </tr>
        <tr>
            <td colspan="5" class="content">
                <h1>Köszöntelek a Dragon Quill oldalon!</h1>
                <div class="main-page">
                    <img src="./greenDragon.png" alt="Zöld sárkány" height="300px">

                    <br><br><br>
                    <p>Mi is az a Dragon Quill?
                        <br>
                        Ez egy olyan portál, ami egyetemi hallgatóknak kínál játékosított
                        tesztírást, annak érdekében, hogy
                        nagyobb motivációt kapjanak a tananyag elsajátításához. Az oldalon fellelhetők különböző
                        jutalmazások (mint trófea-rendszer),
                        vagy esetleg már-már RPG irányba elmenő, avatar-beállítási opció, mellyel picit mindenki
                        személyesebbé teheti profiloldalát.
                        <br><br>
                        Jelentkezz be Neptun kódoddal és -jelszavaddal, böngészd az oldalt, tölts ki az Oktatód által
                        feltöltött teszteket,
                        szerezd meg a legtöbb pontot, az összes trófeát és kapj megajánlott jegyet!
                        <br><br>
                        Amennyiben nem találod azt a kurzust, amire szükséged lenne, böngéssz a lentebb elérhető kari
                        logókra
                        kattintva!
                        <br><br>
                    </p>


                </div>
                <h1>Jó munkát kívánok!</h1>

                <br>

                <h2>Kurzuskategóriák</h2>

                <br>

                <a href="https://elearning.uni-eszterhazy.hu/course/index.php?categoryid=2" target="_blank"><img src="./logo_bmk.png"></img></a>
                <a href="https://elearning.uni-eszterhazy.hu/course/index.php?categoryid=4" target="_blank"><img src="./logo_gtk.png"></img></a>
                <a href="https://elearning.uni-eszterhazy.hu/course/index.php?categoryid=197" target="_blank"><img src="./logo_ik.png"></img></a>
                <a href="https://elearning.uni-eszterhazy.hu/course/index.php?categoryid=5" target="_blank"><img src="./logo_pk.png"></img></a>
                <a href="https://elearning.uni-eszterhazy.hu/course/index.php?categoryid=6" target="_blank"><img src="./logo_ttk.png"></img></a>
                <a href="https://elearning.uni-eszterhazy.hu/course/index.php?categoryid=35" target="_blank"><img src="./logo_ec.png"></img></a>
                <a href="https://elearning.uni-eszterhazy.hu/course/index.php?categoryid=49" target="_blank"><img src="./logo_jc.png"></img></a>

                <br>
                <i>Sárkányos képek forrása: upklyak (Freepik - Free licence)</i>

            </td>
        </tr>
        <tr>
            <td colspan="5" class="footer">
                <a href="https://uni-eszterhazy.hu" target="_blank">Weboldal</a> | <a href="https://www.facebook.com/eszterhazyuniversity/" target="_blank">Facebook</a> | <a href="https://www.instagram.com/unieszterhazy/" target="_blank">Instagram</a>
                <br><br>
                Készítette: Gasparovics Adrienn | BGV8GI | Gazdaságinformatikus BA
            </td>
        </tr>
    </table>

    <div class="area">
        <ul class="circles">
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
        </ul>
    </div>

</body>

♦<style>
    .main-page {
        width: 100%;
        overflow: hidden;
    }

    .main-page img {
        float: right;
        margin-right: 10px;
        size: 100%;
    }
</style>

</html>