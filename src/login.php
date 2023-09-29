<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bejelentkezés</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <h2>Bejelentkezés</h2>
        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="user-type">Belépés típusa</label>
                <select id="user-type" name="user-type">
                    <option value="2">Hallgató</option>
                    <option value="1">Oktató</option>
                </select>
            </div>
            <div class="form-group">
                <label for="username">Felhasználónév</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Jelszó</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Bejelentkezés</button>


            <?php
                session_start();
                if (isset($_SESSION['error'])) {
                    echo "<p style='color: red;'>".$_SESSION['error']."</p>";
                    unset($_SESSION['error']);
                }
            ?>


        </form>
    </div>
</body>
</html>

// Adatbázis kapcsolódás
$servername = "localhost";
$username = "Admin";
$password = "_K*uqlR2qRzexuzw";
$dbname = "SZD_jatekositas";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Hiba a kapcsolódás során: " . $conn->connect_error);
}

// A formból érkező adatok feldolgozása
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userType = $_POST["user-type"];
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Ellenőrzés az adatbázisban
    $sql = "SELECT * FROM users WHERE neptun_kod = '$username' AND jelszo = '$password' AND user_type = '$userType'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
    // Sikeres belépés
    session_start();
    $_SESSION["username"] = $username;
    $_SESSION["user_type"] = $userType;

    // Átirányítás a megfelelő céloldalra
    if ($userType == "2") {
        header("Location: fooldal_hallgato.php");
    } elseif ($userType == "1") {
        header("Location: fooldal_oktato.php");
    }

    exit();
} else {
    // Belépés sikertelen
    $_SESSION['error'] = "Hibás felhasználónév, jelszó vagy belépési típus.";
    header("Location: login.php"); // Átirányítás a bejelentkező oldalra
    exit();
}

}

$conn->close();
?>
