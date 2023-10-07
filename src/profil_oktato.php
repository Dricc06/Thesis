<?php
session_start();

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != "1") {
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
    $avatar = $row['avatar'];
    $username = $row['neptun_kod'];
}


// Felhasználó adatainak lekérdezése a userdatasoktato táblából
$sql_userdatasoktato = "SELECT userdatasoktato.neptunKod, userdatasoktato.onev, karok.karNeve, userdatasoktato.oemail, userdatasoktato.ofogado
                 FROM userdatasoktato
                 LEFT JOIN karok ON userdatasoktato.kar = karok.id
                 WHERE userdatasoktato.neptunKod = '$username'";
$result_userdatasoktato = $conn->query($sql_userdatasoktato);

if ($result_userdatasoktato->num_rows == 1) {
    $row_userdatasoktato = $result_userdatasoktato->fetch_assoc();
    $neptunKod = $row_userdatasoktato['neptunKod'];
    $nev = $row_userdatasoktato['onev'];
    $kar = $row_userdatasoktato['karNeve']; // Itt használjuk a kapcsolt kar nevét
    $email = $row_userdatasoktato['oemail'];
    $fogadoora = $row_userdatasoktato['ofogado'];
}


?>

<!DOCTYPE html>
<html lang="hu">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oktatói profil</title>
    <link href="style.css" rel="stylesheet" />
    <style>
        /* Alap stílusok kifejezetten ezen oldal táblázataihoz */
        table {
            margin: 10px;
        }

        th {
            background-color: #800000;
            color: white;
            padding: 10px;

        }

        /* Táblázatok egymás mellé rendezése */
        .table-container {
            display: flex;
            justify-content: space-between;
            margin: 10px 20%;
            /* Csökkentett margó */
        }

        caption {
            font-weight: bold;
            font-size: 20px;
            text-align: left;
            color: #581845;
            padding: 5px;
        }
    </style>
</head>

<body>

    <table class="main-table">
        <tr>
            <td colspan="5" class="banner">
                <div class="avatar-info">
                    <div class="avatar">
                        <!-- Az avatar kép megjelenítése -->
                        <img src="<?php echo $avatar; ?>" alt="Avatar" width="150" height="150">


                    </div>
                    <div class="user-info">
                        <div class="neptun-kod">
                            <!-- A Neptun kód megjelenítése -->
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
                    <div class="left-menu"><a href=fooldal_oktato.php target="_blank">Főoldal</a></div>
                    <div class="left-menu"><a href=kurzusok.php target="_blank">Kurzusaim</a></div>
                    <div class="right-menu"><a href=logout.php>Kijelentkezés</a></div>
                </div>
                </div>
            </td>
        </tr>
        <tr>
        </tr>
        <tr>
            <td colspan="3" class="content">

                <h1><?php echo $username ?></h1> <br>

                <div class="table-container">
                    <!-- Hallgató adatai táblázat -->
                    <table>
                        <caption>Oktató adatai</caption>
                        <tr>
                            <th>Név: </th>
                            <td><?php echo $nev; ?></td>
                        </tr>
                        <tr>
                            <th>Kar: </th>
                            <td><?php echo $row_userdatasoktato['karNeve']; ?></td>
                        </tr>

                        <tr>
                            <th>E-mail cím: </th>
                            <td><?php echo $email; ?></td>
                        </tr>
                        <tr>
                            <th>Fogadó óra: </th>
                            <td><?php echo $fogadoora; ?></td>
                        </tr>
                        <tr>
                            <th><a href=avatar_modositasa_oktato.php target="_blank">Avatar módosítása!</a></th>
                        </tr>


                    </table>

                    <img src="<?php echo $avatar; ?>" alt="Avatar" width="150" height="150">

                </div>
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

</html>