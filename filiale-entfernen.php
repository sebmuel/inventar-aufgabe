<?php
$siteTitle = 'Benutzer Übersicht';
include_once './layouts/header.php';

//check if user is logged in 
$auth->loggedIn();

// grab all users from db
$filiale = new Filiale();
$records = $filiale->getAll();

if ($_SERVER["REQUEST_METHOD"] === 'POST' and isset($_POST["delete"])) {
    $typ = implode($_POST);
    $filiale->deleteFiliale(htmlspecialchars($typ));
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
            <table id="show-table">
                <h2>Liste aller Filialen</h2>
                <thead>
                    <th>Filiale</th>
                    <th>Löschen</th>
                </thead>
                <tbody>
                    <?php
                    foreach ($records as $record) {
                        $name = $record["filiale"];
                        echo <<<END
                    <tr>
                    <td>$name</td>
                    <td class="action"><button type="submit" value="$name" name="delete">Löschen</td>
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
