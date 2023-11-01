<?php
session_start();

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != "2") {
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

// Kurzus nevének lekérése a GET paraméterből
if (isset($_GET['nev'])) {
    $kurzus_nev = urldecode($_GET['nev']);
} else {
    // Ha nincs megadva kurzus név a GET paraméterként, hibaüzenetet jelenítünk meg
    echo "Hibás URL. Hiányzik a kurzus neve.";
    exit();
}

$hallgatoKod = $_SESSION['username'];

$sql = "SELECT kurzus.kurzusid
        FROM kurzus 
        LEFT JOIN hallgatoKurzusai ON hallgatoKurzusai.kurzusID = kurzus.kurzusid
        WHERE hallgatoKurzusai.HNeptunKod = '$hallgatoKod' AND kurzus.kurzusnev = '$kurzus_nev'";

$result = $conn->query($sql);

if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
    $kurzus_id = $row['kurzusid'];

    // SQL a kurzushoz tartozó hetek és fájlok lekérdezésére
    $sql = "SELECT hetek.het, fajlok.fajlnev, fajlok.fajltipus, fajlok.fajlid
            FROM hetek
            LEFT JOIN fajlok ON hetek.hetid = fajlok.hetid
            WHERE hetek.kurzusid = $kurzus_id";

    $result = $conn->query($sql);
} else {
    // Ha a kurzus nem található az adatbázisban, hibaüzenetet jelenítünk meg
    echo "Hibás URL. A kurzus nem található.";
    exit();
}

// Adatbázis kapcsolat lezárása
$conn->close();
?>

<!DOCTYPE html>
<html lang="hu">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tananyag</title>
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
                    <div class="right-menu"><a href=logout.php>Kijelentkezés</a></div>
                </div>
            </td>
        </tr>
        <tr>
        </tr>
        <tr>
            <td colspan="5" class="content">
                <?php
                // SQL a kurzushoz tartozó hetek és fájlok lekérdezésére
                $sql = "SELECT hetek.het, fajlok.fajlnev, fajlok.fajltipus, fajlok.fajlid, hetek.tesztid
                FROM hetek
                LEFT JOIN fajlok ON hetek.hetid = fajlok.hetid
                LEFT JOIN tesztsor ON hetek.tesztid = tesztsor.tesztID
                WHERE hetek.kurzusid = $kurzus_id";

                // Egy változó a jelenlegi hétre
                $current_week = null;
                ?>
                <a href='kitoltes.php?kurzus_nev=<?php echo urlencode($kurzus_nev); ?>'>
                    <h2>Teszt kitöltése!</h2>
                </a>


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

    h2 {
        text-align: center;
    }
</style>

</html>