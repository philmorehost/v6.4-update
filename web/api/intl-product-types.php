<?php session_start();
include_once("../../func/bc-config.php");

if (isset($_GET["country_code"])) {
    $country_code = mysqli_real_escape_string($connection_server, $_GET["country_code"]);
    $types = [];
    $get_types = mysqli_query($connection_server, "SELECT DISTINCT product_type_id, product_type_name FROM sas_intl_airtime_operators WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' AND country_code='$country_code' AND status='1' ORDER BY product_type_name ASC");
    while($row = mysqli_fetch_assoc($get_types)){
        $types[] = $row;
    }
    header('Content-Type: application/json');
    echo json_encode($types);
}
?>
