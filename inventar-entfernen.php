<?php
$siteTitle = 'Inventar Entfernen';
include_once './layouts/header.php';

//check if user is logged in 
$auth->loggedIn();

// create Inventartyp Object
$inventar = new Inventar();

// gather possible select options 
$typen = $inventar->getInventarTyp();
$abteilungen = $inventar->getAbteilung();
$filialen = $inventar->getFiliale();

if ($_SERVER["REQUEST_METHOD"] === "POST" and count($_POST) > 0) {
    // if those POST variables are set we render table
    if (isset($_POST["typ"]) and isset($_POST["abteilung"]) and isset($_POST["filiale"])) {
        $typ = htmlspecialchars($_POST["typ"]);
        $abteilung = htmlspecialchars($_POST["abteilung"]);
        $filiale = htmlspecialchars($_POST["filiale"]);
        $records = $inventar->getRecords($typ, $abteilung, $filiale);
        // otherwise we can assume the post-request was made to delete a record
    } elseif (isset($_POST["delete"])) {
        $inventar->deleteRecord(htmlspecialchars($_POST["delete"]));
        header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
        exit();
    }
} else {
    $records = $inventar->getRecords("%%", "%%", "%%");
}

?>

<div class="content-inner">
    <?php
    if (empty($records)) {
        echo "<h2>Keine Einträge vorhanden</h2>";
    } else {
    ?>
        <h2>Wähle Kritierien aus: </h2>
        <form id="show-form" action="" method="post" class="choose-form">
            <select name="typ" required>
                <option value="%%" selected>alle Typen</option>
                <?php
                foreach ($typen as $typ) {
                    echo "<option value=" . $typ["typ_name"] . ">" . $typ["typ_name"] . "</option>";
                }
                ?>
            </select>
            <select name="abteilung" required>
                <option value="%%" selected>alle Abteilungen</option>
                <?php
                foreach ($abteilungen as $abteilung) {
                    echo "<option value=" . $abteilung["abteilung"] . ">" . $abteilung["abteilung"] . "</option>";
                }
                ?>
            </select>
            </select>
            <select name="filiale" required>
                <option value="%%" selected>alle Filialen</option>
                <?php
                foreach ($filialen as $filiale) {
                    echo "<option value=" . $filiale["filiale"] . ">" . $filiale["filiale"] . "</option>";
                }
                ?>
            </select>
            <button type="submit" class="btn btn-primary">Suchen</button>
        </form>
        <table id="show-table">
            <form method="post" action="">
                <h2>Vorhandene Gegenstände Inventar</h2>
                <thead>
                    <th>Inventar Name</th>
                    <th>Typ</th>
                    <th>Datum</th>
                    <th>Anschaffungspreis</th>
                    <th>Nutzungsdauer</th>
                    <th>Filiale</th>
                    <th>Abteilung</th>
                    <th>Löschen</th>
                </thead>
                <tbody>

                    <?php
                    foreach ($records as $record) {
                        $id = $record["i_id"];
                        $name = $record["name"];
                        $typ = $record["typ"];
                        $datum = $record["buy_date"];
                        $preis = $record["buy_price"];
                        $dauer = $record["dauer"];
                        $filiale = $record["filiale"];
                        $abteilung = $record["abteilung"];
                        echo <<<END
                     <tr>
                     <td>$name</td>
                     <td>$typ</td>
                     <td>$datum</td>
                     <td>$preis €</td>
                     <td>$dauer Jahre</td>
                     <td>$filiale</td>
                     <td>$abteilung</td>
                     <td class="action"><button type="submit" value="$id" name="delete">Löschen</td>
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
