<?php

// start new session
session_start();

// load classes
require_once './assets/classes.php';

// create auth object
$auth = new Auth();

//test db connection
try {
    $conn = ConnectDb::getDbObject();
    // destroy that test object
    $conn = null;
} catch (PDOException $e) {
    // if connection fails we exit the programm and tell the user to check the config file
    // also destroy the stession so we force a logout 
    session_destroy();
    exit($e->getMessage() . "<br> Datenbank Verbindung fehlgeschlagen. Prüfen sie die Config Datei!");
}
