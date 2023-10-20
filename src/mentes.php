<?php
session_start();

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != "1") {
    header("Location: login.php");
    exit();
}

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

// Oktatóhoz tartozó kurzusok lekérése
$oktatoKod = $_SESSION['username']; // Az oktató neptun kódja

$sql = "SELECT kurzusnev FROM kurzus WHERE koktato = '$oktatoKod'";
$result = $conn->query($sql);

$oktatoKurzusok = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $oktatoKurzusok[] = $row['kurzusnev'];
    }
}

// Kurzus nevek lekérése az adatbázisból
$sql = "SELECT kurzusNEV FROM tesztsor";
$result = $conn->query($sql);

if (!$result) {
    die("Adatbázis hiba: " . $conn->error);
}

$nevek = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $nevek[] = $row['kurzusNEV'];
    }
}

// Elérhető hetek lekérése az adatbázisból
$sql = "SELECT hetID, het FROM hetek"; // Hetek lekérése
$result = $conn->query($sql);

$hetek = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $hetek[$row['hetID']] = $row['het'];
    }
}

// Űrlap adatok inicializálása és hibák tömbje
$urlap_adatok = [
    //'tesztID' => '',
    'kurzusNEV' => '',
    'hetID' => '',
    'kerdes' => '',
    'a' => '',
    'b' => '',
    'c' => '',
    'd' => '',
    'e' => '',
    'f' => '',
    'helyesValasz' => '',
];

$hibak = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Form adatok ellenőrzése és beállítása

    // Kurzus ID ellenőrzése
    if (!isset($_POST['i_kurzusNEV'])) {
        $hibak[] = 'A kurzus kiválasztása kötelező!';
    } else {
        $urlap_adatok['kurzusNEV'] = $_POST['i_kurzusNEV'];
    }

    // Het ID ellenőrzése
    if (!isset($_POST['i_hetID'])) {
        $hibak[] = 'A hét kiválasztása kötelező!';
    } else {
        $urlap_adatok['hetID'] = $_POST['i_hetID'];
    }

    // Kérdés ellenőrzése
    if (!isset($_POST['i_kerdes'])) {
        $hibak[] = 'A kérdés megadása kötelező!';
    } else {
        $urlap_adatok['kerdes'] = $_POST['i_kerdes'];
    }

    // Válaszok ellenőrzése
    $valaszok = ['a', 'b', 'c', 'd', 'e', 'f'];
    foreach ($valaszok as $valasz) {
        $mezőnév = 'i_' . $valasz;
        if (!isset($_POST[$mezőnév])) {
            $hibak[] = "A(z) '$valasz' válaszlehetőség megadása kötelező!";
        } else {
            $urlap_adatok[$valasz] = $_POST[$mezőnév];
        }
    }

    // Helyes válasz ellenőrzése
    if (!isset($_POST['i_helyesValasz'])) {
        $hibak[] = 'A helyes válaszlehetőség megadása kötelező!';
    } else {
        $urlap_adatok['helyesValasz'] = $_POST['i_helyesValasz'];
    }

    // Kérdés hossz ellenőrzése
    if (strlen($_POST['i_kerdes']) < 5) {
        $hibak[] = 'A kérdés legalább 5 karakter kell, hogy legyen!';
    }

    // DEBUG: Hibák kiírása
    var_dump($hibak);

    // Ha nincsenek hibák, akkor adatok beszúrása az adatbázisba
    if (empty($hibak)) {
        try {
            $insert_query_test = 'INSERT INTO tesztsor (kurzusNEV, hetID, kerdes, a, b, c, d, e, f, helyesValasz, nehez)
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
            $stmt_test = $conn->prepare($insert_query_test);

            $nehez = ($_POST['i_nehez'] == 'igen') ? 1 : 0;

            $stmt_test->bind_param("sssssssssss", $urlap_adatok['kurzusNEV'], $urlap_adatok['hetID'], $urlap_adatok['kerdes'], $urlap_adatok['a'], $urlap_adatok['b'], $urlap_adatok['c'], $urlap_adatok['d'], $urlap_adatok['e'], $urlap_adatok['f'], $urlap_adatok['helyesValasz'], $nehez);
            $stmt_test->execute();

            header('Location: kurzusok.php');
            exit;
        } catch (Exception $e) {
            die("Adatbázis hiba: " . $e->getMessage());
        }
    }
}

$conn->close();
