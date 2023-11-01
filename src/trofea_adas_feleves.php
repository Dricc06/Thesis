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
    <title>Féléves trófeák kiosztása</title>
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
                <h1>Féléves trófeák kiosztása</h1>

                <form action="" method="post">
                    <!-- Legördülő lista: Kurzus neve -->
                    <label for="i_kurzusNEV">Kurzus kiválasztása:</label>
                    <select name="i_kurzusNEV" id="i_kurzusNEV">
                        <?php
                        $lathatoKurzusok = array(); // Tömb az eddig látott kurzusnevek tárolásához

                        foreach ($oktatoKurzusok as $kurzus) {
                            $selected = ($kurzus == $selected_course) ? "selected" : "";
                            echo "<option value='" . htmlspecialchars($kurzus) . "' $selected>" . htmlspecialchars($kurzus) . "</option>";
                        }
                        ?>
                    </select><br>
                    <br>
                    <input type="submit" name="submit" value="Eredmény megjelenítése">
                </form>
                <br><br>
                <?php
                if (isset($_POST['submit'])) {
                    $kurzus = $_POST['i_kurzusNEV'];

                    $sql = "SELECT neptun_KOD, SUM(eredmeny_PONT) AS total_points
                            FROM eredmenyek
                            WHERE kurzus_NEV = '$kurzus'
                            GROUP BY neptun_KOD
                            ORDER BY total_points DESC";

                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        echo "<table class='points'>";
                        echo "<tr><th>Hallgató Neptun kódja</th><th>Összpontszám</th><th>Megjutalmazás</th></tr>";

                        $topStudents = array();
                        $highestScore = 0;

                        while ($row = $result->fetch_assoc()) {
                            if ($row['total_points'] >= $highestScore) {
                                $highestScore = $row['total_points'];
                                $topStudents[] = $row;
                            } else {
                                break; // Kilép a ciklusból, ha már nincs holtverseny
                            }
                        }

                        foreach ($topStudents as $student) {
                            echo "<tr>";
                            echo "<td>" . $student['neptun_KOD'] . "</td>";
                            echo "<td>" . $student['total_points'] . "</td>";
                            echo "<td><form method='post' action=''>";
                            echo "<input type='hidden' name='neptun_KOD' value='" . $student['neptun_KOD'] . "'>";
                            echo "<input type='submit' name='megjutalmaz' value='Megjutalmaz'>";
                            echo "</form></td>";
                            echo "</tr>";
                        }

                        echo "</table>";
                    } else {
                        echo "Nincsenek eredmények a kiválasztott kurzusban.";
                    }
                }



                if (isset($_POST['megjutalmaz'])) {
                    $nepKOD = $_POST['neptun_KOD'];

                    $checkSql = "SELECT * FROM trophies_for_students_sem WHERE nepKOD = ?";
                    $checkStmt = $conn->prepare($checkSql);
                    $checkStmt->bind_param("s", $nepKOD);

                    $checkStmt->execute();
                    $checkResult = $checkStmt->get_result();

                    if ($checkResult->num_rows == 0) {
                        $insertSql = "INSERT INTO trophies_for_students_sem (trophID, nepKOD) VALUES (2, ?)";
                        $stmt = $conn->prepare($insertSql);
                        $stmt->bind_param("s", $nepKOD);

                        if ($stmt->execute()) {
                            echo "A hallgató megjutalmazva!";
                        } else {
                            echo "Hiba a megjutalmazás során: " . $stmt->error;
                        }
                    } else {
                        echo "A hallgató már megkapta a trófeát ebben a kurzusban.";
                    }

                    $checkStmt->close();
                    $conn->close();
                }

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
        background-image: url('./flame.png');
        background-position: center;
        background-repeat: no-repeat;
        background-attachment: fixed;
        background-size: cover;
    }


    ul {
        list-style-type: none;
        text-align: left;
    }

    input[type="submit"] {
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
    }

    input[type="submit"]:hover {
        background-color: #800000;
        border-bottom: #FF5733 5px solid;
        border-right: #FF5733 5px solid;
    }

    .points {
        width: 80%;
        border-collapse: collapse;
        overflow: hidden;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        margin: 0 auto;

    }

    .points td {
        padding: 15px;
        background-color: #Ffa233;
        color: #800;
        border-bottom: 1px solid white;
    }

    .points th {
        padding: 15px;
        border-bottom: 1px solid white;
        color: #fff;
        background-color: #aa3a22;
    }
</style>

</html>