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
if ($_SERVER["REQUEST_METHOD"] === "POST" and !empty($_POST)) {
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
    // with no parameters given show all
    $records = $inventar->getRecords("%%", "%%", "%%");
}
// set "restwert" key to array
$validRecords = array();

for ($i = 0; $i < count($records); ++$i) {
    $restwert = $inventar->residualValue($records[$i]["buy_date"], $records[$i]["buy_price"], $records[$i]["dauer"]);
    $min = htmlspecialchars($_POST["min"]);
    $max = htmlspecialchars($_POST["max"]);
    $min = number_format((float)$min, 2);
    echo gettype($min);
    echo $restwert;
    if ($restwert > $min or $restwert < $max) {
        $records[$i]["restwert"] = $restwert;
        array_push($validRecords, $records[$i]);
    }
}

echo "<pre>";
var_dump($validRecords);
echo "</pre>";
?>

<div class="content-inner">
    <?php
    if (empty($records)) {
        echo "<h2>Keine Einträge vorhanden</h2>";
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
            <input type="number" name="max" steps="0.01" placeholder="Restwert kleiner als" value="99999999999999">
            <button type="submit" class="btn btn-primary">Suchen</button>
        </form>
        <form action="" method="post" class="form-wrapper spacer">
            <input type="text" name="search" placeholder="Geben sie einen Begriff ein zum suchen: ">
            <button type="submit">Suchen</button>
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
                        $restwert = $record["restwert"];
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

        </table>

        <div class="table-information">
            <span>
                Summe Restwerte:
            </span>
            <span>
                <?php
                echo number_format((float)$inventar->summeRestwert, 2) . "€";
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
