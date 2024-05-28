<?php
$tetelid = $_GET["tetelid"]; 
$conn = new mysqli("localhost","root","", "tetelek"); //create conn
$sql = "SELECT * FROM tetelcimek WHERE id=?";
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <title>Szóbeli tételek</title>
</head>
<body class="tetelpage">
    <header>
        head shit goes here pls
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