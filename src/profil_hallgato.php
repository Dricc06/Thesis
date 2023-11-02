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
    $avatar = $row['avatar'];
    $username = $row['neptun_kod'];
}


// Felhasználó adatainak lekérdezése a userdatas táblából
$sql_userdatas = "SELECT userdatas.neptunKod, userdatas.nev, karok.karNeve, userdatas.szak, userdatas.tagozat 
                 FROM userdatas 
                 LEFT JOIN karok ON userdatas.kar = karok.id
                 WHERE userdatas.neptunKod = '$username'";
$result_userdatas = $conn->query($sql_userdatas);

if ($result_userdatas->num_rows == 1) {
    $row_userdatas = $result_userdatas->fetch_assoc();
    $neptunKod = $row_userdatas['neptunKod'];
    $nev = $row_userdatas['nev'];
    $kar = $row_userdatas['karNeve']; // Itt használjuk a kapcsolt kar nevét
    $szak = $row_userdatas['szak'];
    $tagozat = $row_userdatas['tagozat'];
}


?>

<!DOCTYPE html>
<html lang="hu">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hallgatói profil</title>
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
            display: fixed;
            justify-content: space-between;
            margin: 10px 15%;
            /* Csökkentett margó */
        }

        caption {
            font-weight: bold;
            font-size: 20px;
            text-align: left;
            color: #581845;
            padding: 5px;
        }

        .profav {
            float: right;
            padding-right: 35%;
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
                </div>
            </td>
        </tr>
        <tr>
        </tr>
        <tr>
            <td colspan="3" class="content">

                <h1><?php echo $username ?></h1> <br>
                <div class="profav"><img src="<?php echo $avatar; ?>" alt="Avatar" width="150" height="150"></div>
                <div class="table-container">
                    <!-- Hallgató adatai táblázat -->
                    <table>
                        <caption>Hallgató adatai</caption>
                        <tr>
                            <th>Név: </th>
                            <td><?php echo $nev; ?></td>
                        </tr>
                        <tr>
                            <th>Kar: </th>
                            <td><?php echo $row_userdatas['karNeve']; ?></td>
                        </tr>

                        <tr>
                            <th>Szak: </th>
                            <td><?php echo $szak; ?></td>
                        </tr>
                        <tr>
                            <th>Tagozat: </th>
                            <td><?php echo $tagozat; ?></td>
                        </tr>
                        <tr>
                            <th><a href=avatar_modositasa.php target="_blank">Avatar módosítása!</a></th>
                        </tr>


                    </table>


                    <!-- Trófeák táblázat -->
                    <table>
                        <caption>Trófeák</caption>
                        <tr>
                            <th>Dátum</th>
                            <th>Trófea</th>
                            <th>Leírás</th>
                        </tr>
                        <?php
                        // Vannak trófeái?
                       $checkTrophiesQuery = "SELECT trophies_for_students.trophID, trophies_for_students.idopont, trophies.trname, trophies.trimg, trophies_desc.trdesc
                     FROM trophies_for_students
                     INNER JOIN trophies ON trophies_for_students.trophID = trophies.trID
                     INNER JOIN trophies_desc ON trophies_for_students.trophID = trophies_desc.trophyID
                     WHERE trophies_for_students.nepKOD = '$username'
                    
                    UNION ALL
                    
                    SELECT trophies_for_students_sem.trophID, trophies_for_students_sem.idopont, trophies.trname, trophies.trimg, trophies_desc.trdesc
                    FROM trophies_for_students_sem
                    INNER JOIN trophies ON trophies_for_students_sem.trophID = trophies.trID
                    INNER JOIN trophies_desc ON trophies_for_students_sem.trophID = trophies_desc.trophyID
                    WHERE trophies_for_students_sem.nepKOD = '$username'";

                        $trophiesResult = $conn->query($checkTrophiesQuery);

                        if ($trophiesResult->num_rows > 0) {
                            while ($rowTrophy = $trophiesResult->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $rowTrophy['idopont'] . "</td>";
                                echo '<td><img src="data:image/png;base64,' . base64_encode($rowTrophy['trimg']) . '" width="50" height="50"></td>';

                                echo "<td>" . $rowTrophy['trdesc'] . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            // If there are no trophies, you can display a message or handle it as needed
                            echo "<tr>";
                            echo "<td colspan='3'>Nincsenek trófeáid.</td>";
                            echo "</tr>";
                        }
                        ?>

                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>


                    </table>


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
