<!DOCTYPE html>
<html lang="hu">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bejelentkezés</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="login-container">
        <h2>Bejelentkezés</h2>
        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="user-type">Belépés típusa</label>
                <select id="user-type" name="user-type">
                    <option value="2">Hallgató</option>
                    <option value="1">Oktató</option>
                </select>
            </div>
            <div class="form-group">
                <label for="username">Felhasználónév</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Jelszó</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Bejelentkezés</button>


            <?php
            session_start();
            if (isset($_SESSION['error'])) {
                echo "<p style='color: red;'>" . $_SESSION['error'] . "</p>";
                unset($_SESSION['error']);
            }
            ?>


        </form>
    </div>
</body>

</html>


<?php


// Adatbázis kapcsolódás
$servername = "localhost";
$username = "Admin";
$password = "_K*uqlR2qRzexuzw";
$dbname = "SZD_jatekositas";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Hiba a kapcsolódás során: " . $conn->connect_error);
}

// A formból érkező adatok feldolgozása
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userType = $_POST["user-type"];
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Ellenőrzés az adatbázisban
    $sql = "SELECT * FROM users WHERE neptun_kod = '$username' AND jelszo = '$password' AND user_type = '$userType'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        // Sikeres belépés
        session_start();
        $_SESSION["username"] = $username;
        $_SESSION["user_type"] = $userType;

        // Átirányítás a megfelelő céloldalra
        if ($userType == "2") {
            header("Location: fooldal_hallgato.php");
        } elseif ($userType == "1") {
            header("Location: fooldal_oktato.php");
        }

        exit();
    } else {
        // Belépés sikertelen
        $_SESSION['error'] = "Hibás felhasználónév, jelszó vagy belépési típus.";
        header("Location: login.php"); // Átirányítás a bejelentkező oldalra
        exit();
    }
}

$conn->close();
?>

<style>
    html,
    body {
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        background-image: url("bg_login.jpg");
        background-size: cover;
        /* A háttér méretét illeszti az ablakhoz */
        background-attachment: fixed;
        /* A háttér fixen marad az ablakban */
    }

    .login-container {
        background-color: rgba(255, 255, 255, 0.9);
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
        width: 400px;
        text-align: center;
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
        color: #800000;
    }

    input[type="text"],
    input[type="password"],
    select {
        width: 80%;
        padding: 10px;
        border: 1px solid #581845;
        border-radius: 5px;
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
</style>

<link href="styles.css" rel="stylesheet" />