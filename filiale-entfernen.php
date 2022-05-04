<?php
$siteTitle = 'Benutzer Übersicht';
include_once './layouts/header.php';

//check if user is logged in 
$auth->loggedIn();

// grab all users from db
$filiale = new Filiale();
$filialen = $filiale->getAll();

if ($_SERVER["REQUEST_METHOD"] === 'POST' and count($_POST) > 0) {
    $typ = implode($_POST);
    $filiale->deleteFiliale(htmlspecialchars($typ));
    header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
    exit();
}

?>

<div class="content-inner">
    <form id="show-form" action="" method="post">
        <table id="show-table">
            <h2>Liste aller Filialen</h2>
            <thead>
                <th>Filiale</th>
                <th>Löschen</th>
            </thead>
            <tbody>
                <?php
                foreach ($filialen as $filiale) {
                    $name = $filiale["filiale"];
                    echo <<<END
                    <tr>
                    <td>$name</td>
                    <td class="action"><button type="submit" value="$name" name="$name">Löschen</td>
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
