<?php
session_start();

// Ellenőrizze, hogy a felhasználó be van-e jelentkezve, és van-e megfelelő jogosultsága
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== "2") {
    header("Location: login.php");
    exit();
}

// Adatbázis kapcsolat
$servername = "localhost";
$username = "Admin";
$password = "_K*uqlR2qRzexuzw";
$dbname = "SZD_jatekositas";

// Új adatbázis kapcsolat létrehozása az objektumorientált stílusban
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
    $avatar = $row['avatar'];
    $username = $row['neptun_kod'];
}

$hallgatoKod = $_SESSION['username'];


if (isset($_POST['kurzus_nev'])) {
    $kurzus_nev = urldecode($_POST['kurzus_nev']);
}


// Lekérdezés a kurzushoz tartozó hetekről
$sqlHetek = "SELECT DISTINCT hetek.hetid, hetek.het 
            FROM hetek 
            LEFT JOIN tesztsor ON hetek.hetid = tesztsor.hetID
            WHERE tesztsor.kurzusNEV = '$kurzus_nev'";
$resultHetek = $conn->query($sqlHetek);

// Egy üres tömb létrehozása a heteknek
$hetek = array();

if ($resultHetek->num_rows > 0) {
    while ($rowHetek = $resultHetek->fetch_assoc()) {
        $hetek[] = $rowHetek;
    }
}

// Ellenőrizze, hogy a form elküldése megtörtént-e
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $kurzus_nev = $_POST["kurzus_nev"];
    $selectedWeek = $_POST["selectedWeek"];

    $sqlKerdesek = "SELECT * FROM tesztsor WHERE kurzusNEV = '$kurzus_nev' AND hetID = '$selectedWeek'";
    $resultKerdesek = $conn->query($sqlKerdesek);

    if ($resultKerdesek->num_rows > 0) {
        $eredmenyek = array();
        $pontszam = 0; // 0-ról indulunk

        while ($rowKerdes = $resultKerdesek->fetch_assoc()) {
            $tesztID = $rowKerdes['tesztID'];

            // Átvesszük az adatokat
            $valasz = $_POST['valaszom'][$tesztID];
            $extra = $_POST['extra'][$tesztID];
            $helyesValasz = $rowKerdes['helyesValasz'];

            // Pontszámítás
            $questionScore = 0;
            if ($valasz === $helyesValasz) {
                switch ($extra) {
                    case 'basegame':
                        $questionScore = 2;
                        break;
                    case 'sure':
                        $questionScore = 3;
                        break;
                    case 'ultra':
                        $questionScore = 6;
                        break;
                }
            } else {
                switch ($extra) {
                    case 'basegame':
                        $questionScore = 0;
                        break;
                    case 'sure':
                        $questionScore = -1;
                        break;
                    case 'ultra':
                        $questionScore = -6;
                        break;
                }
            }

            // Véges pontszám frissítése
            $pontszam += $questionScore;

            $eredmenyek[] = array(
                'tesztID' => $tesztID,
                'pontszam' => $pontszam
            );
        }

        // Elmentjük az eredményt
        foreach ($eredmenyek as $eredmeny) {
            $tesztID = $eredmeny['tesztID'];
            $pontszam = $eredmeny['pontszam'];
        }

        // Eredmények táblába mentés
        $sqlEredmenyMentes = "INSERT INTO eredmenyek (het_ID, kurzus_NEV, neptun_KOD, eredmeny_PONT) VALUES ('$selectedWeek', '$kurzus_nev', '$hallgatoKod', '$pontszam')";
        $conn->query($sqlEredmenyMentes);

        header("Location: sikeres.php");
        exit();
    }
}
