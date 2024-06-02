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
    <header class="full_head">
        <a href="index.php"><h1>Tételek</h1></a>
        
    </header>
    <main class="tetel_main">
        <div class="listing">
    <?php if($data): ?>
        <h1><?php echo $data['id'].". Tétel: ".$data["cim"];?></h1>
        <h2><?php echo"Tantárgy: ".$data["tantargy"];?></h2>
        <div>
            <h2>vázlat: </h2>
            <p><?php echo nl2br(htmlspecialchars($data["vazlat"]));?></p>
        </div>
        <div>
            <h2>kidolgozás: </h2>
            <p><?php echo nl2br(htmlspecialchars($data["kidolgozas"]));?></p>
        </div>

    <?php else: ?>
        <h1>shit went really fucking wrong somewhere, good job c:</h1>
    <?php endif; ?>
    </div>
    </main>
    
</body>
</html>