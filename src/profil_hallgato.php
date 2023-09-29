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
$username= $_SESSION['username'];
$sql = "SELECT avatar, neptun_kod FROM users WHERE neptun_kod = '$username'";
$result = $conn->query($sql);

if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
    $avatar = $row['avatar']; 
    $username = $row['neptun_kod']; 
}


// Felhasználó adatainak lekérdezése a userdatas táblából
$sql_userdatas = "SELECT userdatas.neptunKod, userdatas.nev, karok.karNeve, userdatas.szak, userdatas.tagozat 
                 FROM userdatas 
                 LEFT JOIN karok ON userdatas.kar = karok.id
                 WHERE userdatas.neptunKod = '$username'";
$result_userdatas = $conn->query($sql_userdatas);

if ($result_userdatas->num_rows == 1) {
    $row_userdatas = $result_userdatas->fetch_assoc();
    $neptunKod = $row_userdatas['neptunKod'];
    $nev = $row_userdatas['nev'];
    $kar = $row_userdatas['karNeve']; // Itt használjuk a kapcsolt kar nevét
    $szak = $row_userdatas['szak'];
    $tagozat = $row_userdatas['tagozat'];
}

?>
