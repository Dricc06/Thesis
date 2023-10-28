<?php
$servername = "localhost";
$db_username = "Admin";
$db_password = "_K*uqlR2qRzexuzw";
$dbname = "SZD_jatekositas";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

if ($conn->connect_error) {
    die("Sikertelen adatbázis kapcsolódás: " . $conn->connect_error);
}

if (isset($_POST['submit'])) {
    $hetID = $_POST['i_hetID'];
    $kurzusnev = $_POST['i_kurzusNEV'];
    $fajlnev = $_FILES['pdf_file']['name'];
    $fajltipus = 'pdf';
    $fajl = $_FILES['pdf_file']['tmp_name'];

    if (isset($_FILES['pdf_file']['name'])) {

        $sql = "INSERT INTO fajlok (hetid, fajlnev, fajltipus, fajl) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isss", $hetID, $fajlnev, $fajltipus, $fajl);

        if ($stmt->execute()) {
            header("Location: success_file.php");
        } else {
            echo "Hiba a fájl feltöltésekor: " . $stmt->error;
        }
    }
}
