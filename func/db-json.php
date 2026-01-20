<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
	$db_json_dtls = array("server" => "localhost", "user" => "root", "pass" => "", "dbname" => "vtu_site");
	$db_json_encode = json_encode($db_json_dtls,true);
	$db_json_decode = json_decode($db_json_encode,true);
?>