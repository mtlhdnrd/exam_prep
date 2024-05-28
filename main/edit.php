<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tetelek";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if (!$conn) {
    die("Csatlakozás sikertelen: " . mysqli_connect_error());
}
echo "Sikeres csatlakozás <br>";

// Run SQL query

if ($_SERVER["REQUEST_METHOD"] == "POST") {
}
?>

<!DOCTYPE html>
<html lang="hu">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <link rel="stylesheet" href="style.css">
        <title>Szóbeli tételek</title>
    </head>
    <body>
        <header>
            <h1>Tételek</h1>
            <nav class="navbar">
                <div class="nav-item item1"><a href="#tortdiv">Történelem</a></div>
                <div class="nav-item item2"><a href="#iroddiv">Irodalom</a></div>
                <div class="nav-item item3"><a href="#nyelvdiv">Nyelvtan</a></div>
            </nav>
        </header>
    <main>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label for="cim">Tételcím: </label>
        <select name="cim" id="cim">
            <?php echo $optionstitle; ?>
        </select>
        <br>
        <label for="tantargy">Tantárgy: </label>
        <select name="tantargy" id="tantargy">
            <?php echo $optionssub; ?>
        </select>
        <br>
        <label for="type">Vázlat: </label>
        <input type="file" name="fileToUpload" id="fileToUpload">
        <br>
        <label for="type">Kidolgozás: </label>
        <input type="file" name="fileToUpload" id="fileToUpload">
        <br>
        <input type="submit" value="Upload File" name="submit">
    </form>
    </main>
</body>
</html>