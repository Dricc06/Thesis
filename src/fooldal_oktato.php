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
                <h1>Köszöntjük az E-Learning Portálon!</h1>
                <p>Ön az Egyetem elearning portáljának címoldalán áll. A portál jelenleg hozzávetően 900, különböző képzéshez, illetve projekthez kapcsolódó kurzusnak ad otthont.
                    Keressen, tallózzon szabadon a szervezeti egységenként csoportosított kurzusok között, vagy jelentkezzen be azok használatához!
                    Az első bejelentkezés módjáról <a href="https://elearning.uni-eszterhazy.hu/mod/page/view.php?id=82" target="_blank">itt olvashat.</a></p>
                <p>A különböző kurzusokon belül az oktatók által összeállított, játékosított teszteket is találhat, melyeket kitöltve nem csak félév végi
                    jegyet kaphat a hallgató, hanem különféle címeket, trófeákat szerezhet meg, melyek motivációt adnak a további eredményes teljesítéshez.
                </p>
                <h1>Jó munkát kívánunk!</h1>

                <br>

                <h2>Kurzuskategóriák</h2>

                <br><br>

                <p>

                    <a href="https://elearning.uni-eszterhazy.hu/course/index.php?categoryid=2" target="_blank"><img src="./logo_bmk.png"></img></a>
                    <a href="https://elearning.uni-eszterhazy.hu/course/index.php?categoryid=4" target="_blank"><img src="./logo_gtk.png"></img></a>
                    <a href="https://elearning.uni-eszterhazy.hu/course/index.php?categoryid=197" target="_blank"><img src="./logo_ik.png"></img></a>
                    <a href="https://elearning.uni-eszterhazy.hu/course/index.php?categoryid=5" target="_blank"><img src="./logo_pk.png"></img></a>
                    <a href="https://elearning.uni-eszterhazy.hu/course/index.php?categoryid=6" target="_blank"><img src="./logo_ttk.png"></img></a>
                    <a href="https://elearning.uni-eszterhazy.hu/course/index.php?categoryid=35" target="_blank"><img src="./logo_ec.png"></img></a>
                    <a href="https://elearning.uni-eszterhazy.hu/course/index.php?categoryid=49" target="_blank"><img src="./logo_jc.png"></img></a>

                </p>

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


</html>