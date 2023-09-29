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
