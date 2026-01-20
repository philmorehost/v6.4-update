<?php
error_reporting(E_ALL);
ini_set("display_errors", 1); session_start();
    unset($_SESSION["user_session"]);
    unset($_SESSION["reset-security-detail"]);
    header("Location: /web/Login.php");
?>