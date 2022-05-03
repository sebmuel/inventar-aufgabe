<?php
$siteTitle = 'Erstellen Inventartyp';
include_once './layouts/header.php';

//check if user is logged in 
$auth->loggedIn();
// create Inventartyp Object
$inventartypen = new InventarTypen();

?>





<?php
include_once './layouts/footer.php';
