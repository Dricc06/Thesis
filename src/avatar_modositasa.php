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

<!DOCTYPE html>
<html lang="hu">
<head>
    <!--- Az itt felhasznált avatarképek a https://usersinsights.com/user-avatar-icons/ oldalról származnak, 
            Creative Commons licensz alatt állnak. (https://creativecommons.org/licenses/by/3.0/) -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avatar módosítása</title>
    <link rel="stylesheet" href="style.css">

</head>
<body>
    <div class="container">
        <h2>Avatar módosítása</h2>
        <form action="avatar_modositasa.php" method="POST">
            <div class="gallery">
                <?php
                // Avatarképek listázása
                $avatarFolder = "avatars/"; // Az avatarképek mappája
                $avatarFiles = scandir($avatarFolder); // Mappában lévő fájlok visszaadása

                foreach ($avatarFiles as $file) { // Végigmegyünk a fájlokon
                    if ($file != "." && $file != "..") {
                        echo "<img class='gallery-item' src='$avatarFolder$file' alt='$file' onclick=\"selectAvatar('$file')\">"; // kattinthatóvá tesszük a képeket
                    }
                }
                ?>
            </div>
            <br>  <br> <br>
            <input type="hidden" id="selectedAvatar" name="avatar" value="">
            <button type="submit">Mentés</button>
        </form>
    </div>

    <script>
        // Kiválasztott avatarkép tárolása - JavaScript
        function selectAvatar(avatarName) {
            document.getElementById('selectedAvatar').value = avatarName;
        }

    </script>
</body>
</html>
