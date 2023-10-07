<?php
session_start();

if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] != "1") {
    // Ellenőrizze, hogy a felhasználó be van-e jelentkezve és oktató-e
    header("Location: login.php");
    exit();
}

// Felhasználó adatainak lekérdezése
$username = $_SESSION['username'];

$servername = "localhost";
$db_username = "Admin";
$db_password = "_K*uqlR2qRzexuzw";
$dbname = "SZD_jatekositas";

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

if ($conn->connect_error) {
    die("Kapcsolódási hiba: " . $conn->connect_error);
}

$sql = "SELECT avatar, neptun_kod FROM users WHERE neptun_kod = '$username'";
$result = $conn->query($sql); // Lekérdezés végrehajtása

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

$selected_course = ""; // Változó az aktuális kurzus nevének tárolására

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ellenőrizze, hogy a fájl feltöltés sikeres volt-e
    if (isset($_FILES["file"]) && $_FILES["file"]["error"] == 0) {
        // Mappa, ahová a fájlokat menteni szeretnénk
        $upload_dir = "fajlok/";

        // Fájl neve
        $file_name = basename($_FILES["file"]["name"]);

        // Teljes elérési útvonal a feltöltési mappába
        $target_file = $upload_dir . $file_name;

        // Ellenőrizze, hogy a fájl már létezik-e
        if (file_exists($target_file)) {
            echo "A fájl már létezik.";
        } else {
            // Mozgassa a feltöltött fájlt a célkönyvtárba
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
                echo "A fájl feltöltése sikeres volt.";

                // A kurzus kiválasztása a felhasználói űrlapról (itt a példa a POST "kurzus" mezőjét várja)
                $selected_course = $_POST["kurzus"];

                // Az oktató Neptun kódja a bejelentkezett oktató alapján
                $oktatoKod = $_SESSION['username'];

                // SQL lekérdezés a kurzus azonosítójának lekérdezéséhez
                $sql = "SELECT kurzusid FROM kurzus WHERE koktato = '$oktatoKod' AND kurzusnev = '$selected_course'";
                $result = $conn->query($sql);

                if ($result->num_rows == 1) {
                    $row = $result->fetch_assoc();
                    $kurzus_id = $row['kurzusid'];

                    // Fájl adatainak mentése az adatbázisba
                    $file_type = $_FILES["file"]["type"];
                    $file_size = $_FILES["file"]["size"];

                    $sql = "INSERT INTO fajlok (kurzusid, fajlnev, fajltipus, fajlmeret) 
                            VALUES ('$kurzus_id', '$file_name', '$file_type', '$file_size')";

                    if ($conn->query($sql) === TRUE) {
                        echo "Az adatok sikeresen mentve az adatbázisba.";
                    } else {
                        echo "Hiba az adatok mentésekor: " . $conn->error;
                    }
                } else {
                    echo "Hibás URL. A kurzus nem található.";
                }
            } else {
                echo "Hiba történt a fájl feltöltésekor.";
            }
        }
    } else {
        echo "Hiba történt a fájl feltöltésekor.";
    }
    $conn->close();
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
                    <div class="left-menu"><a href=fooldal_oktato.php target="_blank">Főoldal</a></div>
                    <div class="left-menu"><a href=kurzusok.php target="_blank">Kurzusaim</a></div>
                    <div class="right-menu"><a href=logout.php>Kijelentkezés</a></div>
                </div>
            </td>
        </tr>
        <tr>
        </tr>
        <tr>
            <td colspan="5" class="content">
                <h1>Új fájl feltöltése</h1>
                <form method="post" enctype="multipart/form-data">
                    <label>Válassza ki a kurzust:</label>
                    <select name="kurzus">
                        <?php
                        foreach ($oktatoKurzusok as $kurzus) {
                            $selected = ($kurzus == $selected_course) ? "selected" : "";
                            echo "<option value='" . htmlspecialchars($kurzus) . "' $selected>" . htmlspecialchars($kurzus) . "</option>";
                        }
                        ?>
                    </select>
                    <br>
                    <label>Fájl kiválasztása:</label>
                    <input type="file" name="file" required>
                    <br>
                    <input type="submit" value="Feltöltés">
                </form>
                <br>
                <a href="tananyag.php?nev=<?= urlencode($selected_course) ?>">Vissza a tananyaghoz</a>
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