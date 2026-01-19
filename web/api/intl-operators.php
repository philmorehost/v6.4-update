<?php session_start();
include_once("../../func/bc-config.php");

if (isset($_GET["country_code"]) && isset($_GET["product_type_id"])) {
    $country_code = mysqli_real_escape_string($connection_server, $_GET["country_code"]);
    $product_type_id = mysqli_real_escape_string($connection_server, $_GET["product_type_id"]);
    $operators = [];
    $get_ops = mysqli_query($connection_server, "SELECT operator_id, operator_name FROM sas_intl_airtime_operators WHERE vendor_id='".$get_logged_user_details["vendor_id"]."' AND country_code='$country_code' AND product_type_id='$product_type_id' AND status='1' ORDER BY operator_name ASC");
    while($row = mysqli_fetch_assoc($get_ops)){
        $operators[] = $row;
    }
    header('Content-Type: application/json');
    echo json_encode($operators);
}
?>