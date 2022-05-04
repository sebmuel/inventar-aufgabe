<?php
$siteTitle = 'Erstelle Abteilung';
include_once './layouts/header.php';

//check if user is logged in 
$auth->loggedIn();
// create Inventartyp Object
$abteilung = new Abteilung();

if ($_SERVER["REQUEST_METHOD"] === 'POST' and isset($_POST["abteilung"])) {
    $typ = htmlspecialchars($_POST["abteilung"]);
    $abteilung->saveAbteilung($typ);
    header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
    exit();
}

?>

<div class="content-inner">
    <div class="form-wrapper">
        <h2>Abteilung Anlegen</h2>
        <hr>
        <form action="" method="post">
            <div class="mb-3">
                <input type="text" name="abteilung" placeholder="Abteilung Bezeichnung" required>
            </div>
            <button type="submit" class="btn btn-primary">Anlegen</button>
        </form>
    </div>
</div>

<?php
include_once './layouts/footer.php';
