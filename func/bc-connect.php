<?php
	// error_reporting(E_ALL);
	// ini_set('display_errors', 1);
	// ini_set('display_startup_errors', 1);
	error_reporting(0);

	mysqli_report(MYSQLI_REPORT_OFF);

	date_default_timezone_set('Africa/Lagos');
	include_once("db-dtl.php");
	include_once("bc-mailer.php");
	include_once("email-design.php");

	$connection = mysqli_connect($mySqlServer,$mySqlUser,$mySqlPass);
	
	if($connection){
		if(mysqli_query($connection,"CREATE DATABASE IF NOT EXISTS ".$mySqlDBName)){
			/*echo "DB Created Successfully";*/
		}
	}else{
		/*echo mysqli_connect_error($connection);*/
	}
	
	$connection_server = mysqli_connect($mySqlServer,$mySqlUser,$mySqlPass,$mySqlDBName);
	if(!$connection_server){
		// echo mysqli_connect_error();
	}
	
	// Redirection for www
	$get_requested_website_domain_url = $_SERVER["HTTP_HOST"];
	if (str_starts_with($get_requested_website_domain_url, 'www.')) {
		$non_www_host = substr($get_requested_website_domain_url, 4);
		$protocol = (isset($_SERVER["HTTPS"]) && ($_SERVER["HTTPS"] == "on")) ? "https://" : "http://";
		header("Location: " . $protocol . $non_www_host . $_SERVER["REQUEST_URI"]);
		exit();
	}
?>