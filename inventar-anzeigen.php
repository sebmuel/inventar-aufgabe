<?php
$siteTitle = 'Inventar Anzeigen';
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
} else {
    $records = $inventar->getRecords("%%", "%%", "%%");
}

?>

<div class="content-inner">
    <h2>Wähle Kritierien aus: </h2>
    <form id="show-form" action="" method="post" class="choose-form form-wrapper">
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
        <input type="number" name="min" steps="0.01" min="0" placeholder="Restwert größer als" value="0">
        <input type="number" name="max" steps="0.01" placeholder="Restwert kleiner als" value="%%">
        <button type="submit" class="btn btn-primary">Suchen</button>
    </form>
    <table id="show-table">
        <form method="post" action="">
            <h2>Liste Inventar</h2>
            <thead>
                <th>Gegenstands Name</th>
                <th>Typ</th>
                <th>Datum</th>
                <th>Anschaffungspreis</th>
                <th>Restwert</th>
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
                    $restwert = $inventar->residualValue($datum, $preis, $dauer);
                    $filiale = $record["filiale"];
                    $abteilung = $record["abteilung"];

                    echo <<<END
                     <tr>
                     <td>$name</td>
                     <td>$typ</td>
                     <td>$datum</td>
                     <td>$preis €</td>
                     <td>$restwert €</td>
                     <td>$dauer Jahre</td>
                     <td>$filiale</td>
                     <td>$abteilung</td>
                     <td class="action"><button type="submit" value="$id" name="delete">Löschen</td>
                     </tr>
                     END;
                }
                ?>
            </tbody>
            <tfoot>
                <tr><?php echo $inventar->summeRestwert; ?></tr>
            </tfoot>
    </table>
    </form>
</div>

<?php
include_once './layouts/footer.php';
