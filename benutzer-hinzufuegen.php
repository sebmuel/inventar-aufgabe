<?php
$siteTitle = 'Benutzer Hinzufügen';
include_once './layouts/header.php';

//check if user is logged in 
$auth->loggedIn();

if ($_SERVER["REQUEST_METHOD"] === 'POST' and isset($_POST["username"]) and isset($_POST["password"])) {
    $username = htmlspecialchars($_POST["username"]);
    $password = htmlspecialchars($_POST["password"]);
    $auth->register($username, $password);
    header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
    exit();
}

?>

<div class="content-inner">
    <div class="form-wrapper">
        <h2>Nutzer Anlegen</h2>
        <hr>
        <form action="" method="post">
            <div class="mb-3">
                <input type="username" name="username" placeholder="Username" required>
            </div>
            <div class="mb-3">
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit" class="btn btn-primary">Anlegen</button>
        </form>
    </div>
</div>

<?php
include_once './layouts/footer.php';
