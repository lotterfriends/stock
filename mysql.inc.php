<?php

$online = (getenv('REMOTE_ADDR') != '127.0.0.1');

// $online = false;

if ($online) {

	// Datenbank Host
	
	$sql_host = "localhost";
	
	// Datenbank Name
	
	$sql_dbname = "";
	
	// Datenbank Benutzername
	
	$sql_benutzer = "";
	
	// Datenbank Passwort
	
	$sql_passwort = "";

} else {

	// Datenbank Host
	
	$sql_host = "localhost";
	
	// Datenbank Name
	
	$sql_dbname = "stock";
	
	// Datenbank Benutzername	
	
	$sql_benutzer = "root";
	
	// Datenbank Passwort
	
	$sql_passwort = "root";

}
?>