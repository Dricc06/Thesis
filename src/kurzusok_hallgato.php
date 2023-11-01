<?php
session_start();

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != "2") {
    // Ellenőrizze, hogy a felhasználó be van-e jelentkezve és hallgató-e
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

// Hallgatóhoz tartozó kurzusok lekérése
$hallgatoKod = $_SESSION['username'];

$sql = "SELECT kurzus.kurzusnev FROM kurzus 
        LEFT JOIN hallgatoKurzusai ON hallgatoKurzusai.kurzusID = kurzus.kurzusid
        WHERE hallgatoKurzusai.HNeptunKod = '$hallgatoKod'";
$result = $conn->query($sql);

$hallgatoKurzusok = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $hallgatoKurzusok[] = $row['kurzusnev'];
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="hu">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kurzusaim</title>
    <link href="style.css" rel="stylesheet" />
</head>

<body>

    <table class="main-table">
        <tr>
            <td colspan="5" class="banner">
                <div class="avatar-info">
                    <div class="avatar">
                        <img src="<?php echo $avatar; ?>" alt="Avatar" width="150" height="150">
                    </div>
                    <div class="user-info">
                        <div class="neptun-kod">
                            Neptun kód: <?php echo $username; ?>
                        </div>
                        <div class="profile-link">
                            <a href="profil_hallgato.php">Profilom</a>
                        </div>
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="5" class="menu">
                <div class="nav-menu">
                    <div class="left-menu"><a href=fooldal_hallgato.php target="_self">Főoldal</a></div>
                    <div class="left-menu"><a href=kurzusok_hallgato.php target="_self">Tesztek kitöltése</a></div>
                    <div class=" right-menu"><a href=logout.php>Kijelentkezés</a></div>
                </div>
            </td>
        </tr>
        <tr>

        </tr>
        <tr>
            <td colspan="5" class="content">
                <h1>Leírás</h1>
                Minden héthez az Oktató bizonyos számú kérdésből álló tesztsort készít, amit a Hallgatónak ki kell töltenie.<br>
                Egy kérdéshez hat válaszlehetőség tartozik, ezek közül kell minden esetben egyet választani. <br>
                Alapjáték esetén, ha a Hallgató helyesen válaszolt a feltett kérdésre, úgy 2 pontot kap, a helytelen válasz pedig 0 pontot ér.<br>
                Amennyiben a Hallgató biztos a tudásában és érez magában kockázatvállalási hajlamot, úgy a pontjait felteheti tétként:
                <li>"Biztos" lehetőség jelölése esetén a helyes válasz 3 pontot ér, rossz válasz esetén viszont levonásra kerül 1 pont</li>
                <li>"Ultra" lehetőség jelölése esetén a helyes válasz 6 pontot ér, rossz válasz esetén viszont levonásra kerül 6 pont</li>
                Minden hét, illetve a szemeszter végén az Oktató trófeákat oszt ki a kurzus Hallgatói között, így érdemes minél jobban felkészülni ;)
                <h3>Kezdődjön a játék!</h3>
                <br>
                <h1>Kurzusaim</h1>
                <ul>
                    <?php foreach ($hallgatoKurzusok as $kurzus) : ?>
                        <li>
                            <a href="kitoltes.php?kurzus_nev=<?= urlencode($kurzus) ?>">
                                <?= $kurzus ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>

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

<style>
    ul {
        list-style-type: none;
        text-align: left;
    }
</style>

</html>