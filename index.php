<?php
$siteTitle = 'Login';
include_once './layouts/header.php';



// user already logged in -> redirect to ENTRY Page
if (isset($_SESSION["logged_in"]) and $_SESSION["logged_in"] === true) {
    header("Location: " . ENTRY);

    // user tries to login
} else {
    if ($_POST) {
        // filter var MIGHT be redundant in PHP 8.1
        // sanatize input
        $username = htmlspecialchars($_POST["username"]);
        $password = htmlspecialchars($_POST["password"]);
        $auth->login($username, $password);
        header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
        exit();
    }
}

?>


<div class="content-inner">
    <div class="form-wrapper get">
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
