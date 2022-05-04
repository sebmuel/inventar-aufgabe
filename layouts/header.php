<?php
// load functions
require_once './assets/functions.php';
?>

<!doctype html>
<html lang="de">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&display=swap" rel="stylesheet">
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
        <img id="logo" src="<?php echo LOGO; ?>">
        <h2>
            <?php if (isset($_SESSION["username"])) {
                echo "Eingeloggt als: <span>" . $_SESSION["username"] . "</span>";
            }
            ?>
        </h2>
    </div>

    <div id="content">
        <div id="sidebar">
            <div class="sidebar-wrapper">
                <nav>
                    <div id="logout"><a href="../logout.php">Logout</a></div>
                    <div class="has-dropdown">
                        <span>Inventartypen</span>
                        <div class="dropdown">
                            <div><a href="/inventartyp-erstellen.php">Inventartype Erstellen</a></div>
                            <div><a href="/inventartyp-entfernen.php">Inventartype Entfernen</a></div>
                        </div>
                    </div>
                    <div class="has-dropdown">
                        <span>Filialen</span>
                        <div class="dropdown">
                            <div><a href="/filiale-erstellen.php">Filiale Hinzufügen</a></div>
                            <div><a href="/filiale-entfernen.php">Filiale Löschen</a></div>
                        </div>
                    </div>
                    <div class="has-dropdown">
                        <span>Abteilungen</span>
                        <div class="dropdown">
                            <div><a href="/abteilung-erstellen.php">Abteilung Hinzufügen</a></div>
                            <div><a href="/abteilung-entfernen.php">Abteilung Löschen</a></div>
                        </div>
                    </div>
                    <div class="has-dropdown">
                        <span>Inventar</span>
                        <div class="dropdown">
                            <div><a href="/inventar-erstellen.php">Inventar Hinzufügen</a></div>
                            <div><a href="/inventar-entfernen.php">Inventar Entfernen</a></div>
                        </div>
                    </div>
                    <div class="has-dropdown">
                        <span>Inventarliste</span>
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
        </div>