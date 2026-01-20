<?php
error_reporting(E_ALL);
ini_set("display_errors", 1); session_start();
    unset($_SESSION["admin_session"]);
    unset($_SESSION["spadmin_vendor_auth"]);
    header("Location: /bc-admin/Login.php");
?>