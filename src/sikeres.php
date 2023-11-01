<?php
session_start();

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != "2") {
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
$username = $_SESSION['username'];
$sql = "SELECT avatar, neptun_kod FROM users WHERE neptun_kod = '$username'";
$result = $conn->query($sql);

if ($result->num_rows == 1) {
    $row = $result->fetch_assoc();
    $avatar = $row['avatar']; // Avatar elérési útvonala az adatbázisból
    $username = $row['neptun_kod']; // Neptun kód az adatbázisból
}

$hallgatoKod = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="hu">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teszt kitöltése</title>
    <link href="style.css" rel="stylesheet" />
</head>

<body>

    <table class="main-table">
        <tr>
            <td colspan="5" class="banner">
                <div class="avatar-info">
                    <div class="avatar">
                        <img src="<?php echo $avatar; ?>" alt="Avatar" width="150" height="150">
                    </div>
                    <div class="user-info">
                        <div class="neptun-kod">
                            Neptun kód: <?php echo $username; ?>
                        </div>
                        <div class="profile-link">
                            <a href="profil_hallgato.php">Profilom</a>
                        </div>
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="5" class="menu">
                <div class="nav-menu">
                    <div class="left-menu"><a href=fooldal_hallgato.php target="_self">Főoldal</a></div>
                    <div class="left-menu"><a href=kurzusok_hallgato.php target="_self">Tesztek kitöltése</a></div>
                    <div class="right-menu"><a href=logout.php>Kijelentkezés</a></div>
                </div>
            </td>
        </tr>
        <tr>
        </tr>
        <tr>
            <td colspan="5" class="content">
                <h3>A tesztsor sikeresen beküldésre került!</h3>
                <br>
                <a href="kurzusok_hallgato.php" class="vissza-link">Vissza!</a>
                <br><br>
            </td>

        </tr>
        <tr>
            <td colspan="5" class="footer">
                <a href="https://uni-eszterhazy.hu" target="_blank">Weboldal</a> | <a href="https://www.facebook.com/eszterhazyuniversity/" target="_blank">Facebook</a> | <a href="https://www.instagram.com/unieszterhazy/" target="_blank">Instagram</a>
                <br><br>
                Készítette: Gasparovics Adrienn | BGV8GI | Gazdaságinformatikus BA
            </td>
        </tr>
    </table>

    <div class="area">
        <ul class="circles">
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
        </ul>
    </div>

</body>

<style>
    h3 {
        color: darkgreen;
        font-size: x-large;
    }

    .vissza-link {
        background-color: #FF5733;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 15px;
        border-bottom: #800000 5px solid;
        border-right: #800000 5px solid;
        cursor: pointer;
        font-weight: bold;
        margin-right: 10px;
        transition: background-color 0.3s;
        margin-bottom: 20px;
    }

    .vissza-link:hover {
        background-color: #800000;
        border-bottom: #FF5733 5px solid;
        border-right: #FF5733 5px solid;
    }
</style>

</html>