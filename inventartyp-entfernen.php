<?php
$siteTitle = 'Benutzer Übersicht';
include_once './layouts/header.php';

//check if user is logged in 
$auth->loggedIn();

// grab all users from db
$inventartyp = new InventarTypen();
$inventartypenList = $inventartyp->getAll();

if ($_SERVER["REQUEST_METHOD"] === 'POST' and count($_POST) > 0) {
    $typ = implode($_POST);
    $inventartyp->deleteType(htmlspecialchars($typ));
    header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
    exit();
}

?>

<div class="content-inner">
    <form id="show-form" action="" method="post">
        <table id="show-table">
            <thead>
                <th>Inventartyp</th>
                <th>Löschen</th>
            </thead>
            <tbody>
                <?php
                foreach ($inventartypenList as $inventartypItem) {
                    $typ = $inventartypItem["typ_name"];
                    echo <<<END
                    <tr>
                    <td>$typ</td>
                    <td><button type="submit" value="$typ" name="$typ">Löschen</td>
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
