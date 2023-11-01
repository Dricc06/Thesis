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

if (isset($_GET['kurzus_nev'])) {
    $kurzus_nev = urldecode($_GET['kurzus_nev']);
} else {
    echo "Hibás URL. Hiányzik a kurzus neve.";
    exit();
}


$sqlHetek = "SELECT DISTINCT hetek.hetid, hetek.het 
            FROM hetek 
            LEFT JOIN tesztsor ON hetek.hetid = tesztsor.hetID
            WHERE tesztsor.kurzusNEV = '$kurzus_nev'
            ORDER BY hetek.het ASC";
$resultHetek = $conn->query($sqlHetek);


// Egy üres tömb létrehozása a heteknek
$hetek = array();

if ($resultHetek->num_rows > 0) {
    while ($rowHetek = $resultHetek->fetch_assoc()) {
        $hetek[] = $rowHetek;
    }
}

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
                    <div class=" right-menu"><a href=logout.php>Kijelentkezés</a></div>
                </div>
            </td>
        </tr>
        <tr>
        </tr>
        <tr>
            <td colspan="5" class="content">

                <h1>Üdvözöllek a <i><?php echo $kurzus_nev ?></i> nevű kurzus tesztíró felületén!</h1>
                <br><br>

                <form action="" method="post">
                    <label for="selectedWeek">Válassz egy hetet:</label>
                    <select name="selectedWeek" id="selectedWeek">
                        <?php foreach ($hetek as $het) : ?>
                            <option value="<?php echo $het['hetid']; ?>"><?php echo $het['het']; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input class="gomb" type="submit" name="next" value="Tovább">
                </form>

                <?php
                if (isset($_POST['next'])) {
                    $selectedWeek = $_POST['selectedWeek'];

                    // Küldött már be a hallgató választ az aktuális héthez?
                    $checkSubmissionSQL = "SELECT COUNT(*) as count FROM eredmenyek WHERE het_ID = '$selectedWeek' AND neptun_KOD = '$hallgatoKod' AND kurzus_NEV ='$kurzus_nev'";
                    $checkResult = $conn->query($checkSubmissionSQL);
                    $submissionCount = $checkResult->fetch_assoc()['count'];

                    if ($submissionCount > 0) {
                        echo "Ehhez a héthez már küldtél be választ.";
                    } else {

                        // Most a kurzus nevét használom, amit az URL paraméterből olvastam ki
                        $sqlKerdesek = "SELECT * FROM tesztsor WHERE kurzusNEV = '$kurzus_nev' AND hetID = '$selectedWeek'";
                        $resultKerdesek = $conn->query($sqlKerdesek);

                        if ($resultKerdesek->num_rows > 0) {
                            echo "<h2>Kérdések a kiválasztott héthez:</h2>";
                            echo "<form action='pontozas.php' method='post'>";
                            echo "<input type='hidden' name='kurzus_nev' value='" . $kurzus_nev . "'>";
                            echo "<input type='hidden' name='selectedWeek' value='" . $selectedWeek . "'>";

                            echo "<table class='testTable'>";
                            echo "<tr>
                                <th>Kérdés</th>
                                <th>A válasz</th>
                                <th>B válasz</th>
                                <th>C válasz</th>
                                <th>D válasz</th>
                                <th>E válasz</th>
                                <th>F válasz</th>
                                <th>Válaszom:</th>
                                <th>Extra:</th>
                            </tr>";

                            while ($rowKerdes = $resultKerdesek->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $rowKerdes['kerdes'] . "</td>";
                                echo "<td>" . $rowKerdes['a'] . "</td>";
                                echo "<td>" . $rowKerdes['b'] . "</td>";
                                echo "<td>" . $rowKerdes['c'] . "</td>";
                                echo "<td>" . $rowKerdes['d'] . "</td>";
                                echo "<td>" . $rowKerdes['e'] . "</td>";
                                echo "<td>" . $rowKerdes['f'] . "</td>";

                                echo "<td>
                                <select name='valaszom[" . $rowKerdes['tesztID'] . "]' id='valaszom" . $rowKerdes['tesztID'] . "'>
                                    <option value='a'>A</option>
                                    <option value='b'>B</option>
                                    <option value='c'>C</option>
                                    <option value='d'>D</option>
                                    <option value='e'>E</option>
                                    <option value='f'>F</option>
                                </select>
                            </td>";

                                echo "<td>
                                <select name='extra[" . $rowKerdes['tesztID'] . "]' id='extra" . $rowKerdes['tesztID'] . "'>
                                    <option value='basegame'>Alapjáték (+2/0)</option>
                                    <option value='sure'>Biztos (+3/-1)</option>
                                    <option value='ultra'>Ultra (+6/-6)</option>
                                </select>
                            </td>";

                                echo "</tr>";
                            }
                            echo "</table>";
                            echo "<br>";
                            echo "<input class='gomb' type='submit' name='submit' value='Küldés'>";
                            echo "</form>";
                        } else {
                            echo "Nincs találat az adatbázisban a kiválasztott kurzusra és hétre.";
                        }
                    }
                }

                // Adatbázis kapcsolat lezárása
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
    ul {
        list-style-type: none;
        text-align: left;
    }

    table {
        width: 100%;
        text-align: center;
    }

    .testTable {
        border: 1px solid black;

        border-collapse: collapse;
    }

    .testTable th {
        padding: 10px;
        border-bottom: 2px dotted maroon;
        border-left: 1px solid maroon;
        background-color: #f25c54;
        color: #ffcdb2;
    }


    .testTable td {
        padding: 10px;
        border: 1px solid maroon;
    }

    h3 {
        color: darkgreen;
        font-size: x-large;
    }

    .gomb {
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

    .gomb:hover {
        background-color: #800000;
        border-bottom: #FF5733 5px solid;
        border-right: #FF5733 5px solid;
    }
</style>

</html>