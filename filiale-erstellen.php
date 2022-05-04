<?php
$siteTitle = 'Erstellen Filiale';
include_once './layouts/header.php';

//check if user is logged in 
$auth->loggedIn();
// create Inventartyp Object
$filiale = new Filiale();

if ($_SERVER["REQUEST_METHOD"] === 'POST' and isset($_POST["filiale"])) {
    $typ = htmlspecialchars($_POST["filiale"]);
    $filiale->saveFiliale($typ);
    header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
    exit();
}

?>


<div class="content-inner">
    <div class="form-wrapper">
        <h2>Filiale Anlegen</h2>
        <hr>
        <form action="" method="post">
            <div class="mb-3">
                <input type="text" name="filiale" placeholder="Bezeichnung der Filiale" required>
            </div>
            <button type=" submit" class="btn btn-primary">Anlegen</button>
        </form>
    </div>
</div>




<?php
include_once './layouts/footer.php';
