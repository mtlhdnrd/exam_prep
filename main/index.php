<?php
session_start();
//IF SEARCH
if(!empty($_GET['query'])){
    echo '<script> console.log("search if initiated");</script>'; // DEBUG
    $conn = new mysqli("localhost","root","", "tetelek"); //create conn
    // Check connection
    if ($conn->connect_error) {
        die("Csatlakozás sikertelen: " . $conn->connect_error);
    }
    $search = $_GET['query']; //get query
    echo '<script> console.log("search query is: '.$search.'");</script>'; // DEBUG 
    $sql = "SELECT id, cim, tantargyid FROM tetelcimek WHERE cim LIKE ? OR vazlat LIKE ? OR kidolgozas LIKE ?;";
    echo '<script> console.log("sql query: '.$sql.'");</script>'; // DEBUG 
    $stmt = $conn ->prepare($sql);
    $search_mod = '%'.$search.'%';
    $stmt->bind_param("sss", $search_mod, $search_mod, $search_mod);
    $items = [];
    switch ($stmt->execute()) {
        case true:
            echo '<script> console.log("execute successful");</script>'; // DEBUG 
            $result = $stmt->get_result();
            if($result->num_rows>0){
                echo '<script> console.log("'.$result->num_rows.' rows found");</script>'; // DEBUG 
                while ($row = $result->fetch_assoc()){
                    $items[] = $row;
                }
                    echo '<script> console.log("items array loaded");</script>'; // DEBUG 
                    echo '<script> console.log("'.$items[0]['id'].' || '.$items[0]['cim'].' || '.$items[0]['tantargyid'].'");</script>'; // DEBUG 
            }else{
                echo '<script> console.log("no rows found");</script>'; // DEBUG 
            }
            break;
        case false:
            echo '<script> console.log("execute falied");</script>'; // DEBUG 
            echo '<script> console.log("Error: '.$stmt->error?:$conn->error.'");</script>'; // DEBUG
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
    $sql = "SELECT id, cim, tantargyid FROM tetelcimek;";
    $items = [];
    if($conn->query($sql)==true){
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
echo '<script> console.log("items[g length: '.count($items).'");</script>'; // DEBUG 
foreach ($items as $var) {
    switch ($var['tantargyid']) {
        case '1':
            array_push($items_h, $var);
            break;
        case '2':
            array_push($items_l, $var);
            break;
        case '3':
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
            <div class="nav-item item1"><a href="#tortdiv">Történelem</a></div>
            <div class="nav-item item2"><a href="#iroddiv">Irodalom</a></div>
            <div class="nav-item item3"><a href="#nyelvdiv">Nyelvtan</a></div>
        </nav>
    </header>
    <main class="home">
        <!-- EDIT SUCCESS POPUP -->
            <?php if(isset($_SESSION['editsuccess']) ||isset($_SESSION['addsuccess'])): ?>
                <div class="toast" aria-live="assertive" aria-atomic="true" role="alert" data-delay="3000" style="position:fixed; top:30px; right:30px; z-index: 2;">
                    <div class="toast-header">
                        <strong class="mr-auto">SUCCESS</strong>
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
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif;?>
        <a href="add.php" class="button">Új tétel felvétele...</a>
        <form class="search-container" method="get">
            <input type="text" placeholder="Search..." name="query">
            <button type="submit">Search</button>
        </form>
        <div class="listing" id="tortdiv">
            <h2>Történelem</h2>
            <ul>
                <?php if(!empty($items_h)): ?>
                    <?php foreach($items_h as $tetel): ?>
                        <li><a href="tetel.php?tetelid=<?=$tetel['id']?>"><?=$tetel['cim'] ?></a> <a class="editbutton" href="edit.php?tetelid=<?=$tetel['id']?>">módostás</a> </li>
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
                        <li><a href="tetel.php?tetelid=<?=$tetel['id']?>"><?=$tetel['cim'] ?></a> <a class="editbutton" href="edit.php?tetelid=<?=$tetel['id']?>">módostás</a> </li>
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
                        <li><a href="tetel.php?tetelid=<?=$tetel['id']?>"><?=$tetel['cim'] ?></a> <a class="editbutton" href="edit.php?tetelid=<?=$tetel['id']?>">módostás</a> </li>
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
        Fejlesztők:
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