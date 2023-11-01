<?php
session_start();

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != "1") {
    // Ellenőrizze, hogy a felhasználó be van-e jelentkezve és oktató-e
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

// Elérhető hetek lekérése az adatbázisból
$sql = "SELECT hetID, het FROM hetek"; // Hetek lekérése
$result = $conn->query($sql);

$hetek = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $hetek[$row['hetID']] = $row['het'];
    }
}

$urlap_adatok = [
    //'tesztID' => '',
    'kurzusNEV' => '',
    'hetID' => '',
];

// Trófeák
$sql = "SELECT nepKOD FROM trophies_for_students WHERE nepKOD = '$username'";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $nepKOD = $row['nepKOD'];
    }
}


?>

<!DOCTYPE html>
<html lang="hu">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trófeás hallgatók</title>
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
                            <a href="profil_oktato.php">Profilom</a>
                        </div>
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="5" class="menu">
                <div class="nav-menu">
                    <div class="left-menu"><a href=fooldal_oktato.php target="_self">Főoldal</a></div>
                    <div class="left-menu"><a href=kezelo.php target="_self">Oktatói kezelőfelület</a></div>
                    <div class="right-menu"><a href=logout.php>Kijelentkezés</a></div>
                </div>
            </td>
        </tr>
        <tr>
        </tr>
        <tr>
            <td colspan="5" class="content">
                <h1>Trófeával rendelkező hallgatók listája</h1>

                <?php

                $sql = "SELECT ts.nepKOD, ts.idopont, trophies.trname
                        FROM trophies_for_students ts
                        INNER JOIN trophies ON ts.trophID = trophies.trid
                        UNION
                        SELECT tss.nepKOD, tss.idopont, trophies.trname
                        FROM trophies_for_students_sem tss
                        INNER JOIN trophies ON tss.trophID = trophies.trid;";

                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    echo "<table class='list'>";
                    echo "<tr><th>Neptun kód</th><th>Időpont</th><th>Trófea neve</th></tr>";

                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['nepKOD'] . "</td>";
                        echo "<td>" . $row['idopont'] . "</td>";
                        echo "<td>" . $row['trname'] . "</td>";
                        echo "</tr>";
                    }

                    echo "</table>";
                } else {
                    echo "Nincs találat az adatbázisban.";
                }

                echo '<form method="post" action="export.php">
                                <input type="submit" name="export" value="Trófeás hallgatók letöltése CSV-ben">
                            </form>';

                // Kapcsolat lezárása
                $conn->close();
                ?>

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
    .content {
        background-image: url('./listBG.jpg');
        background-position: center;
        background-repeat: no-repeat;
        background-attachment: fixed;
        background-size: cover;
    }

    h1 {
        color: white;
    }

    ul {
        list-style-type: none;
        text-align: left;
    }

    .list {
        width: 80%;
        border-collapse: collapse;
        overflow: hidden;
        box-shadow: 0 0 20px rgba(144, 12, 63, 0.1);
        margin: 0 auto;

    }

    .list td {
        padding: 15px;
        background-color: rgba(255, 255, 255, 0.6);
        color: #000080;
        border-bottom: 1px solid white;
    }

    .list th {
        padding: 15px;
        border-bottom: 1px solid white;
        color: #fff;
        background-color: rgba(144, 12, 63, 0.8);
    }
</style>

</html>