<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">
    <title>Szóbeli tételek</title>
</head>
<body>
    <header>
        <h1>Tételek</h1>
        <nav class="navbar">
            <div class="nav-item item1"><a href="#tortdiv">Történelem</a></div>
            <div class="nav-item item2"><a href="#iroddiv">Irodalom</a></div>
            <div class="nav-item item3"><a href="#nyelvdiv">Nyelvtan</a></div>
        </nav>
    </header>
    <main>
        <a href="add.php" class="button">Új tétel felvétele...</a>
        <form class="search-container" method="get">
            <input type="text" placeholder="Search..." name="query">
            <button type="submit">Search</button>
        </form>
        <div class="listing" id="tortdiv">
            <?php
            if(!empty($_GET['query'])){
                $query = $_GET['query'];
                print $query;
            }
            ?>
            <h2>Történelem</h2>
            <ul>
                <li>aha</li>
                <li>aha</li>
                <li>aha</li>
                <li>aha</li>
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
</body>
</html>