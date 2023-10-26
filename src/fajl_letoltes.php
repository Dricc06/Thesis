<?php
session_start();

if (isset($_GET['fajl_id'])) {
    $fajl_id = $_GET['fajl_id'];

    // Ellenőrizze, hogy a fájl létezik-e a fajlok táblában
    $servername = "localhost";
    $db_username = "Admin";
    $db_password = "_K*uqlR2qRzexuzw";
    $dbname = "SZD_jatekositas";

    $conn = new mysqli($servername, $db_username, $db_password, $dbname);

    if ($conn->connect_error) {
        die("Kapcsolódási hiba: " . $conn->connect_error);
    }

    $sql = "SELECT fajlnev, fajltipus, fajl FROM fajlok WHERE fajlid = $fajl_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $file_name = $row['fajlnev'];
        $file_type = "application/pdf";
        $file_content = $row['fajl'];

        // Beállítjuk a HTTP fejlécet a fájl típusához és a megfelelő méretéhez
        header("Content-Type: $file_type");
        header("Content-Disposition: inline; filename=\"$file_name\"");
        header("Content-Length: " . strlen($file_content));

        // Kiírjuk a fájl tartalmát a böngészőbe
        echo $file_content;

        // Kilépés a fájl megnyitása után
        exit;
    } else {
        echo 'A fájl nem található.';
    }

    $conn->close();
} else {
    echo 'Érvénytelen kérés.';
}
?>
