<?php


// Debug Values comment out if dont needed
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// config
define('LOGO', "/assets/media/logo-platzhalter.png");
define('HOSTNAME', "localhost");
define('DATABASENAME', "inventarisierung");
define('DATABASEUSER', "seb");
define('DATABASEPASSWORD', "Hallodu123");

// determine entry point when logged in
define('ENTRY', '/inventartyp-erstellen.php');

// define min password length
define('PWLENGTH', 6);
