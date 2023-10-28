<?php
session_start();

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != "1") {
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
            header("Location: profil_oktato.php");
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
        <form action="avatar_modositasa_oktato.php" method="POST">
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


<style>
html, body {
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    background: radial-gradient(circle at bottom, #FF5733 , #581845);
    background-size: cover;
    background-attachment: fixed; /* A háttér fixen marad az ablakban */
}

.container {
    background-color: rgba(255, 255, 255, 0.5);
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
    width: 50%;
}

h2 {
    text-align: center;
    color: #C70039;
}

.form-group {
    margin-bottom: 20px;
}

label {
    display: block;
    font-weight: bold;
    margin-bottom: 5px;
    color: #900C3F;
}

button {
    display: block;
    width: 100%;
    padding: 10px;
    background-color: #FF5733;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

button:hover {
    background-color: #C70039;
}

.gallery {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

.gallery-item {
            width: 100px;
            height: 100px;
            cursor: pointer;
            transition: transform 0.2s;
        }

.gallery-item:hover {
            transform: scale(1.1);
        }

</style>

<link href="styles.css" rel="stylesheet" />