<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
	include("db-json.php");
	$mySqlServer = $db_json_decode["server"];
	$mySqlUser = $db_json_decode["user"];
	$mySqlPass = $db_json_decode["pass"];
	$mySqlDBName = $db_json_decode["dbname"];
?>