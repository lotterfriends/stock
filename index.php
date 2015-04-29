<?php
mb_internal_encoding("UTF-8");

// SQL Data
require_once "mysql.inc.php";
@mysql_connect($sql_host,$sql_benutzer,$sql_passwort) or die ("Fehler: Verbindung zur MySQL Server nicht möglich!");
@mysql_select_db($sql_dbname) or die ("Fehler: Verbindung zur Datenbank nicht möglich!");

// Service
require_once "functions.inc.php";

$action = cleanGet('action');
$page = cleanGet('page');
$load = cleanGet('load');

require_once "lib/template.php";
$templateUrl = "templates/";

require_once 'stock.php';

if (!isset($page) || empty($page) || $page == 'home') {
	showAllPlaces();
}

?>