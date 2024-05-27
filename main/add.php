<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tetelek";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Csatlakozás sikertelen: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $sql = "INSERT INTO tetelcimek(cim, tantargy, vazlat, kidolgozas) VALUES (?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $cim, $tantargy, $vazlat, $kidolgozas);
    
    // Set parameter values
    $cim = $_POST['cim'];
    $tantargy = $_POST['tantargy'];
    $vazlat = $_POST['vazlat'];
    $kidolgozas = $_POST['kidolgozas'];
    //Execute SQL statement
    if ($stmt->execute()) {
        echo "Sikeresen hozzáadva!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
    //Close the connection
    $stmt->close();
    $conn->close();
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
    <?php
        $sql = "SELECT id, cim FROM tetelcimek";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $tetelcimek = $result->fetch_all(MYSQLI_ASSOC);
        } else {
            $tetelcimek = [];
        } 
        $conn->close();
    ?>

    <form action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <label for="tetel">Choose an tetel:</label>
        <select name="tetel" id="tetel">
        <?php foreach ($tetelcimek as $tetel): ?>
            <option value="<?php echo $tetel['id']; ?>"><?php echo $tetel['name']; ?></option>
        <?php endforeach; ?>
        </select>
        <br>
        <label for="type">Típusa</label>
        <input type="text" id="type" name="type">
        <br>
        <label for="type">Típusa</label>
        <input type="text" id="type" name="type">
        <br>
        <label for="type">Típusa</label>
        <input type="text" id="type" name="type">
        <br>
    </form>
    </main>
</body>
</html>