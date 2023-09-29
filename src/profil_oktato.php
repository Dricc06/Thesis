<?php
session_start();

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != "1") {
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


// Felhasználó adatainak lekérdezése a userdatasoktato táblából
$sql_userdatasoktato = "SELECT userdatasoktato.neptunKod, userdatasoktato.onev, karok.karNeve, userdatasoktato.oemail, userdatasoktato.ofogado
                 FROM userdatasoktato
                 LEFT JOIN karok ON userdatasoktato.kar = karok.id
                 WHERE userdatasoktato.neptunKod = '$username'";
$result_userdatasoktato = $conn->query($sql_userdatasoktato);

if ($result_userdatasoktato->num_rows == 1) {
    $row_userdatasoktato = $result_userdatasoktato->fetch_assoc();
    $neptunKod = $row_userdatasoktato['neptunKod'];
    $nev = $row_userdatasoktato['onev'];
    $kar = $row_userdatasoktato['karNeve']; // Itt használjuk a kapcsolt kar nevét
    $email= $row_userdatasoktato['oemail'];
    $fogadoora = $row_userdatasoktato['ofogado'];
}


?>
