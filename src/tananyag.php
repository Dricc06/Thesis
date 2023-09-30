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

// Kurzus nevének lekérése a GET paraméterből
if (isset($_GET['nev'])) {
    $kurzus_nev = urldecode($_GET['nev']);
} else {
    // Ha nincs megadva kurzus név a GET paraméterként, hibaüzenetet jelenítünk meg
    echo "Hibás URL. Hiányzik a kurzus neve.";
    exit();
}

// Oktatóhoz tartozó kurzusok lekérése
$oktatoKod = $_SESSION['username']; // Az oktató neptun kódja

$sql = "SELECT kurzusid FROM kurzus WHERE koktato = '$oktatoKod' AND kurzusnev = '$kurzus_nev'";
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

