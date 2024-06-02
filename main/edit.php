<?php
session_start();
$conn = new mysqli("localhost","root","", "tetelek"); //create conn
// Check connection
if ($conn->connect_error) {
    die("Csatlakozás sikertelen: " . $conn->connect_error);
}
$tetelid = $_GET['tetelid'];
$ERROR = null;
$sql = "SELECT * FROM tetelcimek INNER JOIN tantargyak ON tetelcimek.tantargyid=tantargyak.id WHERE tetelcimek.id=?;";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $tetelid);
if($stmt->execute()==true){
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $stmt->close();
}else $data = null;
/* RELOAD CHECKS */
if(!empty($_GET['edittag'])){
    $cim = $_GET['cimnew'];
    $vazlat = $_GET['vazlatnew'];
    $kidolg = $_GET['kidolgnew'];
    $tanid = $_GET['tantargynew'];
    $currentdate = date('Y-m-d');
    $sql = "UPDATE tetelcimek SET cim=?, vazlat =?, kidolgozas=?, modosit=?, tantargyid=? WHERE tetelcimek.id=?;";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssii", $cim, $vazlat, $kidolg, $currentdate, $tanid, $tetelid);
    if($stmt->execute()==true){
        $conn->close();
        $_SESSION['editsuccess'] = true;
        session_write_close();
        header('Location: index.php'); /* EDIT SUCCESS, RETURN TO INDEX WITH SUCCESS VAR */
    }
    else{
        $ERROR = 2;
    }
}
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
            <div style="visibility: hidden;" class="nav-item item1"><a href="#tortdiv">Történelem</a></div> <!-- just here as padding so header stays large, yes im lazy -->
            <div style="visibility: hidden;" class="nav-item item2"><a href="#iroddiv">Irodalom</a></div>   <!-- just here as padding so header stays large, yes im lazy -->
            <div style="visibility: hidden;" class="nav-item item3"><a href="#nyelvdiv">Nyelvtan</a></div>  <!-- just here as padding so header stays large, yes im lazy -->
        </nav>
    </header>
    <main style="width: 100vw;">
        <?php if ($data):?>
            <form class="editform" method="get">
                <div class="items">
                    <label for="cimnew">Cím: </label>
                    <input type="text" name="cimnew" id="cimnew" value="<?php echo $data['cim'];?>">
                </div>
                <div class="items">
                    <label for="tantargynew">Tantárgy: </label>
                    <select name="tantargynew" id="tantargynaw">
                        <?php 
                        switch($data['tantargyid']){
                            case'1':
                                echo '<option value="1" selected="seleced">Történelem</option>';
                                echo '<option value="2">Irodalom</option>';
                                echo '<option value="3">Nyelvtan</option>';
                                break;
                            case '2':
                                echo '<option value="1">Történelem</option>';
                                echo '<option value="2" selected="seleced">Irodalom</option>';
                                echo '<option value="3">Nyelvtan</option>';
                                break;
                            case '3':
                                echo '<option value="1">Történelem</option>';
                                echo '<option value="2">Irodalom</option>';
                                echo '<option value="3" selected="seleced">Nyelvtan</option>';
                                break;
                        }
                        ?>
                    </select>
                </div>
                <div class="items">
                    <label for="vazlatnew">Vázlat: </label>
                    <textarea name="vazlatnew" id="vazlatnew"><?php echo nl2br(htmlspecialchars($data['vazlat']));?></textarea>
                </div>
                <h3></h3>
                <div class="items">
                    <label for="kidolgnew">Kidolgozás: </label>
                    <textarea name="kidolgnew" id="kidolgnew"><?php echo nl2br(htmlspecialchars($data['kidolgozas']));?></textarea>
                </div>
                <input type="hidden" name="edittag" value="1">
                <input type="hidden" name="tetelid" value="<?php echo $tetelid;?>">
                <button type="submit">Frissítés</button>
            </form>
        <?php else: ?>
            <h1>shit went really fucking wrong somewhere, good job c:</h1>
        <?php endif ?>
    </main>
    
</body>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script>
$(document).ready(function(){
    $('.toast').toast('show');
});
</script>
</html>