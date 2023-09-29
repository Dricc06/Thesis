<?php
session_start();

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != "2") {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_neptun = $_SESSION['username'];
    $selectedAvatar = $_POST["avatar"];
    
    // Létezik egyáltalán a kiválasztott kép?
    $avatarFolder = "avatars/";
    if (file_exists($avatarFolder . $selectedAvatar)) {
        // Elérési útvonal
        $avatarPath = $avatarFolder . $selectedAvatar;
        
        // Adatbázis kapcsolat
        $servername = "localhost";
        $db_username = "Admin";
        $db_password = "_K*uqlR2qRzexuzw";
        $dbname = "SZD_jatekositas";
        
        $conn = new mysqli($servername, $db_username, $db_password, $dbname);
        if ($conn->connect_error) {
            die("Kapcsolódási hiba: " . $conn->connect_error);
        }
        
        $sql = "UPDATE users SET avatar = '$avatarPath' WHERE neptun_kod = '$user_neptun'"; // Kép módosítása
        
        if ($conn->query($sql) === TRUE) {
            header("Location: profil_hallgato.php");
            exit();
        } else {
            echo "Hiba történt az adatbázis frissítése során: " . $conn->error;
        }
        
        $conn->close();
    } else {
        echo "Hiba: A kiválasztott avatar nem található!";
    }
}
?>
