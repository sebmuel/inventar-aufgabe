<?php


// Debug Values comment out if dont needed
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// logo path
define('LOGO', "/assets/media/logo-platzhalter.png");

// db credentials

define('HOSTNAME', "localhost");
define('DATABASENAME', "inventarisierung");
define('DATABASEUSER', "");
define('DATABASEPASSWORD', "");

// determine entry point when logged in
define('ENTRY', '/inventartyp-erstellen.php');

// define min password length
define('PWLENGTH', 6);
// define min username length
define("USERLENGTH", 4);

// define min values for
// Inventartyp
// Filialen
// Abteilungen
define("MINLENGTH", 2);
