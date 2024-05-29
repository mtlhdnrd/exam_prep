<?php
$conn = new mysqli("localhost","root","", "tetelek"); //create conn
$tetelid = $_GET['tetelid'];
$ERROR = null;
/* RELOAD CHECKS */
if(!empty($_GET['edittag'])){
    /* TANTARGY CHECK */
    if($_GET['tantargynew']==0){
        $ERROR = 1;
    }else{
        /* IF TANTARGY GOOD --> CONTINUE */
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
            header('Location: index.php?editsuccess=true'); /* EDIT SUCCESS, RETURN TO INDEX WITH SUCCESS VAR */
        }
        else{
            $ERROR = 2;
        }
    }
}
$sql = "SELECT * FROM tetelcimek INNER JOIN tantargyak ON tetelcimek.tantargyid=tantargyak.id WHERE tetelcimek.id=?;";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $tetelid);
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
    <main style="width: 100vw;">
        <?php if ($data):?>
            <?php if($ERROR): ?>
                <!-- HOLY FUCKING SHIT I SPENT WAY TOO LONG ON THIS STUPID ASS POPUP BUT IT FINALLY WORKS -->
                <!-- link for documentation if you wanna change the css of it c:  https://getbootstrap.com/docs/4.2/components/toasts/-->
                <div class="toast" aria-live="assertive" aria-atomic="true" role="alert" data-delay="3000" style="position:fixed; top:30px; right:30px; z-index: 2;">
                    <div class="toast-header">
                        <strong class="mr-auto">ERROR</strong>
                        <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                            &times;
                        </button>
                    </div>
                    <div class="toast-body">
                        <?php if($ERROR == 1): ?>
                            Kérem válasszon ki egy tantárgyat!
                        <?php elseif($ERROR == 2):?>
                            Frissítés sikertelen
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif;?>
            <form class="editform" method="get">
                <h3>Cím</h3>
                <div class="items">
                    <p>Régi: <?php echo $data['cim'];?></p>
                    <label for="cimnew">Új: </label>
                    <input type="text" name="cimnew" id="cimnew">
                </div>
                <h3>Tantárgy</h3>
                <div class="items">
                    <p>Régi: <?php echo $data['tantargy'];?></p>
                    <label for="tantargynew">Új: </label>
                    <select name="tantargynew" id="tantargynaw">
                        <option value="0">Válassz egy tantárgyat</option>
                        <option value="1">Történelem</option>
                        <option value="2">Irodalom</option>
                        <option value="3">Nyelvtan</option>
                    </select>
                </div>
                <h3>Vázlat</h3>
                <div class="items">
                    <p>Régi: <br><?php echo nl2br(htmlspecialchars($data['vazlat']));?></p>
                    <label for="vazlatnew">Új: </label>
                    <textarea name="vazlatnew" id="vazlatnew"></textarea>
                </div>
                <h3>Kidolgozás</h3>
                <div class="items">
                    <p>Régi: <br> <?php echo nl2br(htmlspecialchars($data['kidolgozas']));?></p>
                    <label for="kidolgnew">Új: </label>
                    <textarea name="kidolgnew" id="kidolgnew"></textarea>
                </div>
                <input type="hidden" name="edittag" value="1">
                <input type="hidden" name="tetelid" value="<?php echo $tetelid;?>">
                <button type="submit">Frissítés</button>
            </form>
        <?php else:?>
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