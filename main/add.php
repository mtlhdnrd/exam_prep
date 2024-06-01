<?php
session_start();
$ERROR = 0;
// Check connection
/* if ($conn->connect_error) {
    die("Csatlakozás sikertelen: " . $conn->connect_error);
} */
/* check for form stage */
$continue = $_POST['continue'] ?? 0;
/* stage 1 return --> check for input errors */
switch ($continue) {
    case '1':
        if(empty($_POST['title'])){
            /* no cim = no next page */
            $continue = 0;
            $ERROR = 1;
        }elseif($_POST['class'] == 0){
            /* didn't select class back to stage 1 */
            $continue = 0 ;
            $ERROR = 2;
        }elseif(empty($_POST['sketch'])){
            /* no sketch = no next page */
            $continue = 0;
            $ERROR = 3;
        }else{
            /* NO ERRORS, CONTINUE */
            $_SESSION['title'] = $_POST['title'];
            $_SESSION['class'] = $_POST['class'];
            $_SESSION['sketch'] = $_POST['sketch'];
        }
        break;
    case '2':
        /* NO ERRORS --> PROCEED WITH UPLOAD */
        $conn = new mysqli("localhost","root","", "tetelek"); //create conn
        $sql = "INSERT INTO tetelcimek (id, cim, vazlat, kidolgozas, modosit, tantargyid) VALUES (NULL,?,?,?,?,?);";
        $stmt = $conn->prepare($sql);
        $title = $_SESSION['title'];
        $sketch = $_SESSION['sketch'];
        $kidolg = $_POST['kidolg'];
        $date = date("Y-M-D");
        $classid = $_SESSION['class'];
        $stmt->bind_param("ssssi", $title, $sketch, $kidolg, $date, $classid);
        if($stmt->execute()==true){
            header("Location: index.php?addsuccess=true");
        }else{
            $ERROR = 5;
        }
        break;
    default:
        break;
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
        <?php if($ERROR): ?>
            <div class="toast" aria-live="assertive" aria-atomic="true" role="alert" data-delay="3000" style="position:fixed; top:30px; right:30px; z-index: 2;">
                <div class="toast-header">
                    <strong class="mr-auto">ERROR</strong>
                    <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                        &times;
                    </button>
                </div>
                <div class="toast-body">
                    <?php if($ERROR == 1): ?>
                        Kérem adjon meg egy címet!
                    <?php elseif($ERROR == 2):?>
                        Kérem válasszon ki egy tantárgyat!
                    <?php elseif($ERROR == 3):?>
                        Kérem adjon meg a vázlattot!
                    <?php elseif($ERROR == 4):?>
                        Kérem adja meg a kidolgozást!
                    <?php elseif($ERROR == 5):?>
                        Sikertelen hozzáadás...
                    <?php endif; ?>
                </div>
            </div>
        <?php endif;?>
        <?php if ($continue==0):?>
            <h2>Tétel hozzáadása</h2> <!-- goofy ahh font  -->
            <form class="addform" method="post">
                <label for="title">Tétel címe: </label>
                <input type="text" name="title" id="title" <?php if(!empty($_POST['title'])): echo 'value="'.$_POST['title'].'"'; endif;?>><br><br>
                <label for="class">Tantárgy</label>
                <select name="class" id="class">
                    <option value="0">Kérem válasszon egy tantárgyat</option>
                    <option value="1">Történelem</option>
                    <option value="2">Irodalom</option>
                    <option value="3">Nyelvtan</option>
                </select><br><br>
                <p style="line-height: 0;">Vázlat:</p>
                <textarea name="sketch" id="sketch"><?php if(!empty($_POST['sketch'])):echo $_POST['sketch'];endif;?></textarea>
                <input type="hidden" name="continue" value="1"><br>
                <button type="submit">Tovább</button>
            </form>
        <?php elseif($continue==1): ?>
            <h2>Kidolgozás hozzáadása</h2>
            <form class="addform" method="post">
                <textarea name="kidolg" id="kidolg"></textarea>
                <input type="hidden" name="continue" value="2"><br>
                <button type="submit">Feltöltés</button>
            </form>
        <?php endif;?>
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