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

    // Teszt ID ellenőrzése
    /*if (!isset($_POST['i_tesztID'])) {
        $hibak[] = 'A teszt ID-jának megadása kötelező!';
    } else {
        $i_tesztID = $_POST['i_tesztID'];
        if (!filter_var($i_tesztID, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])) {
            $hibak[] = 'A teszt ID-jának pozitív egész számnak kell lennie!';
        } else {
            $urlap_adatok['tesztID'] = $i_tesztID;
        }
    }*/

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
            $insert_query_test = 'INSERT INTO tesztsor (/*tesztID,*/ kurzusNEV, hetID, kerdes, a, b, c, d, e, f, helyesValasz)
                                VALUES (/*?,*/ ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
            $stmt_test = $conn->prepare($insert_query_test);
            $stmt_test->bind_param("ssssssssss", /*$urlap_adatok['tesztID'],*/ $urlap_adatok['kurzusNEV'], $urlap_adatok['hetID'], $urlap_adatok['kerdes'], $urlap_adatok['a'], $urlap_adatok['b'], $urlap_adatok['c'], $urlap_adatok['d'], $urlap_adatok['e'], $urlap_adatok['f'], $urlap_adatok['helyesValasz']);
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

                        <label for="i_hetID">Hét ID-je:</label>
                        <input type="number" name="i_hetID" id="i_hetID" value="<?= $urlap_adatok['hetID'] ?>" min="1" required><br>

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
                        <b>Helyes válasz:</b><br>
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
                        <label for="i_helyesValaszF">F</label><br><br>

                        <div class="button-container">
                            <button type="submit">Mentés!</button>
                        </div>
                        <br><br>


                        <?php foreach ($oktatoKurzusok as $kurzus) : ?>
                            <a href="tananyag.php?nev=<?= urlencode($kurzus) ?>"><button type="button">Vissza!</button></a>
                        <?php endforeach; ?>
                    </form>


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