<?php
session_start();
//IF SEARCH
if(!empty($_GET['query'])){
    $conn = new mysqli("localhost","root","", "tetelek"); //create conn
    // Check connection
    if ($conn->connect_error) {
        die("Csatlakozás sikertelen: " . $conn->connect_error);
    }
    $search = $_GET['query']; //get query
    $sql = "SELECT id, cim, tantargyid FROM tetelcimek WHERE cim LIKE ? OR vazlat LIKE ? OR kidolgozas LIKE ?;";
    $stmt = $conn ->prepare($sql);
    $search_mod = '%'.$search.'%';
    $stmt->bind_param("sss", $search_mod, $search_mod, $search_mod);
    $items = [];
    switch ($stmt->execute()) {
        case true:
            $result = $stmt->get_result();
            if($result->num_rows>0){
                while ($row = $result->fetch_assoc()){
                    $items[] = $row;
                }
            }else{
            }
            break;
        case false:
            break;
    }
    $conn->close(); //the end
    //IF NOT SEARCH
}else{
    $conn = new mysqli("localhost","root","", "tetelek"); //create conn
    // Check connection
    if ($conn->connect_error) {
        die("Csatlakozás sikertelen: " . $conn->connect_error);
    }
    $sql = "SELECT tetelcimek.id, cim, tantargy FROM tetelcimek INNER JOIN tantargyak On tetelcimek.tantargyid=tantargyak.id;";
    $items = [];
    if($conn->query($sql)) {
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $items[] = $row; // Add each row as an associative array to the $items array
            }
        }
    }
    $conn->close();
}
$items_h = [];
$items_l = [];
$items_g = [];
foreach ($items as $var) {
    switch ($var['tantargy']) {
        case 'Történelem':
            array_push($items_h, $var);
            break;
        case 'Irodalom':
            array_push($items_l, $var);
            break;
        case 'Nyelvtan':
            array_push($items_g, $var);
            break;
        default:
            break;
    }
}
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
<body>
    <header>
        <a href="index.php"><h1>Tételek</h1></a>
        <nav class="navbar">
            <a href="#tortdiv"><div class="nav-item" id="item1">Történelem</div></a>
            <a href="#iroddiv"><div class="nav-item" id="item2">Irodalom</div></a>
            <a href="#nyelvdiv"><div class="nav-item" id="item3">Nyelvtan</div></a>
        </nav>
    </header>
    <main>
        <!-- EDIT SUCCESS POPUP -->
            <?php if(isset($_SESSION['editsuccess']) ||isset($_SESSION['addsuccess'])): ?>
                <div class="toast" aria-live="assertive" aria-atomic="true" role="alert" data-delay="3000" style="position:fixed; top:30px; right:30px; z-index: 2;">
                    <div class="toast-header">
                        <strong class="mr-auto">ALERT</strong>
                        <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                            &times;
                        </button>
                    </div>
                    <div class="toast-body">
                        <?php if(!empty($_SESSION['editsuccess'])): ?>
                            Sikeres változtatás!
                            <?php unset($_SESSION['editsuccess']);?>
                        <?php elseif(!empty($_SESSION['addsuccess'])): ?>
                            Sikeres hozzáadás!
                            <?php unset($_SESSION['addsuccess']);?>
                        <?php elseif(!empty($_SESSION['editcancel'])): ?>
                            Módosítások elvetve!
                            <?php unset($_SESSION['editcancel']);?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif;?>
        <a href="add.php" class="add button">Új tétel felvétele...</a>
        <form class="search-container" method="get">
            <input type="text" placeholder="Search..." name="query">
            <button class="button search" type="submit">Search</button>
        </form>
        <div class="listing" id="tortdiv">
            <h2>Történelem</h2>
            <ul>
                <?php if(!empty($items_h)): ?>
                    <?php foreach($items_h as $tetel): ?>
                        <li><a href="tetel.php?tetelid=<?=$tetel['id']?>"><p><?=$tetel['cim'] ?></p></a> <a class="edit button" href="edit.php?tetelid=<?=$tetel['id']?>">módostás</a> </li>
                    <?php endforeach; ?>
                <?php elseif(isset($_GET['query'])):?>
                    <p>Nincs a keresésnek megfelelő történelem tétel</p>
                <?php else:?>
                    <p>Nincs történelem tétel az adatbázisban</p>
                <?php endif;?>
            </ul>
        </div>
        <div class="listing" id="iroddiv">
            <h2>Irodalom</h2>
            <ul>
                <?php if(!empty($items_l)): ?>
                    <?php foreach($items_l as $tetel): ?>
                        <li><a href="tetel.php?tetelid=<?=$tetel['id']?>"><p><?=$tetel['cim'] ?></p></a> <a class="edit button" href="edit.php?tetelid=<?=$tetel['id']?>">módostás</a> </li>
                    <?php endforeach; ?>
                <?php elseif(isset($_GET['query'])):?>
                    <p>Nincs a keresésnek megfelelő irodalom tétel</p>
                <?php else:?>
                    <p>Nincs irodalom tétel az adatbázisban</p>
                <?php endif;?>
            </ul>
        </div>
        <div class="listing" id="nyelvdiv">
            <h2>Nyelvtan</h2>
            <ul>
                <?php if(!empty($items_g)): ?>
                    <?php foreach($items_g as $tetel): ?>
                        <li><a href="tetel.php?tetelid=<?=$tetel['id']?>"><p><?=$tetel['cim'] ?></p></a> <a class="edit button" href="edit.php?tetelid=<?=$tetel['id']?>">módostás</a> </li>
                    <?php endforeach; ?>

                <?php elseif(isset($_GET['query'])):?>
                    <p>Nincs a keresésnek megfelelő nyelvtan tétel</p>
                <?php else:?>
                    <p>Nincs nyelvtan tétel az adatbázisban</p>
                <?php endif;?>
            </ul>
        </div>
    </main>
    <footer>
        <div id="devs" class="portrait">     
        <h2>Készítette</h2>
        </div>  
        <div class="portrait">
        <div class="portrait_div">
            <img class="portrait_img" src="../img/tokodi.png">
            <figcaption>Tokodi Mihály</figcaption>
        </div>
        <div class="portrait_div">
            <img class="portrait_img" src="../img/bako.png">
            <figcaption>Bakó Borka</figcaption>
        </div>
        <div class="portrait_div">
            <img class="portrait_img" src="../img/katona.png">
            <figcaption>Katona Bálint</figcaption>
        </div>
        </div>
    </footer>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script>
$(document).ready(function(){
    $('.toast').toast('show');
});
</script>
</body>
</html>