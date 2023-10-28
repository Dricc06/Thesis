<?php
$servername = "localhost";
$username = "Admin";
$password = "_K*uqlR2qRzexuzw";
$dbname = "SZD_jatekositas";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Kapcsolódási hiba: " . $conn->connect_error);
}
if (isset($_POST['submit'])) {
    $kurzus = $_POST['i_kurzusNEV'];
    $het = $_POST['i_hetID'];

    $sql = "SELECT neptun_KOD, eredmeny_PONT, bekuldes FROM eredmenyek WHERE kurzus_NEV = '$kurzus' AND het_ID = '$het'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {

        echo "<h2>Eredmények:</h2>";
        echo "<table>";
        echo "<tr><th>Hallgató Neptun kódja</th><th>Pontszám</th><th>Beküldés ideje</th></tr>";
        while ($row = $result->fetch_assoc()) {
            echo "<tr><td>" . $row['neptun_KOD'] . "</td><td>" . $row['eredmeny_PONT'] . "</td><td>" . $row['bekuldes'] . "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "Nincsenek eredmények a kiválasztott kurzus és hét kombinációhoz.";
    }
}

$conn->close();
