<?php
$siteTitle = 'Login';
include_once './layouts/header.php';


if ((isset($_SESSION['error']))) {
    echo $_SESSION['error'];
}

// user already logged in
if (isset($_SESSION["logged_in"]) and $_SESSION["logged_in"] === true) {
    header("Location: " . ENTRY);
    // user tries to login
} else {
    if ($_POST) {
        $logged = $auth->login(htmlspecialchars($_POST["username"]), htmlspecialchars($_POST["password"]));
        header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
        exit();
        if ($logged) {
            header("Location: " . ENTRY);
        }
    }
}

?>


<div class="content-inner">
    <div class="form-wrapper">
        <h2>Login</h2>
        <form action="" method="post">
            <div class="mb-3">
                <input type="username" name="username" placeholder="Username">
            </div>
            <div class="mb-3">
                <input type="password" name="password" placeholder="Password">
            </div>
            <button type="submit" class="btn btn-primary">Absenden</button>
        </form>
    </div>
</div>

<?php
include_once './layouts/footer.php';
