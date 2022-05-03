<?php

//load config
require_once './assets/config.php';

// load functions
require_once './assets/functions.php';

// load classes
require_once './assets/classes.php';

// start auth object
$auth = new Auth();

?>

<!doctype html>
<html lang="de">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link href="/assets/css/style.css" rel="stylesheet">
    <title><?php echo $siteTitle ?></title>
</head>

<body>
    <?php
    // render message template when message exists
    if (isset($_SESSION["message"])) {
        include_once 'message.php';
    }
    ?>
    <div class="jumbotron">

        <img src="<?php echo LOGO; ?>">

        <div>Aktuelle Seite: <?php echo $siteTitle; ?></div>
    </div>

    <div id="content">
        <div id="sidebar">
            <button><a href="../logout.php">Logout</a></button>
            <nav>
                <div class="has-dropdown">
                    <span>Bearbeite Inventartypen</span>
                    <div class="dropdown">
                        <div><a href="/inventartyp-erstellen.php">Inventartype Erstellen</a></div>
                        <div><a href="/inventartyp-entfernen.php">Inventartype Entfernen</a></div>
                    </div>
                </div>
                <div class="has-dropdown">
                    <span>Bearbeite Filialen</span>
                    <div class="dropdown">
                        <div><a href="/filiale-hinzufuegen.php">Filiale Hinzufügen</a></div>
                        <div><a href="/filiale-loeschen.php">Filiale Löschen</a></div>
                    </div>
                </div>
                <div class="has-dropdown">
                    <span>Bearbeite Abteilungen</span>
                    <div class="dropdown">
                        <div><a href="/abteilung-hinzufuegen.php">Abteilung Hinzufügen</a></div>
                        <div><a href="/abteilung-loeschen.php">Abteilung Löschen</a></div>
                    </div>
                </div>
                <div class="has-dropdown">
                    <span>Bearbeite Inventar</span>
                    <div class="dropdown">
                        <div><a href="/inventar-hinzufuegen.php">Inventar Hinzufügen</a></div>
                        <div><a href="/inventar-entfernen.php">Inventar Entfernen</a></div>
                    </div>
                </div>
                <div class="has-dropdown">
                    <span>Anzeigen Inventarliste</span>
                    <div class="dropdown">
                        <div><a href="/inventar-anzeigen.php">Inventar Anzeigen</a></div>
                    </div>
                </div>
                <div class="has-dropdown">
                    <span>Benutzerverwaltung</span>
                    <div class="dropdown">
                        <div><a href="/benutzer-hinzufuegen.php">Neuen Benutzer anlegen</a></div>
                        <div><a href="/benutzer-uebersicht.php">Nutzer Übersicht</a></div>
                    </div>
                </div>
            </nav>
        </div>