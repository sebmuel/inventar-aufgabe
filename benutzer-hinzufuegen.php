<?php
$siteTitle = 'Benutzer Hinzufügen';
include_once './layouts/header.php';

//check if user is logged in 
$auth->loggedIn();

if ($_SERVER["REQUEST_METHOD"] === 'POST' and isset($_POST["username"]) and isset($_POST["password"])) {
}

?>

<div class="content-inner">
    <div class="form-wrapper">
        <h2>Nutzer Anlegen</h2>
        <form action="" method="post">
            <div class="mb-3">
                <input type="username" name="username" placeholder="Username">
            </div>
            <div class="mb-3">
                <input type="password" name="password" placeholder="Password">
            </div>
            <button type="submit" class="btn btn-primary">Anlegen</button>
        </form>
    </div>
</div>

<?php
include_once './layouts/footer.php';
