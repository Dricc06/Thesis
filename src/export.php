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

$sql = "SELECT ts.nepKOD, ts.idopont, trophies.trname
                        FROM trophies_for_students ts
                        INNER JOIN trophies ON ts.trophID = trophies.trid
                        UNION
                        SELECT tss.nepKOD, tss.idopont, trophies.trname
                        FROM trophies_for_students_sem tss
                        INNER JOIN trophies ON tss.trophID = trophies.trid;";

$result = $conn->query($sql);


$filename = "trofeasHallgatok.csv";
$file = fopen($filename, "w");
if ($file) {

    $header = array("Neptun kód", "Időpont", "Trófea neve");
    fputcsv($file, $header);

    while ($row = $result->fetch_assoc()) {
        $data = array(
            $row['nepKOD'],
            $row['idopont'],
            $row['trname']
        );
        fputcsv($file, $data);
    }

    fclose($file);

    header('Content-Type: text/csv; charset=UTF-8');
    header("Content-Disposition: attachment; filename=$filename");
    readfile($filename);
    exit;
} else {
    echo "Hiba a .csv fájl létrehozása során.";
}

$conn->close();


$conn->close();
