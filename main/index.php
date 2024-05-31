<?php
if(!empty($_GET['query'])){
    $conn = new mysqli("localhost","root","", "tetelek"); //create conn
    $items = []; //create return matrix
    $search = $_GET['query']; //get query
    $sql = "SELECT cim FROM 'tetelcimek' WHERE id = ? OR cim LIKE ? OR vazlat LIKE ? OR kidolgozas LIKE ?;"; //write query
    $stmt = $conn ->prepare($sql); //prepare query
    $se = "%".$search."%"; //change vars to actually work the way they're supposed to
    $id = intval($search); // -||-
    $stmt->bind_param("isss", $id,$se,$se,$se); //bind variables
    if($stmt->execute()==true){ //if it doesn't die
        $result = $stmt->get_result(); //get the stuff
        while ($row = $result->fetch_assoc()) {  
            array_push($items, $row); //cram the stuff into the matrix
        }
    }
    $conn->close(); //the end
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
    <main>
        <!-- EDIT SUCCESS POPUP -->
            <?php if(!empty($_GET['editsuccess'])||!empty($_GET['addsuccess'])): ?>
                <div class="toast" aria-live="assertive" aria-atomic="true" role="alert" data-delay="3000" style="position:fixed; top:30px; right:30px; z-index: 2;">
                    <div class="toast-header">
                        <strong class="mr-auto">SUCCESS</strong>
                        <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                            &times;
                        </button>
                    </div>
                    <div class="toast-body">
                        <?php if($_GET['addsuccess']): ?>
                            Sikeres hozzáadás!
                        <?php elseif($_GET['editsuccess']): ?>
                            Sikeres változtatás!
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
                <li><a href="tetel.php?tetelid=1">aha</a><a class="editbutton" href="edit.php?tetelid=1">módosítás</a></li>
                <li><a href="tetel.php?tetelid=2">aha</a><a class="editbutton" href="edit.php?tetelid=2">módosítás</a></li>
                <li><a href="tetel.php?tetelid=3">aha</a><a class="editbutton" href="edit.php?tetelid=3">módosítás</a></li>
                <li><a href="tetel.php?tetelid=4">aha</a><a class="editbutton" href="edit.php?tetelid=4">módosítás</a></li>
            </ul>
        </div>
        <div class="listing" id="iroddiv">
            <h2>Irodalom</h2>
            <ul>
                <li>aha</li>
                <li>aha</li>
                <li>aha</li>
            </ul>
        </div>
        <div class="listing" id="nyelvdiv">
            <h2>Nyelvtan</h2>
            <ul>
                <li>aha</li>
                <li>aha</li>
                <li>aha</li>
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