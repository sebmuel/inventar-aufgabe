<?php
$siteTitle = 'Abteilung Übersicht';
include_once './layouts/header.php';

//check if user is logged in 
$auth->loggedIn();

// grab all users from db
$abteilung = new Abteilung();
$abteilungsListe = $abteilung->getAll();

if ($_SERVER["REQUEST_METHOD"] === 'POST' and count($_POST) > 0) {
    $typ = implode($_POST);
    $abteilung->deleteAbteilung(htmlspecialchars($typ));
    header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
    exit();
}

?>

<div class="content-inner">
    <form id="show-form" action="" method="post">
        <h2>Liste aller Abteilungen: </h2>
        <table id="show-table">
            <thead>
                <th>Abteilung</th>
                <th>Löschen</th>
            </thead>
            <tbody>
                <?php
                foreach ($abteilungsListe as $abteilung) {
                    $typ = $abteilung["abteilung"];
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
</div>

<?php
include_once './layouts/footer.php';
