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
$sql = "SELECT hetID, het FROM hetek"; // Hetek lekérése
$result = $conn->query($sql);

$hetek = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $hetek[$row['hetID']] = $row['het'];
    }
}

// Űrlap adatok inicializálása és hibák tömbje
$urlap_adatok = [
    //'tesztID' => '',
    'kurzusNEV' => '',
    'hetID' => '',
    'kerdes' => '',
    'a' => '',
    'b' => '',
    'c' => '',
    'd' => '',
    'e' => '',
    'f' => '',
    'helyesValasz' => '',
];

$hibak = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Form adatok ellenőrzése és beállítása

    // Kurzus ID ellenőrzése
    if (!isset($_POST['i_kurzusNEV'])) {
        $hibak[] = 'A kurzus kiválasztása kötelező!';
    } else {
        $urlap_adatok['kurzusNEV'] = $_POST['i_kurzusNEV'];
    }

    // Het ID ellenőrzése
    if (!isset($_POST['i_hetID'])) {
        $hibak[] = 'A hét kiválasztása kötelező!';
    } else {
        $urlap_adatok['hetID'] = $_POST['i_hetID'];
    }

    // Kérdés ellenőrzése
    if (!isset($_POST['i_kerdes'])) {
        $hibak[] = 'A kérdés megadása kötelező!';
    } else {
        $urlap_adatok['kerdes'] = $_POST['i_kerdes'];
    }

    // Válaszok ellenőrzése
    $valaszok = ['a', 'b', 'c', 'd', 'e', 'f'];
    foreach ($valaszok as $valasz) {
        $mezőnév = 'i_' . $valasz;
        if (!isset($_POST[$mezőnév])) {
            $hibak[] = "A(z) '$valasz' válaszlehetőség megadása kötelező!";
        } else {
            $urlap_adatok[$valasz] = $_POST[$mezőnév];
        }
    }

    // Helyes válasz ellenőrzése
    if (!isset($_POST['i_helyesValasz'])) {
        $hibak[] = 'A helyes válaszlehetőség megadása kötelező!';
    } else {
        $urlap_adatok['helyesValasz'] = $_POST['i_helyesValasz'];
    }

    // Kérdés hossz ellenőrzése
    if (strlen($_POST['i_kerdes']) < 5) {
        $hibak[] = 'A kérdés legalább 5 karakter kell, hogy legyen!';
    }

    // DEBUG: Hibák kiírása
    var_dump($hibak);

    // Ha nincsenek hibák, akkor adatok beszúrása az adatbázisba
    if (empty($hibak)) {
        try {
            $insert_query_test = 'INSERT INTO tesztsor (kurzusNEV, hetID, kerdes, a, b, c, d, e, f, helyesValasz, nehez)
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
            $stmt_test = $conn->prepare($insert_query_test);

            $nehez = ($_POST['i_nehez'] == 'igen') ? 1 : 0;

            $stmt_test->bind_param("sssssssssss", $urlap_adatok['kurzusNEV'], $urlap_adatok['hetID'], $urlap_adatok['kerdes'], $urlap_adatok['a'], $urlap_adatok['b'], $urlap_adatok['c'], $urlap_adatok['d'], $urlap_adatok['e'], $urlap_adatok['f'], $urlap_adatok['helyesValasz'], $nehez);
            $stmt_test->execute();

            header('Location: kurzusok.php');
            exit;
        } catch (Exception $e) {
            die("Adatbázis hiba: " . $e->getMessage());
        }
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="hu">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teszt hozzáadása</title>
    <link href="style.css" rel="stylesheet" />
</head>


<body>

    <table class="main-table">
        <tr>
            <td colspan="5" class="banner">
                <div class="avatar-info">
                    <div class="avatar">
                        <!-- Az avatar kép megjelenítése
                    <img src="data:image/png;base64,<?php echo base64_encode($row['avatar']); ?>" width="150" height="150"> -->
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
            <td colspan="5" class="content">

                <div class="form">

                    <br><br>

                    <form action="" method="POST">
                        <br>
                        <!-- <label for="i_tesztID">Tesztsor ID-ja:</label>
                        <input type="number" name="i_tesztID" id="i_tesztID" value="<?= $urlap_adatok['tesztID'] ?>" min="1" required><br> -->


                        <!-- Legördülő lista: Kurzus neve -->
                        <label for="i_kurzusNEV">Kurzus kiválasztása:</label>
                        <select name="i_kurzusNEV" id="i_kurzusNEV">
                            <option value="" disabled selected>Válasszon kurzust!</option>
                            <?php
                            $lathatoKurzusok = array(); // Tömb az eddig látott kurzusnevek tárolásához

                            foreach ($nevek as $kurzusnev) :
                                if (!in_array($kurzusnev, $lathatoKurzusok)) {
                                    echo '<option value="' . $kurzusnev . '" ' . ($kurzusnev == $urlap_adatok['kurzusNEV'] ? 'selected' : '') . '>' . $kurzusnev . '</option>';
                                    $lathatoKurzusok[] = $kurzusnev; // Kurzus hozzáadása az eddig látottakhoz
                                }
                            endforeach;
                            ?>
                        </select><br>


                        <!-- Legördülő lista: Hetek kiválasztása -->
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

                        <label for="i_kerdes">Kérdés:</label>
                        <input type="text" name="i_kerdes" id="i_kerdes" value="<?= $urlap_adatok['kerdes'] ?>" placeholder="Kérdés:" required><br><br>

                        <label for="i_a">A válaszlehetőség:</label>
                        <input type="text" name="i_a" id="i_a" value="<?= $urlap_adatok['a'] ?>" placeholder="'A' válaszlehetőség:" required><br><br>

                        <label for="i_b">B válaszlehetőség:</label>
                        <input type="text" name="i_b" id="i_b" value="<?= $urlap_adatok['b'] ?>" placeholder="'B' válaszlehetőség:" required><br><br>

                        <label for="i_c">C válaszlehetőség:</label>
                        <input type="text" name="i_c" id="i_c" value="<?= $urlap_adatok['c'] ?>" placeholder="'C' válaszlehetőség:" required><br><br>

                        <label for="i_d">D válaszlehetőség:</label>
                        <input type="text" name="i_d" id="i_d" value="<?= $urlap_adatok['d'] ?>" placeholder="'D' válaszlehetőség:" required><br><br>

                        <label for="i_e">E válaszlehetőség:</label>
                        <input type="text" name="i_e" id="i_e" value="<?= $urlap_adatok['e'] ?>" placeholder="'E' válaszlehetőség:" required><br><br>

                        <label for="i_f">F válaszlehetőség:</label>
                        <input type="text" name="i_f" id="i_f" value="<?= $urlap_adatok['f'] ?>" placeholder="'F' válaszlehetőség:" required><br><br>

                        <!-- RÁDIÓGOMBOK a helyes válaszlehetőséghez -->
                        <div class="radio-label-group">

                            <div class="formLabel">Helyes válasz:</div><br>
                            <input type="radio" name="i_helyesValasz" id="i_helyesValaszA" value="a" <?= ($urlap_adatok['helyesValasz'] == 'a') ? 'checked' : '' ?>>
                            <label for="i_helyesValaszA">A</label><br>

                            <input type="radio" name="i_helyesValasz" id="i_helyesValaszB" value="b" <?= ($urlap_adatok['helyesValasz'] == 'b') ? 'checked' : '' ?>>
                            <label for="i_helyesValaszB">B</label><br>

                            <input type="radio" name="i_helyesValasz" id="i_helyesValaszC" value="c" <?= ($urlap_adatok['helyesValasz'] == 'c') ? 'checked' : '' ?>>
                            <label for="i_helyesValaszC">C</label><br>

                            <input type="radio" name="i_helyesValasz" id="i_helyesValaszD" value="d" <?= ($urlap_adatok['helyesValasz'] == 'd') ? 'checked' : '' ?>>
                            <label for="i_helyesValaszD">D</label><br>

                            <input type="radio" name="i_helyesValasz" id="i_helyesValaszE" value="e" <?= ($urlap_adatok['helyesValasz'] == 'e') ? 'checked' : '' ?>>
                            <label for="i_helyesValaszE">E</label><br>

                            <input type="radio" name="i_helyesValasz" id="i_helyesValaszF" value="f" <?= ($urlap_adatok['helyesValasz'] == 'f') ? 'checked' : '' ?>>
                            <label for="i_helyesValaszF">F</label>
                        </div>

                        <br><br>

                        <label for="i_nehez">Nehéz kérdés?</label>
                        <select name="i_nehez" id="i_nehez">
                            <option value="nem">Nem</option>
                            <option value="igen">Igen</option>
                        </select>

                        <br><br>

                        <div class="button-container">
                            <button type="submit">Mentés!</button>
                        </div>
                        <br>
                        <!-- Ellenőrizd, hogy van-e kurzus a tömbben -->
                        <?php if (!empty($oktatoKurzusok)) : ?>
                            <a href="tananyag.php?nev=<?= urlencode($oktatoKurzusok[0]) ?>" class="vissza-link">Vissza!</a>
                        <?php endif; ?>


                        <br><br>

                    </form>

                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            var successMessage = document.getElementById("success-message");
                            var form = document.querySelector("form");

                            form.addEventListener("submit", function(e) {
                                e.preventDefault();

                                // Adatok elküldése az űrlapról a szerverre AJAX segítségével
                                var xhr = new XMLHttpRequest();

                                xhr.open("POST", "mentes.php", true);

                                xhr.onreadystatechange = function() {
                                    if (xhr.readyState === 4 && xhr.status === 200) {
                                        successMessage.style.display = "block";

                                        setTimeout(function() {
                                            successMessage.style.display = "none";
                                            window.location.href = "addTest.php";
                                        }, 3000);
                                    }
                                };

                                // Elküldjük az űrlap adatait a szerverre
                                var formData = new FormData(form);
                                xhr.send(formData);
                            });
                        });
                    </script>


                    <div id="success-message" class="success-message">A kérdés mentésre került!</div>



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
    .form {
        background-image: url('./addTest_FormBG.png');
        background-position: center;
        background-repeat: no-repeat;
        background-attachment: fixed;
        text-align: center;
    }

    .button-container button[type="submit"] {
        background-color: #FF5733;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 15px;
        border-bottom: #800000 5px solid;
        border-left: #800000 5px solid;
        cursor: pointer;
        font-weight: bold;
        margin-right: 10px;
        transition: background-color 0.3s;
    }

    .button-container button[type="submit"]:hover {
        background-color: #800000;
        border-bottom: #FF5733 5px solid;
        border-left: #FF5733 5px solid;
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
    }

    .vissza-link:hover {
        background-color: #800000;
        border-bottom: #FF5733 5px solid;
        border-right: #FF5733 5px solid;
    }

    .formLabel,
    label[for="i_kerdes"],
    label[for="i_hetID"],
    label[for="i_kurzusNEV"],
    label[for="i_a"],
    label[for="i_b"],
    label[for="i_c"],
    label[for="i_d"],
    label[for="i_e"],
    label[for="i_f"],
    label[for="i_helyesValasz"],
    label[for="i_nehez"] {
        font-weight: bold;
        color: #800000;
    }

    .radio-label-group {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .radio-label-group label {
        margin-right: 10px;
        text-align: center;
    }

    /* A legördülő lista testreszabása */
    select {
        background-color: #f0f0f0;
        color: #333;
        border: 1px solid #999;
        border-radius: 5px;
        padding: 5px;
        width: 15%;
    }

    input[type="text"] {
        background-color: #f0f0f0;
        color: #333;
        border: 1px solid #999;
        border-radius: 5px;
        padding: 5px;
        width: 15%;

    }

    .success-message {
        display: none;
        background-color: #4CAF50;
        color: white;
        text-align: center;
        padding: 10px;
        position: fixed;
        top: 0;
        left: 50%;
        transform: translateX(-50%);
        z-index: 999;
    }
</style>


</html>