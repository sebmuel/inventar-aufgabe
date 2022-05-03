<?php
$siteTitle = 'Benutzer Übersicht';
include_once './layouts/header.php';

//check if user is logged in 
$auth->loggedIn();
// grab all users from db
$users = $auth->listUsers();

if ($_SERVER["REQUEST_METHOD"] === 'POST' and count($_POST) > 0) {
    // get key and make it string
    $key = implode(array_keys($_POST));
    // sanitize
    $key = htmlspecialchars($key);
    $value = implode(array_values($_POST));

    // check what method needs to be executed
    switch ($key) {
        case "delete":
            $auth->deleteUser($value);
            break;
        case "lock":
            $auth->setLoginAttemps($value, "3");
            break;
        case "unlock":
            $auth->setLoginAttemps($value, "0");
            break;
    }
    header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
    exit();
}

?>

<div class="content-inner">
    <form id="show-form" action="" method="post">
        <table id="show-table">
            <thead>
                <th>Nutzer</th>
                <th>Login Versuche</th>
                <th>Status</th>
                <th>Löschen</th>
                <th>Sperren</th>
                <th>Entsperren</th>
            </thead>
            <tbody>
                <?php
                foreach ($users as $user) {
                    $username  = $user["username"];
                    $loginAttemps = $user["login_attepms"];
                    $status = ($loginAttemps >= 3) ? $status = "locked" : $status = "active";
                    echo <<<END
                    <tr>
                        <td>$username</td>
                        <td>$loginAttemps</td>
                        <td>$status</td>
                        <td><button type="submit" name="delete" value="$username">Löschen</td>
                        <td><button type="submit" name="lock" value="$username">Sperren</td>
                        <td><button type="submit" name="unlock" value="$username">Entsperren</td>
                    </tr>
                    END;
                }
                ?>
            </tbody>
        </table>
    </form>
</div>

<?php
include_once './layouts/footer.php';
