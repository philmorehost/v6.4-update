<?php session_start();
    //header("Content-Type", "application/json");
    include_once("func/bc-connect.php");
    include_once("func/bc-func.php");

    //Select Vendor Table
	$select_vendor_table = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_vendors WHERE website_url='".$_SERVER["HTTP_HOST"]."' LIMIT 1"));
	if(($select_vendor_table == true) && ($select_vendor_table["website_url"] == $_SERVER["HTTP_HOST"]) && ($select_vendor_table["status"] == 1)){
        $get_api_post_info = json_decode(file_get_contents('php://input'),true);
        
        $get_vendor_details = mysqli_fetch_array(mysqli_query($connection_server, "SELECT * FROM sas_vendors WHERE website_url='".$_SERVER["HTTP_HOST"]."' LIMIT 1"));
        $get_user_detail_via_username = mysqli_query($connection_server, "SELECT * FROM sas_users WHERE vendor_id='".$get_vendor_details["id"]."' && username='".$_SESSION["user_session"]."' LIMIT 1");
        $get_logged_user_details = mysqli_fetch_array($get_user_detail_via_username);
        if(mysqli_num_rows($get_user_detail_via_username) == 1){
            //User exists
            $random_paid = substr(str_shuffle("12345678901234567890"), 0, 15);
            $vendor_id = $get_logged_user_details["vendor_id"];
            $username = $get_logged_user_details["username"];
            mysqli_query($connection_server, "INSERT INTO sas_user_payment_checkouts (vendor_id, username, reference, status) VALUES ('$vendor_id', '$username', '$random_paid', '2')");
            $json_response_array = array("status" => "200", "text" => $random_paid);
            echo json_encode($json_response_array,true);
        }else{
        	//User not exists
        	$json_response_array = array("status" => "201", "text" => "User Not Exists");
        	echo json_encode($json_response_array,true);
        }
	}else{
		//Website not registered
		$json_response_array = array("status" => "201", "text" => "Website Not Registered");
		echo json_encode($json_response_array,true);
	}
?>