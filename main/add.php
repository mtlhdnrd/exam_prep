<?php
session_start();
$ERROR = 0;
/* check for form stage */
$continue = $_POST['continue'] ?? 0;
/* get classes for select */
$conn = new mysqli("localhost","root","", "tetelek"); //create conn
$classes = [];
$sql = "SELECT * FROM tantargyak;";
if($conn->query($sql)){
    $result = $conn->query($sql);
    if($result->num_rows>0){
        while($row = $result->fetch_assoc()){
            $classes[] = $row;
        }
    }
}

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
        // Check connection
        if ($conn->connect_error) {
            die("Csatlakozás sikertelen: " . $conn->connect_error);
        }
        $sql = "INSERT INTO tetelcimek (cim, vazlat, kidolgozas, modosit, tantargyid) VALUES (?,?,?,?,?);";
        $stmt = $conn->prepare($sql);
        $title = $_SESSION['title'];
        $sketch = $_SESSION['sketch'];
        $kidolg = $_POST['kidolg'];
        $date = date("Y-m-d");
        $classid = $_SESSION['class'];
        $stmt->bind_param("ssssi", $title, $sketch, $kidolg, $date, $classid);
        if($stmt->execute()){
            session_unset();
            $_SESSION['addsuccess'] = true;
            session_write_close();
            header("Location: index.php");
            exit();
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
    <body class="tetelpage">
        <header class="full_head">
            <a href="index.php"><h1>Tételek</h1></a>
        </header>
    <main class="tetel_main">
        <div class="listing">
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
                        currently isnt used
                    <?php elseif($ERROR == 5):?>
                        Sikertelen hozzáadás...
                    <?php endif; ?>
                </div>
            </div>
        <?php endif;?>
        <?php if ($continue==0):?>
            <h1>Tétel hozzáadása</h1> 
            <form class="form" method="post">
                <label for="title"><h3>Tétel címe: </h3></label>
                <br>
                <input type="text" name="title" id="title" <?php if(!empty($_POST['title'])): echo 'value="'.$_POST['title'].'"'; elseif(!empty($_SESSION['title'])): echo 'value="'.$_SESSION['title'].'"'; endif;?>><br><br>
                <label for="class"><h3>Tantárgy</h3></label>
                <br>
                <?php if($classes): ?>
                    <select name="class" id="class">
                        <option value="0" disabled selected="selected">Kérem válasszon egy tantárgyat</option>
                        <?php foreach($classes as $class){                            
                            echo '<option value="'.$class['id'].'">'.$class['tantargy'].'</option>';
                        }?>
                    </select><br><br>
                <?php endif;?>
                <p style="line-height: 0;">Vázlat:</p>
                <textarea name="sketch" id="sketch"><?php if(!empty($_POST['sketch'])):echo $_POST['sketch']; elseif(!empty($_SESSION['sketch'])): echo $_SESSION['sketch']; endif;?></textarea>
                <input type="hidden" name="continue" value="1"><br><br>
                <button type="submit" class="button">Tovább</button>
            </form>
        <?php elseif($continue==1): ?>
            <h2>Kidolgozás hozzáadása</h2>
            <form class="addform" method="post">
                <textarea name="kidolg" id="kidolg"></textarea>
                <input type="hidden" name="continue" value="2"><br>
                <button type="submit" name="continue" value="0">Vissza</button>
                <button type="submit">Feltöltés</button>
            </form>
        <?php endif;?>
        </div>
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