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

if ($_SERVER["REQUEST_METHOD"] == 'POST') {
    $inventar->entries();
    $records = $inventar->entries;
    $filteredRecords = $inventar->filter($_POST, $records);
    $records = $filteredRecords;
} else {
    $inventar->entries();
    $records = $inventar->entries;
}
// calc the sum of all "Restwerte"
$summeRestwerte = $inventar->calcTotal();
?>

<div class="content-inner">
    <?php
    if (empty($records)) {
        echo <<<END
        <h2>Keine Einträge vorhanden</h2>
        END;
    } else {
    ?>
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
            <input type="number" name="max" steps="0.01" placeholder="Restwert kleiner als" value="<?php echo floatval($summeRestwerte); ?>">
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
                </thead>
                <tbody>

                    <?php
                    foreach ($records as $record) {
                        $name = $record->name;
                        $typ = $record->typ;
                        $datum = $record->anschaffungsDatum;
                        $preis = number_format((float)$record->anschaffungsPreis, 2);
                        $dauer = $record->dauer;
                        $restwert = number_format((float)$record->restwert, 2);
                        $filiale = $record->filiale;
                        $abteilung = $record->abteilung;

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
                     </tr>
                     END;
                    }
                    ?>
                </tbody>

        </table>

        <div class="table-information">
            <span>
                Summe Restwerte:
            </span>
            <span>
                <?php
                echo number_format((float)$summeRestwerte, 2) . "€";
                ?>
            </span>
        </div>
        </form>

    <?php
    }
    ?>
</div>

<?php
include_once './layouts/footer.php';
