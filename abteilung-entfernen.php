<?php
$siteTitle = 'Abteilung Übersicht';
include_once './layouts/header.php';

//check if user is logged in 
$auth->loggedIn();

// grab all users from db
$abteilung = new Abteilung();
$records = $abteilung->getAll();

if ($_SERVER["REQUEST_METHOD"] === 'POST' and isset($_POST["delete"])) {
    $typ = implode($_POST);
    $abteilung->deleteAbteilung(htmlspecialchars($typ));
    header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
    exit();
}
?>
<div class="content-inner">
    <?php
    if (empty($records)) {
        echo "<h2>Keine Einträge vorhanden</h2>";
    } else {
    ?>
        <form class="small-table" id="show-form" action="" method="post">
            <h2>Liste aller Abteilungen: </h2>
            <table id="show-table">
                <thead>
                    <th>Abteilung</th>
                    <th>Löschen</th>
                </thead>
                <tbody>
                    <?php
                    foreach ($records as $record) {
                        $typ = $record["abteilung"];
                        echo <<<END
                    <tr>
                    <td>$typ</td>
                    <td class="action"><button type="submit" value="$typ" name="delete">Löschen</td>
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
