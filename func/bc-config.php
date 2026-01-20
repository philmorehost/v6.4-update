<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
if (isset($_SERVER["HTTPS"]) && ($_SERVER["HTTPS"] == "on")) {
	$web_http_host = "https://" . $_SERVER["HTTP_HOST"];
} else {
	$web_http_host = "http://" . $_SERVER["HTTP_HOST"];
}

include("bc-connect.php");
include("bc-func.php");
include("bc-tables.php");

if ($connection && $connection_server) {
	$checkmate_super_admin_table_exists = mysqli_query($connection_server, "SELECT * FROM sas_super_admin");
	if ($checkmate_super_admin_table_exists && mysqli_num_rows($checkmate_super_admin_table_exists) >= 1) {
		//Unset Admin To User Redirect 
		unset($_SESSION["admin_to_user_redirect"]);

		//Select Vendor Table
		$select_vendor_table_res = mysqli_query($connection_server, "SELECT * FROM sas_vendors WHERE website_url='" . $_SERVER["HTTP_HOST"] . "' LIMIT 1");
		$select_vendor_table = ($select_vendor_table_res) ? mysqli_fetch_array($select_vendor_table_res) : false;

		if (($select_vendor_table == true) && ($select_vendor_table["website_url"] == $_SERVER["HTTP_HOST"]) && ($select_vendor_table["status"] == 1)) {
			if (isset($_SESSION["user_session"])) {
				$get_vendor_details = $select_vendor_table;
				$get_logged_user_query = mysqli_query($connection_server, "SELECT * FROM sas_users WHERE vendor_id='" . $get_vendor_details["id"] . "' && username='" . $_SESSION["user_session"] . "' LIMIT 1");
				if ($get_logged_user_query && mysqli_num_rows($get_logged_user_query) == 1) {
					$get_logged_user_details = mysqli_fetch_array($get_logged_user_query);
					if (($get_logged_user_details["status"] == 1) || (isset($_SESSION["admin_session"]))) {
						$config_get_active_billing_details = mysqli_query($connection_server, "SELECT * FROM sas_vendor_billings WHERE date >= '" . $get_vendor_details["reg_date"] . "' ORDER BY date DESC");
						$billing_end_date_array = array();
						if ($config_get_active_billing_details && mysqli_num_rows($config_get_active_billing_details) >= 1) {
							$config_billing_ending_date_array = array();
							while ($config_active_billing = mysqli_fetch_assoc($config_get_active_billing_details)) {
								$config_get_paid_bill_details = mysqli_query($connection_server, "SELECT * FROM sas_vendor_paid_bills WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && bill_id='" . $config_active_billing["id"] . "'");
								if ($config_get_paid_bill_details && mysqli_num_rows($config_get_paid_bill_details) == 0) {
									array_push($config_billing_ending_date_array, $config_active_billing["ending_date"]);
								}
							}

							foreach ($config_billing_ending_date_array as $config_ending_dates) {
								if (strtotime(date("Y-m-d")) > strtotime($config_ending_dates)) {
									array_push($billing_end_date_array, "1");
								} else {
									array_push($billing_end_date_array, "2");
								}
							}
						}
						if (in_array("1", $billing_end_date_array)) {
							header("Location: /web/Suspended.php");
						} else {
							$config_kyc_verification_status_array = array();
							$config_kyc_verification_status_array_value = array();
							foreach (array("bvn", "nin") as $config_verification_name) {
								$config_get_verification_details_res = mysqli_query($connection_server, "SELECT * FROM sas_kyc_verifications WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && verification_name='$config_verification_name'");
								$config_get_verification_details = ($config_get_verification_details_res) ? mysqli_fetch_array($config_get_verification_details_res) : false;
								if ($config_get_verification_details && in_array($config_get_verification_details["status"], array(1, 2))) {
									if ($config_get_verification_details["status"] == 1) {
										array_push($config_kyc_verification_status_array, "1");
										array_push($config_kyc_verification_status_array_value, strtoupper($config_verification_name));
									} else {
										array_push($config_kyc_verification_status_array, "2");
									}
								}
							}

							$config_check_bvn_verification = false;
							if (in_array("BVN", $config_kyc_verification_status_array_value)) {
								if ((!empty($select_vendor_table["bvn"]) && is_numeric($select_vendor_table["bvn"]) && (strlen($select_vendor_table["bvn"]) == 11)) || (!empty($get_logged_user_details["bvn"]) && is_numeric($get_logged_user_details["bvn"]) && (strlen($get_logged_user_details["bvn"]) == 11))) {
									$config_check_bvn_verification = false;
								} else {
									$config_check_bvn_verification = true;
								}
							}

							$config_check_nin_verification = false;
							if (in_array("NIN", $config_kyc_verification_status_array_value)) {
								if ((!empty($select_vendor_table["nin"]) && is_numeric($select_vendor_table["nin"]) && (strlen($select_vendor_table["nin"]) == 11)) || (!empty($get_logged_user_details["nin"]) && is_numeric($get_logged_user_details["nin"]) && (strlen($get_logged_user_details["nin"]) == 11))) {
									$config_check_nin_verification = false;
								} else {
									$config_check_nin_verification = true;
								}
							}

							if (!in_array("1", $config_kyc_verification_status_array)) {
								if (($config_check_bvn_verification == false) && ($config_check_nin_verification == false)) {
									$config_all_user_credit_transaction = 0;
									$config_get_all_user_credit_transaction_details = ($connection_server) ? mysqli_query($connection_server, "SELECT * FROM sas_transactions WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' && username='" . $get_logged_user_details["username"] . "' && (type_alternative LIKE '%credit%' OR type_alternative LIKE '%received%' OR type_alternative LIKE '%commission%')") : false;
									if ($config_get_all_user_credit_transaction_details && mysqli_num_rows($config_get_all_user_credit_transaction_details) >= 1) {
										while ($config_transaction_record = mysqli_fetch_assoc($config_get_all_user_credit_transaction_details)) {
											$config_all_user_credit_transaction += $config_transaction_record["discounted_amount"];
										}
										$config_user_total_funding = $config_all_user_credit_transaction;
									} else {
										$config_user_total_funding = 0;
									}
									$config_user_minimum_funding_details_res = mysqli_query($connection_server, "SELECT * FROM sas_user_minimum_funding WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "' LIMIT 1");
									$config_user_minimum_funding_details = ($config_user_minimum_funding_details_res) ? mysqli_fetch_array($config_user_minimum_funding_details_res) : false;
									$config_min_funding_amount = $config_user_minimum_funding_details["min_amount"] ?? 0;

									if ($config_user_total_funding >= $config_min_funding_amount) {
										if (in_array(explode("?", trim($_SERVER["REQUEST_URI"]))[0], array("/web/Login.php", "/web/Register.php", "/web/PasswordRecovery.php"))) {
											if (isset($_COOKIE["security_answer"]) && ($_COOKIE["security_answer"] == ($get_logged_user_details["security_answer"] ?? ''))) {
												header("Location: /web/Dashboard.php");
											} else {
												if (!in_array(explode("?", trim($_SERVER["REQUEST_URI"]))[0], array("/web/SecurityQuest.php"))) {
													header("Location: /web/SecurityQuest.php");
												}
											}
										} else {
											if (isset($_COOKIE["security_answer"]) && ($_COOKIE["security_answer"] == ($get_logged_user_details["security_answer"] ?? ''))) {
												if (in_array(explode("?", trim($_SERVER["REQUEST_URI"]))[0], array("/web/SecurityQuest.php"))) {
													header("Location: /web/Dashboard.php");
												}
											} else {
												if (!in_array(explode("?", trim($_SERVER["REQUEST_URI"]))[0], array("/web/SecurityQuest.php"))) {
													header("Location: /web/SecurityQuest.php");
												}
											}
										}
									} else {
										$json_response_array = array("desc" => "Dear " . ucwords($get_logged_user_details["firstname"] ?? 'User') . ", Your total amount funded was N" . $config_user_total_funding . ", kindly fund your wallet with minimum of N" . ($config_min_funding_amount - $config_user_total_funding) . " to unlock website feature. Thank you.");
										$json_response_encode = json_encode($json_response_array, true);
										$json_response_decode = json_decode($json_response_encode, true);
										if (isset($_SESSION["product_purchase_response"])) {
											if ($_SESSION["product_purchase_response"] !== $json_response_decode["desc"]) {
												$config_get_product_purchase_response = $_SESSION["product_purchase_response"] . "<br>";
											} else {
												$config_get_product_purchase_response = "";
											}
										} else {
											$config_get_product_purchase_response = "";
										}
										$_SESSION["product_purchase_response"] = $config_get_product_purchase_response . $json_response_decode["desc"];
										if (!in_array(explode("?", trim($_SERVER["REQUEST_URI"]))[0], array("/web/Fund.php", "/web/SubmitPayment.php", "/web/PaymentOrders.php"))) {
											header("Location: /web/Fund.php");
										}
									}
								}
							} else {

								if (($config_check_bvn_verification == true) || ($config_check_nin_verification == true)) {
									$json_response_array = array("desc" => "Dear " . ucwords($get_logged_user_details["firstname"] ?? 'User') . ", To comply with CBN regulations in creation of dedicated virtual accounts for users.<br>Please provide your " . implode(" and ", $config_kyc_verification_status_array_value) . " securely through our platform.<br>Your information is treated confidentially.");
									$json_response_encode = json_encode($json_response_array, true);
									$json_response_decode = json_decode($json_response_encode, true);
									if (isset($_SESSION["product_purchase_response"])) {
										if ($_SESSION["product_purchase_response"] !== $json_response_decode["desc"]) {
											$config_get_product_purchase_response = $_SESSION["product_purchase_response"] . "<br>";
										} else {
											$config_get_product_purchase_response = "";
										}
									} else {
										$config_get_product_purchase_response = "";
									}
									$_SESSION["product_purchase_response"] = $config_get_product_purchase_response . $json_response_decode["desc"];
									if (!in_array(explode("?", trim($_SERVER["REQUEST_URI"]))[0], array("/web/AccountSettings.php", "/web/Fund.php", "/web/SubmitPayment.php", "/web/PaymentOrders.php"))) {
										header("Location: /web/AccountSettings.php");
									}
								}
							}
						}
						alterUser($get_logged_user_details["username"], "last_login", date('Y-m-d H:i:s'));
						$create_select_sas_daily_purchase_tracker = mysqli_query($connection_server, "SELECT * FROM sas_daily_purchase_limit WHERE vendor_id='" . $get_logged_user_details["vendor_id"] . "'");
						if ($create_select_sas_daily_purchase_tracker && mysqli_num_rows($create_select_sas_daily_purchase_tracker) == 0) {
							//Daily Product Tracker
							mysqli_query($connection_server, "INSERT INTO sas_daily_purchase_limit (vendor_id, `limit`) VALUES ('" . $get_logged_user_details["vendor_id"] . "', '5')");
						}
					} else {
						header("Location: /logout.php");
					}
				} else {
					header("Location: /logout.php");
				}
			} else {
				if (!in_array(explode("?", trim($_SERVER["REQUEST_URI"]))[0], array("/web/Login.php", "/web/Register.php", "/web/PasswordRecovery.php"))) {
					$redirecturl = trim($_SERVER["REQUEST_URI"]);
					if (!empty(trim($redirecturl)) && file_exists(".." . $redirecturl)) {
						header("Location: /web/Login.php?redirecturl=" . $redirecturl);
					} else {
						header("Location: /web/Login.php");
					}
				}
			}
			$get_all_site_details_res = mysqli_query($connection_server, "SELECT * FROM sas_site_details WHERE vendor_id='" . ($get_logged_user_details["vendor_id"] ?? 0) . "' LIMIT 1");
				$get_all_site_details = ($get_all_site_details_res) ? mysqli_fetch_array($get_all_site_details_res) : array("site_title" => "My Site", "site_desc" => "Site Description", "primary_color" => "#198754");
		} else {
			header("Location: /web/Error.php");
		}
	} else {
		if (!in_array(explode("?", trim($_SERVER["REQUEST_URI"]))[0], array("/saSetup.php"))) {
			header("Location: /saSetup.php");
		}
	}
} else {
	//If Database Is Having Issue
	if (!in_array(explode("?", trim($_SERVER["REQUEST_URI"]))[0], array("/index.php"))) {
		header("Location: /index.php");
	}
}

//CSS Template Update
$css_style_template_location = "/cssfile/template/bc-style-template-1.css";
$select_vendor_style_template = mysqli_query($connection_server, "SELECT * FROM sas_vendor_style_templates WHERE vendor_id='" . ($select_vendor_table["id"] ?? 0) . "'");
if ($select_vendor_style_template && mysqli_num_rows($select_vendor_style_template) == 1) {
	$get_vendor_style_template = mysqli_fetch_array($select_vendor_style_template);
	$style_template_name = $get_vendor_style_template["template_name"];
	if (!empty($style_template_name)) {
		$style_template_location = "/cssfile/template/" . $style_template_name;
		if (file_exists(".." . $style_template_location)) {
			$css_style_template_location = $style_template_location;
		}
	}
}

/*if(emailTemplateTableExist('student-reg','','verify') == false){
	mysqli_query($connection_server, "INSERT INTO sm_email_templates (school_id_number, template_name, template_title, template_message) VALUES ('".$get_logged_user_details["school_id_number"]."', 'student-reg', 'Student Registration', 'Hello {{student_name}} ,\n\nYour registration has been successful with {{school_name}}. You can now access your account. \n\nUser Name : {{user_name}}\nClass Name : {{class_name}}\nEmail : {{email}}\n\n\nRegards From {{school_name}}.')");
}
if(emailTemplateTableExist('add-user','','verify') == false){
	mysqli_query($connection_server, "INSERT INTO sm_email_templates (school_id_number, template_name, template_title, template_message) VALUES ('".$get_logged_user_details["school_id_number"]."', 'add-user', 'Your have been assigned role of {{role}} in {{school_name}}.', 'Dear {{user_name}},\n\n         You are Added by admin in {{school_name}} . Your have been assigned role of {{role}} in {{school_name}}.  You can sign in using this link. {{login_link}}\n\nUserName : {{username}}\nPassword : {{password}}\n\nRegards From {{school_name}}.')");
}
if(emailTemplateTableExist('fees-alert','','verify') == false){
	mysqli_query($connection_server, "INSERT INTO sm_email_templates (school_id_number, template_name, template_title, template_message) VALUES ('".$get_logged_user_details["school_id_number"]."', 'fees-alert', 'Fees Alert', 'Dear {{parent_name}},\n\n        You have a new invoice.  You can check the invoice on your portal.\n.')");
}
if(emailTemplateTableExist('student-assign-teacher','','verify') == false){
	mysqli_query($connection_server, "INSERT INTO sm_email_templates (school_id_number, template_name, template_title, template_message) VALUES ('".$get_logged_user_details["school_id_number"]."', 'student-assign-teacher', 'New Student has been assigned to you.', 'Dear {{teacher_name}},\n\n         New Student {{student_name}} has been assigned to you.\n \nRegards From {{school_name}}.')");
}
if(emailTemplateTableExist('student-assigned-teacher','','verify') == false){
	mysqli_query($connection_server, "INSERT INTO sm_email_templates (school_id_number, template_name, template_title, template_message) VALUES ('".$get_logged_user_details["school_id_number"]."', 'student-assigned-teacher', 'You have been Assigned {{teacher_name}} at {{school_name}}', 'Dear {{student_name}},\n\n         You are assigned to  {{teacher_name}}. {{teacher_name}} belongs to {{class_name}}.\n \nRegards From {{school_name}}.')");
}
if(emailTemplateTableExist('attendance-absent','','verify') == false){
	mysqli_query($connection_server, "INSERT INTO sm_email_templates (school_id_number, template_name, template_title, template_message) VALUES ('".$get_logged_user_details["school_id_number"]."', 'attendance-absent', 'Your Child {{child_name}} is absent today', 'Your Child {{child_name}} is absent today.\n\nRegards From {{school_name}}.')");
}
if(emailTemplateTableExist('payment-invoice','','verify') == false){
	mysqli_query($connection_server, "INSERT INTO sm_email_templates (school_id_number, template_name, template_title, template_message) VALUES ('".$get_logged_user_details["school_id_number"]."', 'payment-invoice', 'Payment Received against Invoice', 'Dear {{student_name}},\n\n        Your have successfully paid your invoice {{invoice_no}}. You can check the invoice receipt on your portal.\n \nRegards From {{school_name}}.')");
}
if(emailTemplateTableExist('notice','','verify') == false){
	mysqli_query($connection_server, "INSERT INTO sm_email_templates (school_id_number, template_name, template_title, template_message) VALUES ('".$get_logged_user_details["school_id_number"]."', 'notice', 'New Notice For You', 'New Notice For You.\n\nNotice Title : {{notice_title}}\n\nNotice Date  : {{notice_date}}\n\nNotice For  : {{notice_for}}\n\nNotice Comment :  {{notice_comment}}\n\nRegards From {{school_name}}\n')");
}
if(emailTemplateTableExist('holiday','','verify') == false){
	mysqli_query($connection_server, "INSERT INTO sm_email_templates (school_id_number, template_name, template_title, template_message) VALUES ('".$get_logged_user_details["school_id_number"]."', 'holiday', 'Holiday Announcement', 'Holiday Announcement\n\nHoliday Title : {{holiday_title}}\n\nHoliday Date : {{holiday_date}}\n\nRegards From {{school_name}}\n')");
}
if(emailTemplateTableExist('school-bus','','verify') == false){
	mysqli_query($connection_server, "INSERT INTO sm_email_templates (school_id_number, template_name, template_title, template_message) VALUES ('".$get_logged_user_details["school_id_number"]."', 'school-bus', 'School Bus Allocation', 'School Bus Allocation\n	\n	Route Name : {{route_name}}\n	\n	Vehicle Identifier : {{vehicle_identifier}}\n	\n	Vehicle Registration Number : {{vehicle_registration_number}}\n	\n	Driver Name : {{driver_name}}\n	\n	Driver Phone Number : {{driver_phone_number}}\n	\n	Driver Address : {{driver_address}}\n	\n	Route Fare  : {{route_fare}}\n	\n	Regards From {{school_name}}\n\n')");
}
if(emailTemplateTableExist('hostel-bed','','verify') == false){
	mysqli_query($connection_server, "INSERT INTO sm_email_templates (school_id_number, template_name, template_title, template_message) VALUES ('".$get_logged_user_details["school_id_number"]."', 'hostel-bed', 'Hostel Bed Assigned', 'Hello {{student_name}} ,\n\n		You have been assigned new hostel bed in {{school_name}}.\n\nHostel Name : {{hostel_name}}\nRoom Number : {{room_id}}\nBed Number : {{bed_id}}\n\nRegards From {{school_name}}.')");
}
if(emailTemplateTableExist('subject-assigned','','verify') == false){
	mysqli_query($connection_server, "INSERT INTO sm_email_templates (school_id_number, template_name, template_title, template_message) VALUES ('".$get_logged_user_details["school_id_number"]."', 'subject-assigned', 'New subject has been assigned to you', 'Dear {{teacher_name}},\n\nNew subject {{subject_name}} has been assigned to you.\n\nRegards From \n{{school_name}}')");
}
if(emailTemplateTableExist('issue-book','','verify') == false){
	mysqli_query($connection_server, "INSERT INTO sm_email_templates (school_id_number, template_name, template_title, template_message) VALUES ('".$get_logged_user_details["school_id_number"]."', 'issue-book', 'New book has been issue to you', 'Dear {{student_name}},\n\nNew book {{book_name}} has been issue to you.\n\nRegards From \n{{school_name}}')");
}*/

//include("./email-design.php");

//Service Provider ID Array
$mtn_carrier_id_array = array("803", "702", "703", "704", "903", "806", "706", "707", "813", "810", "814", "816", "906", "916", "913", "903");
$airtel_carrier_id_array = array("701", "708", "802", "808", "812", "901", "902", "904", "907", "911", "912");
$glo_carrier_id_array = array("805", "705", "905", "807", "815", "811", "915");
$etisalat_carrier_id_array = array("809", "817", "818", "908", "909");

?>