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
    <?php
    // when no records where found print message instead of empty table
    if (empty($inventartypenList)) {
        echo "<h2>Keine Einträge vorhanden</h2>";
    } else {
    ?>
        <form id="show-form" action="" method="post">
            <h2>Liste aller Inventartypen: </h2>
            <table class="small-table" id="show-table">
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
                    <td class="action"><button type="submit" value="$typ" name="$typ">Löschen</td>
                    </tr>
                    END;
                    }
                    ?>
                </tbody>
            </table>
        </form>
    <?php
    }
    ?>
</div>

<?php
include_once './layouts/footer.php';
