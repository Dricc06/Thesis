<?php
session_start();

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != "1") {
    header("Location: login.php");
    exit();
}

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

// Kurzus nevek lekérése az adatbázisból
$sql = "SELECT kurzusNEV FROM tesztsor";
$result = $conn->query($sql);

if (!$result) {
    die("Adatbázis hiba: " . $conn->error);
}

$nevek = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $nevek[] = $row['kurzusNEV'];
    }
}

// Elérhető hetek lekérése az adatbázisból
$sql = "SELECT hetID, het FROM hetek";
$result = $conn->query($sql);

$hetek = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $hetek[$row['hetID']] = $row['het'];
    }
}

$urlap_adatok = [
    'kurzusNEV' => '',
    'hetID' => ''
];

$hibak = [];

if (isset($_POST['submit'])) {
    $hetID = $_POST['i_hetID'];
    $fajlnev = $_FILES['pdf_file']['name'];
    $fajltipus = 'pdf';
    $fajl = $_FILES['pdf_file']['tmp_name'];

    if (isset($_FILES['pdf_file']['name'])) {

        $sql = "INSERT INTO fajlok (hetid, fajlnev, fajltipus, fajl) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isss", $hetID, $fajlnev, $fajltipus, $fajl);

        if ($stmt->execute()) {
            header("Location: success_file.php");
        } else {
            echo "Hiba a fájl feltöltésekor: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="hu">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Új fájl feltöltése</title>
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
            <td colspan="5" class="content">
                <h1>Új fájl feltöltése</h1>
                <form action="" method="post" enctype="multipart/form-data">
                    <label for="i_kurzusNEV">Kurzus kiválasztása:</label>
                    <select name="i_kurzusNEV" id="i_kurzusNEV">
                        <option value="" disabled selected>Válasszon kurzust!</option>
                        <?php
                        $lathatoKurzusok = array();

                        foreach ($nevek as $kurzusnev) :
                            if (!in_array($kurzusnev, $lathatoKurzusok)) {
                                echo '<option value="' . $kurzusnev . '" ' . ($kurzusnev == $urlap_adatok['kurzusNEV'] ? 'selected' : '') . '>' . $kurzusnev . '</option>';
                                $lathatoKurzusok[] = $kurzusnev;
                            }
                        endforeach;
                        ?>
                    </select><br>

                    <label for="i_hetID">Hét kiválasztása:</label>
                    <select name="i_hetID" id="i_hetID">
                        <option value="" disabled selected>Válasszon hetet!</option>
                        <?php
                        foreach ($hetek as $hetID => $het) {
                            echo '<option value="' . $hetID . '"';
                            if ($urlap_adatok['hetID'] == $hetID) {
                                echo ' selected';
                            }
                            echo '>' . $het . '</option>';
                        }
                        ?>
                    </select>
                    <br><br>

                    <label for="fajl">Fájl kiválasztása:</label>
                    <div class="form-group">
                        <input type="file" name="pdf_file" accept=".pdf" title="Upload PDF" />
                    </div>
                    <br>

                    <input type="submit" name="submit" value="Fájl feltöltése">
                </form>

                <br>
                <a href="kurzusok.php">Vissza a kurzusokhoz</a>
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
</body>

</html>