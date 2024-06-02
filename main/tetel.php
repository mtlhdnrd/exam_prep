<?php
$tetelid = $_GET["tetelid"]; 
$conn = new mysqli("localhost","root","", "tetelek"); //create conn
// Check connection
if ($conn->connect_error) {
    die("Csatlakozás sikertelen: " . $conn->connect_error);
}
$sql = "SELECT tetelcimek.id, cim, vazlat, kidolgozas, tantargy FROM tetelcimek INNER JOIN tantargyak ON tetelcimek.tantargyid=tantargyak.id WHERE tetelcimek.id=?;";
$stmt = $conn ->prepare($sql); //prepare query
$stmt->bind_param("i",$tetelid);
if($stmt->execute()==true){
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $stmt->close();
}else $data = null;
$conn->close();
?>

<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <title>Szóbeli tételek</title>
</head>
<body class="tetelpage">
    <header>
        <a href="index.php"><h1>Tételek</h1></a>
        <nav class="navbar">
            <div style="visibility: hidden;" class="nav-item item1"><a href="#tortdiv">Történelem</a></div> <!-- just here so header stays large, yes im lazy -->
            <div style="visibility: hidden;" class="nav-item item2"><a href="#iroddiv">Irodalom</a></div>   <!-- just here so header stays large, yes im lazy -->
            <div style="visibility: hidden;" class="nav-item item3"><a href="#nyelvdiv">Nyelvtan</a></div>  <!-- just here so header stays large, yes im lazy -->
        </nav>
    </header>
    <main>
    <?php if($data): ?>
        <h1><?php echo $data['id'].". Tétel: ".$data["cim"];?></h1>
        <h2><?php echo"Tantárgy: ".$data["tantargy"];?></h2>
        <div>
            <h3>vázlat: </h3>
            <p><?php echo nl2br(htmlspecialchars($data["vazlat"]));?></p>
        </div>
        <div>
            <h3>kidolgozás: </h3>
            <p><?php echo nl2br(htmlspecialchars($data["kidolgozas"]));?></p>
        </div>

    <?php else: ?>
        <h1>shit went really fucking wrong somewhere, good job c:</h1>
    <?php endif; ?>

    </main>
    
</body>
</html>