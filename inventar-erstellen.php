<?php
$siteTitle = 'Erstellen Inventar';
include_once './layouts/header.php';

// check if user is logged in 
$auth->loggedIn();

// create Inventartyp Object
$inventar = new Inventar();

// gather possible select options 
$typen = $inventar->getInventarTyp();
$abteilungen = $inventar->getAbteilung();
$filialen = $inventar->getFiliale();


if ($_SERVER["REQUEST_METHOD"] === "POST" and !empty($_POST)) {
    $values['name'] = htmlspecialchars($_POST["name"]);
    $values['typ'] = htmlspecialchars($_POST["typ"]);
    $values['datum'] = htmlspecialchars($_POST["datum"]);
    $values['preis'] = htmlspecialchars($_POST["preis"]);
    $values['dauer'] = htmlspecialchars($_POST["dauer"]);
    $values['abteilung'] = htmlspecialchars($_POST["abteilung"]);
    $values['filiale']  = htmlspecialchars($_POST["filiale"]);
    echo "<pre>";
    var_dump($values);
    echo "</pre>";
    $inventar->saveInventar($values);
    header("Location: {$_SERVER['REQUEST_URI']}", true, 303);
    exit();
}

?>
<div class="content-inner">
    <?php
    if (empty($typen) or empty($abteilungen) or empty($filialen)) {
        echo "<h2>Es fehlen Typ, Abteilung oder Filiale zum erzeugen eines Eintrages</h2>";
    } else {
    ?>
        <div class="form-wrapper">
            <h2>Inventar Anlegen</h2>
            <hr>
            <form action="" method="post">
                <input type="text" name="name" placeholder="Gegenstands Name" required>
                <select name="typ" required>
                    <option value="" selected hidden disabled>Wähle Typ</option>
                    <?php
                    foreach ($typen as $typ) {
                        echo "<option value=" . $typ["typ_name"] . ">" . $typ["typ_name"] . "</option>";
                    }
                    ?>
                </select>
                <input type="date" name="datum" placeholder="Datum der Anschaffung" max="<?php echo $currentDate ?>" value="<?php echo $currentDate ?>">
                <input type="number" min="0" step="0.01" name="preis" placeholder="Anschaffungspreis: 00.00" required>
                <input type="number" min="1" step="0" name="dauer" placeholder="Nutzungsdauer in Jahren" required>
                <select name="abteilung" required>
                    <option value="" selected hidden disabled>Wähle Abteilung</option>
                    <?php
                    foreach ($abteilungen as $abteilung) {
                        echo "<option value=" . $abteilung["abteilung"] . ">" . $abteilung["abteilung"] . "</option>";
                    }
                    ?>
                </select>
                <select name="filiale" required>
                    <option value="" selected hidden disabled>Wähle Filiale</option>
                    <?php
                    foreach ($filialen as $filiale) {
                        echo "<option value=" . $filiale["filiale"] . ">" . $filiale["filiale"] . "</option>";
                    }
                    ?>
                </select>
                <button type="submit" class="btn btn-primary">Anlegen</button>
            </form>
        </div>
    <?php
    }
    ?>
</div>




<?php
include_once './layouts/footer.php';
