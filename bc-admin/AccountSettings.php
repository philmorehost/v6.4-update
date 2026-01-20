<?php
error_reporting(E_ALL);
ini_set("display_errors", 1); session_start();
include ("../func/bc-admin-config.php");


if (isset($_POST["change-logo"])) {
    $logo_name = $_FILES["logo"]["name"];
    $logo_tmp_name = $_FILES["logo"]["tmp_name"];
    $logo_size = $_FILES["logo"]["size"];
    $logo_ext = strtolower(pathinfo($logo_name)["extension"]);
    $acceptable_ext_array = array("png", "jpg");
    $website_edited_name = str_replace([".", ":"], "-", $_SERVER["HTTP_HOST"]);

    if (!empty($logo_name) && ($logo_size <= "2097152") && in_array($logo_ext, $acceptable_ext_array)) {
        if (file_exists("../uploaded-image/" . $website_edited_name . "_logo.png") == true) {
            unlink("../uploaded-image/" . $website_edited_name . "_logo.png");
            move_uploaded_file($logo_tmp_name, "../uploaded-image/" . $website_edited_name . "_logo.png");
            //Website Logo Updated Successfully
            $json_response_array = array("desc" => "Website Logo Updated Successfully");
            $json_response_encode = json_encode($json_response_array, true);
        } else {
            move_uploaded_file($logo_tmp_name, "../uploaded-image/" . $website_edited_name . "_logo.png");
            //Website Logo Created Successfully
            $json_response_array = array("desc" => "Website Logo Created Successfully");
            $json_response_encode = json_encode($json_response_array, true);
        }
    } else {
        if (empty($logo_name)) {
            //File Field Empty
            $json_response_array = array("desc" => "File Field Empty");
            $json_response_encode = json_encode($json_response_array, true);
        } else {
            if (($logo_size > "2097152")) {
                //File Too Larger Than 2MB
                $json_response_array = array("desc" => "File Too Larger Than 2MB");
                $json_response_encode = json_encode($json_response_array, true);
            } else {
                if (!in_array($logo_ext, $acceptable_ext_array)) {
                    //Error: Image Extension must be ()
                    $json_response_array = array("desc" => "Error: Image Extension must be (" . implode(", ", $acceptable_ext_array) . ")");
                    $json_response_encode = json_encode($json_response_array, true);
                }
            }
        }
    }

    $json_response_decode = json_decode($json_response_encode, true);
    $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
    header("Location: " . $_SERVER["REQUEST_URI"]);
}


$css_style_template_array = array("1" => "bc-style-template-1", "2" => "bc-style-template-2", "3" => "bc-style-template-3", "4" => "bc-style-template-4", "5" => "bc-style-template-5");
if (isset($_POST["update-template"])) {
    $template_name = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["template-name"])));
    $template_filename = pathinfo($template_name, PATHINFO_FILENAME);
    $css_style_template_name_array = array_values($css_style_template_array);

    if (!empty($template_name) && in_array($template_filename, $css_style_template_name_array)) {
        $select_vendor_style_templates_details = mysqli_query($connection_server, "SELECT * FROM sas_vendor_style_templates WHERE vendor_id='" . $get_logged_admin_details["id"] . "'");
        if (mysqli_num_rows($select_vendor_style_templates_details) == 0) {
            mysqli_query($connection_server, "INSERT INTO sas_vendor_style_templates (vendor_id, template_name) VALUES ('" . $get_logged_admin_details["id"] . "', '$template_name')");
            //Template Created & Updated Successfully
            $json_response_array = array("desc" => "Template Created & Updated Successfully");
            $json_response_encode = json_encode($json_response_array, true);
        } else {
            if (mysqli_num_rows($select_vendor_style_templates_details) == 1) {
                mysqli_query($connection_server, "UPDATE sas_vendor_style_templates SET template_name='$template_name' WHERE vendor_id='" . $get_logged_admin_details["id"] . "'");
                //Template Updated Successfully
                $json_response_array = array("desc" => "Template Updated Successfully");
                $json_response_encode = json_encode($json_response_array, true);
            } else {
                if (mysqli_num_rows($select_vendor_style_templates_details) > 1) {
                    //Duplicated Details, Contact Admin
                    $json_response_array = array("desc" => "Duplicated Details, Contact Admin");
                    $json_response_encode = json_encode($json_response_array, true);
                }
            }
        }
    } else {
        if (empty($template_name)) {
            //Template Field Empty
            $json_response_array = array("desc" => "Template Field Empty");
            $json_response_encode = json_encode($json_response_array, true);
        } else {
            if (!in_array($template_filename, $css_style_template_name_array)) {
                //Invalid Template Type
                $json_response_array = array("desc" => "Invalid Template Type");
                $json_response_encode = json_encode($json_response_array, true);
            }
        }
    }

    $json_response_decode = json_decode($json_response_encode, true);
    $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
    header("Location: " . $_SERVER["REQUEST_URI"]);
}

if (isset($_POST["update-profile"])) {
    $first = mysqli_real_escape_string($connection_server, trim(strip_tags(ucwords($_POST["first"]))));
    $last = mysqli_real_escape_string($connection_server, trim(strip_tags(ucwords($_POST["last"]))));
    $address = mysqli_real_escape_string($connection_server, trim(strip_tags(ucwords($_POST["address"]))));

    if (!empty($first) && !empty($last) && !empty($address)) {
        $check_admin_details = mysqli_query($connection_server, "SELECT * FROM sas_vendors WHERE id='" . $get_logged_admin_details["id"] . "'");
        if (mysqli_num_rows($check_admin_details) == 1) {
            mysqli_query($connection_server, "UPDATE sas_vendors SET firstname='$first', lastname='$last', home_address='$address' WHERE id='" . $get_logged_admin_details["id"] . "'");
            // Email Beginning
            $log_template_encoded_text_array = array("{firstname}" => $first, "{lastname}" => $last, "{email}" => $get_logged_admin_details["email"], "{phone}" => $get_logged_admin_details["phone_number"], "{address}" => $address, "{website}" => $get_logged_admin_details["website_url"]);
            $raw_log_template_subject = getSuperAdminEmailTemplate('vendor-account-update', 'subject');
            $raw_log_template_body = getSuperAdminEmailTemplate('vendor-account-update', 'body');
            foreach ($log_template_encoded_text_array as $array_key => $array_val) {
                $raw_log_template_subject = str_replace($array_key, $array_val, $raw_log_template_subject);
                $raw_log_template_body = str_replace($array_key, $array_val, $raw_log_template_body);
            }
            sendVendorEmail($get_logged_admin_details["email"], $raw_log_template_subject, $raw_log_template_body);
            // Email End
            //Profile Information Updated Successfully
            $json_response_array = array("desc" => "Profile Information Updated Successfully");
            $json_response_encode = json_encode($json_response_array, true);
        } else {
            if (mysqli_num_rows($check_admin_details) == 0) {
                //Admin Not Exists
                $json_response_array = array("desc" => "Admin Not Exists");
                $json_response_encode = json_encode($json_response_array, true);
            } else {
                if (mysqli_num_rows($check_admin_details) > 1) {
                    //Duplicated Details, Contact Admin
                    $json_response_array = array("desc" => "Duplicated Details, Contact Admin");
                    $json_response_encode = json_encode($json_response_array, true);
                }
            }
        }
    } else {
        if (empty($first)) {
            //Firstname Field Empty
            $json_response_array = array("desc" => "Firstname Field Empty");
            $json_response_encode = json_encode($json_response_array, true);
        } else {
            if (empty($last)) {
                //Lastname Field Empty
                $json_response_array = array("desc" => "Lastname Field Empty");
                $json_response_encode = json_encode($json_response_array, true);
            } else {
                if (empty($address)) {
                    //Home Address Field Empty
                    $json_response_array = array("desc" => "Home Address Field Empty");
                    $json_response_encode = json_encode($json_response_array, true);
                }
            }
        }
    }

    $json_response_decode = json_decode($json_response_encode, true);
    $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
    header("Location: " . $_SERVER["REQUEST_URI"]);
}

if (isset($_POST["update-verification"])) {
    $bank_code = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["bank-code"])));
    $account_number = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["account-number"])));
    $bvn_nin = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["bvnnin"])));
    $verification_type = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["verification-type"])));
    $verification_type_array = array(1 => "bvn", 2 => "nin");
    if (!empty($bank_code) && is_numeric($bank_code) && (strlen($bank_code) >= 1) && !empty($account_number) && is_numeric($account_number) && (strlen($account_number) == 10) && !empty($bvn_nin) && is_numeric($bvn_nin) && (strlen($bvn_nin) == 11) && in_array($verification_type, array_keys($verification_type_array))) {
        $check_user_details = mysqli_query($connection_server, "SELECT * FROM sas_vendors WHERE id='" . $get_logged_admin_details["id"] . "'");
        if (mysqli_num_rows($check_user_details) == 1) {
            if ($verification_type == 1) {
                $amount = 30;
            } else {
                if ($verification_type == 2) {
                    $amount = 100;
                }
            }
            $discounted_amount = $amount;
            $type_alternative = ucwords("bvn/nin verification");
            $reference = substr(str_shuffle("12345678901234567890"), 0, 15);
            $description = "BVN/NIN Verification charges";
            $status = 3;
            if (!empty(vendorBalance(1)) && is_numeric(vendorBalance(1)) && (vendorBalance(1) > 0) && (vendorBalance(1) >= 10)) {
                $debit_user = chargeVendor("debit", "BVN/NIN Verification", $type_alternative, $reference, $amount, $discounted_amount, $description, $_SERVER["HTTP_HOST"], $status);
                if ($debit_user === "success") {
                    $get_monnify_access_token = json_decode(getVendorMonnifyAccessToken(), true);
                    if ($get_monnify_access_token["status"] == "success") {
                        $user_detail_verified = false;
                        $get_monnify_nuban_verification = json_decode(makeMonnifyRequest("get", $get_monnify_access_token["token"], "api/v1/disbursements/account/validate?accountNumber=" . $account_number . "&bankCode=" . $bank_code, ""), true);
                        if ($get_monnify_nuban_verification["status"] == "success") {
                            if ($verification_type == 1) {
                                $bvn_nin_account_array = array("bankCode" => $bank_code, "accountNumber" => $account_number, "bvn" => $bvn_nin);
                                $get_monnify_bvn_account_verification = json_decode(makeMonnifyRequest("post", $get_monnify_access_token["token"], "api/v1/vas/bvn-account-match", $bvn_nin_account_array), true);
                                if ($get_monnify_bvn_account_verification["status"] == "success") {
                                    mysqli_query($connection_server, "UPDATE sas_vendors SET bank_code='$bank_code', account_number='$account_number', bvn='$bvn_nin' WHERE id='" . $get_logged_admin_details["id"] . "'");
                                    $user_detail_verified = true;
                                    alterVendorTransaction($reference, "status", "1");
                                    //Admin BVN Verification Information Updated Successfully
                                    $json_response_array = array("desc" => "Admin BVN Verification Information Updated Successfully");
                                    $json_response_encode = json_encode($json_response_array, true);
                                } else {
                                    $reference_2 = substr(str_shuffle("12345678901234567890"), 0, 15);
                                    chargeVendor("credit", "BVN/NIN Verification", "Refund", $reference_2, $amount, $discounted_amount, "Refund for Ref:<i>'$reference'</i>", $_SERVER["HTTP_HOST"], "1");
                                    //BVN And Account Number Not Linked
                                    $json_response_array = array("desc" => "BVN And Account Number Not Linked");
                                    $json_response_encode = json_encode($json_response_array, true);
                                }
                            }

                            if ($verification_type == 2) {
                                $bvn_nin_account_array = array("nin" => $bvn_nin);
                                $get_monnify_nin_account_verification = json_decode(makeMonnifyRequest("post", $get_monnify_access_token["token"], "api/v1/vas/nin-details", $bvn_nin_account_array), true);
                                if ($get_monnify_nin_account_verification["status"] == "success") {
                                    $monnify_nin_response = json_decode($get_monnify_nin_account_verification["json_result"], true);
                                    $retrieved_monnify_phone_number = $monnify_nin_response["responseBody"]["mobileNumber"];
                                    $strlen_retrieved_monnify_phone_number = strlen($retrieved_monnify_phone_number);
                                    $refined_retrieved_monnify_phone_number = "0" . substr($retrieved_monnify_phone_number, ($strlen_retrieved_monnify_phone_number - 10), $strlen_retrieved_monnify_phone_number);
                                    mysqli_query($connection_server, "UPDATE sas_vendors SET phone_number='$refined_retrieved_monnify_phone_number', bank_code='$bank_code', account_number='$account_number', nin='$bvn_nin' WHERE id='" . $get_logged_admin_details["id"] . "'");
                                    $user_detail_verified = true;
                                    alterVendorTransaction($reference, "status", "1");
                                    //Admin NIN Verification Information Updated Successfully
                                    $json_response_array = array("desc" => "Admin NIN Verification Information Updated Successfully");
                                    $json_response_encode = json_encode($json_response_array, true);
                                } else {
                                    $reference_2 = substr(str_shuffle("12345678901234567890"), 0, 15);
                                    chargeVendor("credit", "BVN/NIN Verification", "Refund", $reference_2, $amount, $discounted_amount, "Refund for Ref:<i>'$reference'</i>", $_SERVER["HTTP_HOST"], "1");
                                    //NIN Cannot Be Verified
                                    $json_response_array = array("desc" => "NIN Cannot Be Verified");
                                    $json_response_encode = json_encode($json_response_array, true);
                                }
                            }


                            /*if($user_detail_verified == true){
                                //Check If Monnify Virtual Account Exists
                                $admin_monnify_account_reference = md5($_SERVER["HTTP_HOST"]."-".$get_logged_admin_details["id"]."-".$get_logged_admin_details["email"]);
                                $get_monnify_reserve_account = json_decode(makeMonnifyRequest("get", $get_monnify_access_token["token"], "api/v2/bank-transfer/reserved-accounts/".$admin_monnify_account_reference, ""), true);
                                if($get_monnify_reserve_account["status"] == "failed"){
                                    $select_monnify_gateway_details = mysqli_query_and_fetch_array($connection_server, "SELECT * FROM sas_super_admin_payment_gateways WHERE gateway_name='monnify' LIMIT 1");
                                    $monnify_create_reserve_account_array = array("accountReference" => $admin_monnify_account_reference, "accountName" => $get_logged_admin_details["firstname"]." ".$get_logged_admin_details["lastname"]." ".$get_logged_admin_details["othername"], "currencyCode" => "NGN", "contractCode" => $select_monnify_gateway_details["encrypt_key"], "customerEmail" => $get_logged_admin_details["email"], $bvn_nin_monnify_account_creation, "getAllAvailableBanks" => false, "preferredBanks" => ["232", "035", "50515", "058"]);
                                    makeMonnifyRequest("post", $get_monnify_access_token["token"], "api/v2/bank-transfer/reserved-accounts", $monnify_create_reserve_account_array);
                                }
                            }*/
                        } else {
                            $reference_2 = substr(str_shuffle("12345678901234567890"), 0, 15);
                            chargeVendor("credit", "BVN/NIN Verification", "Refund", $reference_2, $amount, $discounted_amount, "Refund for Ref:<i>'$reference'</i>", $_SERVER["HTTP_HOST"], "1");
                            //Invalid Account Number
                            $json_response_array = array("desc" => "Invalid Account Number");
                            $json_response_encode = json_encode($json_response_array, true);
                        }
                    } else {
                        if ($get_monnify_access_token["status"] == "failed") {
                            $reference_2 = substr(str_shuffle("12345678901234567890"), 0, 15);
                            chargeVendor("credit", "BVN/NIN Verification", "Refund", $reference_2, $amount, $discounted_amount, "Refund for Ref:<i>'$reference'</i>", $_SERVER["HTTP_HOST"], "1");
                            //Unable To Generate Token , Try Again Later
                            $json_response_array = array("desc" => $get_monnify_access_token["message"]);
                            $json_response_encode = json_encode($json_response_array, true);
                        }
                    }
                } else {
                    //Unable to proceed with charges
                    $json_response_array = array("status" => "failed", "desc" => "Unable to proceed with charges");
                    $json_response_encode = json_encode($json_response_array, true);
                }
            } else {
                //Error: Insufficient Funds
                $json_response_array = array("desc" => "Error: Insufficient Funds");
                $json_response_encode = json_encode($json_response_array, true);
            }
        } else {
            if (mysqli_num_rows($check_user_details) == 0) {
                //User Not Exists
                $json_response_array = array("desc" => "User Not Exists");
                $json_response_encode = json_encode($json_response_array, true);
            } else {
                if (mysqli_num_rows($check_user_details) > 1) {
                    //Duplicated Details, Contact Admin
                    $json_response_array = array("desc" => "Duplicated Details, Contact Admin");
                    $json_response_encode = json_encode($json_response_array, true);
                }
            }
        }
    } else {
        if (empty($bvn_nin)) {
            //BVN/NIN Field Empty
            $json_response_array = array("desc" => strtoupper($verification_type_array[$verification_type]) . " Field Empty");
            $json_response_encode = json_encode($json_response_array, true);
        } else {
            if (!is_numeric($bvn_nin)) {
                //Non-numeric BVN/NIN String
                $json_response_array = array("desc" => "Non-numeric " . strtoupper($verification_type_array[$verification_type]) . " String");
                $json_response_encode = json_encode($json_response_array, true);
            } else {
                if (strlen($bvn_nin) == 11) {
                    //BVN/NIN must be 11 digit long
                    $json_response_array = array("desc" => strtoupper($verification_type_array[$verification_type]) . " must be 11 digit long");
                    $json_response_encode = json_encode($json_response_array, true);
                } else {
                    if (empty($bank_code)) {
                        //Bank Code Field Empty
                        $json_response_array = array("desc" => "Bank Code Field Empty");
                        $json_response_encode = json_encode($json_response_array, true);
                    } else {
                        if (!is_numeric($bank_code)) {
                            //Non-numeric Bank Code String
                            $json_response_array = array("desc" => "Non-numeric Bank Code String");
                            $json_response_encode = json_encode($json_response_array, true);
                        } else {
                            if (strlen($bank_code) < 1) {
                                //Bank Code must be atleast 1 digit long
                                $json_response_array = array("desc" => "Bank Code must be atleast 1 digit long");
                                $json_response_encode = json_encode($json_response_array, true);
                            } else {
                                if (empty($account_number)) {
                                    //Account Number Field Empty
                                    $json_response_array = array("desc" => "Account Number Field Empty");
                                    $json_response_ennumber = json_encode($json_response_array, true);
                                } else {
                                    if (!is_numeric($account_number)) {
                                        //Non-numeric Account Number String
                                        $json_response_array = array("desc" => "Non-numeric Account Number String");
                                        $json_response_ennumber = json_encode($json_response_array, true);
                                    } else {
                                        if (strlen($account_number) < 1) {
                                            //Account Number must be atleast 1 digit long
                                            $json_response_array = array("desc" => "Account Number must be atleast 1 digit long");
                                            $json_response_ennumber = json_encode($json_response_array, true);
                                        } else {
                                            if (!in_array($verification_type, array_keys($verification_type_array))) {
                                                //Unknown Verification Type
                                                $json_response_array = array("desc" => "Unknown Verification Type");
                                                $json_response_ennumber = json_encode($json_response_array, true);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    $json_response_decode = json_decode($json_response_encode, true);
    $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
    header("Location: " . $_SERVER["REQUEST_URI"]);
}

if (isset($_POST["change-password"])) {
    $old_pass = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["old-pass"])));
    $new_pass = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["new-pass"])));
    $con_new_pass = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["con-new-pass"])));

    if (!empty($old_pass) && !empty($new_pass) && !empty($con_new_pass)) {
        $check_admin_details = mysqli_query($connection_server, "SELECT * FROM sas_vendors WHERE id='" . $get_logged_admin_details["id"] . "'");
        if (mysqli_num_rows($check_admin_details) == 1) {
            $md5_old_pass = md5($old_pass);
            $md5_new_pass = md5($new_pass);
            $md5_con_new_pass = md5($con_new_pass);

            if ($md5_old_pass == $get_logged_admin_details["password"]) {
                if ($md5_new_pass !== $get_logged_admin_details["password"]) {
                    if ($md5_new_pass == $md5_con_new_pass) {
                        mysqli_query($connection_server, "UPDATE sas_vendors SET password='$md5_new_pass' WHERE id='" . $get_logged_admin_details["id"] . "'");
                        // Email Beginning
                        $log_template_encoded_text_array = array("{firstname}" => $get_logged_admin_details["firstname"], "{lastname}" => $get_logged_admin_details["lastname"]);
                        $raw_log_template_subject = getSuperAdminEmailTemplate('vendor-pass-update', 'subject');
                        $raw_log_template_body = getSuperAdminEmailTemplate('vendor-pass-update', 'body');
                        foreach ($log_template_encoded_text_array as $array_key => $array_val) {
                            $raw_log_template_subject = str_replace($array_key, $array_val, $raw_log_template_subject);
                            $raw_log_template_body = str_replace($array_key, $array_val, $raw_log_template_body);
                        }
                        sendVendorEmail($get_logged_admin_details["email"], $raw_log_template_subject, $raw_log_template_body);
                        // Email End
                        //Account Password Updated Successfully
                        $json_response_array = array("desc" => "Account Password Updated Successfully");
                        $json_response_encode = json_encode($json_response_array, true);
                    } else {
                        //New & Confirm Password Not Match
                        $json_response_array = array("desc" => "New & Confirm Password Not Match");
                        $json_response_encode = json_encode($json_response_array, true);
                    }
                } else {
                    //New & Old Password Must Be Different
                    $json_response_array = array("desc" => "New & Old Password Must Be Different");
                    $json_response_encode = json_encode($json_response_array, true);
                }
            } else {
                //Incorrect Old Password
                $json_response_array = array("desc" => "Incorrect Old Password");
                $json_response_encode = json_encode($json_response_array, true);
            }
        } else {
            if (mysqli_num_rows($check_admin_details) == 0) {
                //Admin Not Exists
                $json_response_array = array("desc" => "Admin Not Exists");
                $json_response_encode = json_encode($json_response_array, true);
            } else {
                if (mysqli_num_rows($check_admin_details) > 1) {
                    //Duplicated Details, Contact Admin
                    $json_response_array = array("desc" => "Duplicated Details, Contact Admin");
                    $json_response_encode = json_encode($json_response_array, true);
                }
            }
        }
    } else {
        if (empty($old_pass)) {
            //Old Password Field Empty
            $json_response_array = array("desc" => "Old Password Field Empty");
            $json_response_encode = json_encode($json_response_array, true);
        } else {
            if (empty($new_pass)) {
                //New Password Field Empty
                $json_response_array = array("desc" => "New Password Field Empty");
                $json_response_encode = json_encode($json_response_array, true);
            } else {
                if (empty($con_new_pass)) {
                    //Confirm New Password Field Empty
                    $json_response_array = array("desc" => "Confirm New Password Field Empty");
                    $json_response_encode = json_encode($json_response_array, true);
                }
            }
        }
    }

    $json_response_decode = json_decode($json_response_encode, true);
    $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
    header("Location: " . $_SERVER["REQUEST_URI"]);
}

if (isset($_POST["update-bank-details"])) {
    $fullname = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["name"])));
    $bank_name = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["bank"])));
    $account_number = mysqli_real_escape_string($connection_server, preg_replace("/[^0-9]+/", "", trim(strip_tags($_POST["number"]))));
    $phone_number = mysqli_real_escape_string($connection_server, preg_replace("/[^0-9]+/", "", strip_tags($_POST["phone"])));
    $amount_charged = mysqli_real_escape_string($connection_server, preg_replace("/[^0-9.]+/", "", trim(strip_tags($_POST["charges"]))));

    if (!empty($fullname) && !empty($bank_name) && !empty($account_number) && is_numeric($account_number) && !empty($phone_number) && is_numeric($phone_number) && !empty($amount_charged) && is_numeric($amount_charged) && ($amount_charged > 0)) {
        $get_admin_payment_details = mysqli_query($connection_server, "SELECT * FROM sas_admin_payments WHERE vendor_id='" . $get_logged_admin_details["id"] . "'");

        if (mysqli_num_rows($get_admin_payment_details) == 1) {
            mysqli_query($connection_server, "UPDATE sas_admin_payments SET bank_name='$bank_name', account_name='$fullname', account_number='$account_number', phone_number='$phone_number', amount_charged='$amount_charged' WHERE vendor_id='" . $get_logged_admin_details["id"] . "'");
            //Bank Information Updated Successfully
            $json_response_array = array("desc" => "Bank Information Updated Successfully");
            $json_response_encode = json_encode($json_response_array, true);
        } else {
            if (mysqli_num_rows($get_admin_payment_details) == 0) {
                mysqli_query($connection_server, "INSERT INTO sas_admin_payments (vendor_id, bank_name, account_name, account_number, phone_number, amount_charged) VALUES ('" . $get_logged_admin_details["id"] . "', '$bank_name', '$fullname', '$account_number', '$phone_number', '$amount_charged')");
                //Admin Bank Info Exists
                $json_response_array = array("desc" => "Bank Information Created Successfully");
                $json_response_encode = json_encode($json_response_array, true);
            } else {
                if (mysqli_num_rows($get_admin_payment_details) > 1) {
                    //Duplicated Details, Contact Admin
                    $json_response_array = array("desc" => "Duplicated Details, Contact Admin");
                    $json_response_encode = json_encode($json_response_array, true);
                }
            }
        }
    } else {
        if (empty($fullname)) {
            //Fullname Field Empty
            $json_response_array = array("desc" => "Fullname Field Empty");
            $json_response_encode = json_encode($json_response_array, true);
        } else {
            if (empty($bank_name)) {
                //Bank Name Field Empty
                $json_response_array = array("desc" => "Bank Name Field Empty");
                $json_response_encode = json_encode($json_response_array, true);
            } else {
                if (empty($account_number)) {
                    //Account Number Field Empty
                    $json_response_array = array("desc" => "Account Number Field Empty");
                    $json_response_encode = json_encode($json_response_array, true);
                } else {
                    if (!is_numeric($account_number)) {
                        //Non-numeric Account Number
                        $json_response_array = array("desc" => "Non-numeric Account Number");
                        $json_response_encode = json_encode($json_response_array, true);
                    } else {
                        if (empty($phone_number)) {
                            //Phone Number Field Empty
                            $json_response_array = array("desc" => "Phone Number Field Empty");
                            $json_response_encode = json_encode($json_response_array, true);
                        } else {
                            if (!is_numeric($phone_number)) {
                                //Non-numeric Phone Number
                                $json_response_array = array("desc" => "Non-numeric Account Number");
                                $json_response_encode = json_encode($json_response_array, true);
                            } else {
                                if (empty($amount_charged)) {
                                    //Amount Field Empty
                                    $json_response_array = array("desc" => "Amount Number Field Empty");
                                    $json_response_encode = json_encode($json_response_array, true);
                                } else {
                                    if (!is_numeric($amount_charged)) {
                                        //Non-numeric Amount
                                        $json_response_array = array("desc" => "Non-numeric Account");
                                        $json_response_encode = json_encode($json_response_array, true);
                                    } else {
                                        if ($amount_charged > 0) {
                                            //Amount Must Be Greater Than 0
                                            $json_response_array = array("desc" => "Amount Must Be Greater Than 0");
                                            $json_response_encode = json_encode($json_response_array, true);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    $json_response_decode = json_decode($json_response_encode, true);
    $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
    header("Location: " . $_SERVER["REQUEST_URI"]);
}


if (isset($_POST["update-daily-purchase-limit-details"])) {
    $limit = mysqli_real_escape_string($connection_server, preg_replace("/[^0-9]+/", "", trim(strip_tags($_POST["limit"]))));

    if (!empty($limit) && is_numeric($limit) && ($limit > 0)) {
        $get_daily_purchase_limit_details = mysqli_query($connection_server, "SELECT * FROM sas_daily_purchase_limit WHERE vendor_id='" . $get_logged_admin_details["id"] . "'");

        if (mysqli_num_rows($get_daily_purchase_limit_details) == 1) {
            mysqli_query($connection_server, "UPDATE sas_daily_purchase_limit SET `limit`='$limit' WHERE vendor_id='" . $get_logged_admin_details["id"] . "'");
            //User Daily Limits Information Updated Successfully
            $json_response_array = array("desc" => "User Daily Limits Information Updated Successfully");
            $json_response_encode = json_encode($json_response_array, true);
        } else {
            if (mysqli_num_rows($get_daily_purchase_limit_details) == 0) {
                mysqli_query($connection_server, "INSERT INTO sas_daily_purchase_limit (vendor_id, `limit`) VALUES ('" . $get_logged_admin_details["id"] . "', '$limit')");
                //User Daily Limits Information Created Successfully
                $json_response_array = array("desc" => "User Daily Limits Information Created Successfully");
                $json_response_encode = json_encode($json_response_array, true);
            } else {
                if (mysqli_num_rows($get_daily_purchase_limit_details) > 1) {
                    //Duplicated Details, Contact Admin
                    $json_response_array = array("desc" => "Duplicated Details, Contact Admin");
                    $json_response_encode = json_encode($json_response_array, true);
                }
            }
        }
    } else {
        if (empty($limit)) {
            //Limit Field Empty
            $json_response_array = array("desc" => "Limit Field Empty");
            $json_response_encode = json_encode($json_response_array, true);
        } else {
            if (!is_numeric($limit)) {
                //Non-numeric Minimum Amount
                $json_response_array = array("desc" => "Non-numeric Limit");
                $json_response_encode = json_encode($json_response_array, true);
            } else {
                if (($limit < 0)) {
                    //Limit Must Be Greater Than Zero (0)
                    $json_response_array = array("desc" => "Limit MUst Be Greater Than Zero (0)");
                    $json_response_encode = json_encode($json_response_array, true);
                }
            }
        }
    }

    $json_response_decode = json_decode($json_response_encode, true);
    $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
    header("Location: " . $_SERVER["REQUEST_URI"]);
}

if (isset($_POST["refresh-purchase-id"])) {
    $purchase_id = mysqli_real_escape_string($connection_server, preg_replace("/[^0-9]+/", "", trim(strip_tags($_POST["purchase-id"]))));

    if (!empty($purchase_id) && is_numeric($purchase_id) && (strlen($purchase_id) >= 1)) {
        $get_daily_purchase_tracker_details = mysqli_query($connection_server, "SELECT * FROM sas_daily_purchase_tracker WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && product_id='$purchase_id' && date='" . date("Y-m-d") . "'");

        if (mysqli_num_rows($get_daily_purchase_tracker_details) >= 1) {
            mysqli_query($connection_server, "DELETE FROM sas_daily_purchase_tracker WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && product_id='$purchase_id' && date='" . date("Y-m-d") . "'");
            //Purchase ID Limit History Cleared Successfully
            $json_response_array = array("desc" => "Purchase ID Limit History Cleared Successfully");
            $json_response_encode = json_encode($json_response_array, true);
        } else {
            if (mysqli_num_rows($get_daily_purchase_tracker_details) == 0) {
                //Purchase ID Daily Limits Unused
                $json_response_array = array("desc" => "Purchase ID Daily Limits Unused");
                $json_response_encode = json_encode($json_response_array, true);
            }
        }
    } else {
        if (empty($purchase_id)) {
            //Pruchase ID Field Empty
            $json_response_array = array("desc" => "Purchase ID Field Empty");
            $json_response_encode = json_encode($json_response_array, true);
        } else {
            if (!is_numeric($purchase_id)) {
                //Non-numeric Purchase ID
                $json_response_array = array("desc" => "Non-numeric Purchase ID");
                $json_response_encode = json_encode($json_response_array, true);
            } else {
                if ((strlen($purchase_id) < 1)) {
                    //Purchase ID Must Be Atleast 1 Numeric Value
                    $json_response_array = array("desc" => "Purchase ID Must Be Atleast 1 Numeric Value");
                    $json_response_encode = json_encode($json_response_array, true);
                }
            }
        }
    }

    $json_response_decode = json_decode($json_response_encode, true);
    $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
    header("Location: " . $_SERVER["REQUEST_URI"]);
}

if (isset($_POST["whitelist-purchase-id"])) {
    $purchase_id = mysqli_real_escape_string($connection_server, preg_replace("/[^0-9]+/", "", trim(strip_tags($_POST["purchase-id"]))));
    $type = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["type"])));
    $type_array = array("whitelist", "blacklist");
    if (!empty($purchase_id) && is_numeric($purchase_id) && (strlen($purchase_id) >= 1) && !empty($type) && in_array($type, $type_array)) {
        $get_validated_user_purchase_id_list_details = mysqli_query($connection_server, "SELECT * FROM sas_validated_user_purchase_id_list WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && product_id='$purchase_id'");
        if ($type == "whitelist") {
            if (mysqli_num_rows($get_validated_user_purchase_id_list_details) == 0) {
                mysqli_query($connection_server, "INSERT INTO sas_validated_user_purchase_id_list (vendor_id, product_id) VALUES ('" . $get_logged_admin_details["id"] . "', '$purchase_id')");
                //Purchase ID Whitelisted Successfully
                $json_response_array = array("desc" => "Purchase ID Whitelisted Successfully");
                $json_response_encode = json_encode($json_response_array, true);
            } else {
                if (mysqli_num_rows($get_validated_user_purchase_id_list_details) == 1) {
                    //Purchase ID Already Whitelisted Successfully
                    $json_response_array = array("desc" => "Purchase ID Already Whitelisted Successfully");
                    $json_response_encode = json_encode($json_response_array, true);
                } else {
                    if (mysqli_num_rows($get_validated_user_purchase_id_list_details) > 1) {
                        //Duplicated Details, Contact Admin
                        $json_response_array = array("desc" => "Duplicated Details, Contact Admin");
                        $json_response_encode = json_encode($json_response_array, true);
                    }
                }
            }
        }

        if ($type == "blacklist") {
            if (mysqli_num_rows($get_validated_user_purchase_id_list_details) == 0) {
                //Purchase ID Not Exists in Whitelist Database
                $json_response_array = array("desc" => "Purchase ID Not Exists in Whitelist Database");
                $json_response_encode = json_encode($json_response_array, true);
            } else {
                if (mysqli_num_rows($get_validated_user_purchase_id_list_details) == 1) {
                    mysqli_query($connection_server, "DELETE FROM sas_validated_user_purchase_id_list WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && product_id='$purchase_id'");
                    //Purchase ID Removed from Whitelist Successfully
                    $json_response_array = array("desc" => "Purchase ID Removed from Whitelist Successfully");
                    $json_response_encode = json_encode($json_response_array, true);
                } else {
                    if (mysqli_num_rows($get_validated_user_purchase_id_list_details) > 1) {
                        //Duplicated Details, Contact Admin
                        $json_response_array = array("desc" => "Duplicated Details, Contact Admin");
                        $json_response_encode = json_encode($json_response_array, true);
                    }
                }
            }
        }
    } else {
        if (empty($purchase_id)) {
            //Pruchase ID Field Empty
            $json_response_array = array("desc" => "Purchase ID Field Empty");
            $json_response_encode = json_encode($json_response_array, true);
        } else {
            if (!is_numeric($purchase_id)) {
                //Non-numeric Purchase ID
                $json_response_array = array("desc" => "Non-numeric Purchase ID");
                $json_response_encode = json_encode($json_response_array, true);
            } else {
                if ((strlen($purchase_id) < 1)) {
                    //Purchase ID Must Be Atleast 1 Numeric Value
                    $json_response_array = array("desc" => "Purchase ID Must Be Atleast 1 Numeric Value");
                    $json_response_encode = json_encode($json_response_array, true);
                } else {
                    if (empty($type)) {
                        //Type Field Empty
                        $json_response_array = array("desc" => "Type Field Empty");
                        $json_response_encode = json_encode($json_response_array, true);
                    } else {
                        if (!in_array($type, $type_array)) {
                            //Invalid Type
                            $json_response_array = array("desc" => "Invalid Type");
                            $json_response_encode = json_encode($json_response_array, true);
                        }
                    }
                }
            }
        }
    }

    $json_response_decode = json_decode($json_response_encode, true);
    $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
    header("Location: " . $_SERVER["REQUEST_URI"]);
}

if (isset($_POST["update-user-minimum-funding-details"])) {
    $min_amount = mysqli_real_escape_string($connection_server, preg_replace("/[^0-9.]+/", "", trim(strip_tags($_POST["min"]))));

    if (!empty($min_amount) && is_numeric($min_amount) && ($min_amount > 0)) {
        $get_user_minimum_funding_details = mysqli_query($connection_server, "SELECT * FROM sas_user_minimum_funding WHERE vendor_id='" . $get_logged_admin_details["id"] . "'");

        if (mysqli_num_rows($get_user_minimum_funding_details) == 1) {
            mysqli_query($connection_server, "UPDATE sas_user_minimum_funding SET min_amount='$min_amount' WHERE vendor_id='" . $get_logged_admin_details["id"] . "'");
            //User Minimum Fund Limits Information Updated Successfully
            $json_response_array = array("desc" => "User Minimum Fund Limits Information Updated Successfully");
            $json_response_encode = json_encode($json_response_array, true);
        } else {
            if (mysqli_num_rows($get_user_minimum_funding_details) == 0) {
                mysqli_query($connection_server, "INSERT INTO sas_user_minimum_funding (vendor_id, min_amount) VALUES ('" . $get_logged_admin_details["id"] . "', '$min_amount')");
                //User Minimum Fund Limits Information Created Successfully
                $json_response_array = array("desc" => "User Minimum Fund Limits Information Created Successfully");
                $json_response_encode = json_encode($json_response_array, true);
            } else {
                if (mysqli_num_rows($get_user_minimum_funding_details) > 1) {
                    //Duplicated Details, Contact Admin
                    $json_response_array = array("desc" => "Duplicated Details, Contact Admin");
                    $json_response_encode = json_encode($json_response_array, true);
                }
            }
        }
    } else {
        if (empty($min_amount)) {
            //Minimum Amount Field Empty
            $json_response_array = array("desc" => "Minimum Amount Field Empty");
            $json_response_encode = json_encode($json_response_array, true);
        } else {
            if (!is_numeric($min_amount)) {
                //Non-numeric Minimum Amount
                $json_response_array = array("desc" => "Non-numeric Minimum Amount");
                $json_response_encode = json_encode($json_response_array, true);
            } else {
                if (($min_amount < 0)) {
                    //Minimum Amount Must Be Greater Than Zero (0)
                    $json_response_array = array("desc" => "Minimum Amount MUst Be Greater Than Zero (0)");
                    $json_response_encode = json_encode($json_response_array, true);
                }
            }
        }
    }

    $json_response_decode = json_decode($json_response_encode, true);
    $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
    header("Location: " . $_SERVER["REQUEST_URI"]);
}

if (isset($_POST["update-payment-order-details"])) {
    $min_amount = mysqli_real_escape_string($connection_server, preg_replace("/[^0-9.]+/", "", trim(strip_tags($_POST["min"]))));
    $max_amount = mysqli_real_escape_string($connection_server, preg_replace("/[^0-9.]+/", "", trim(strip_tags($_POST["max"]))));

    if (!empty($min_amount) && is_numeric($min_amount) && ($min_amount > 0) && !empty($max_amount) && is_numeric($max_amount) && ($max_amount > 0) && ($max_amount > $min_amount)) {
        $get_admin_payment_order_details = mysqli_query($connection_server, "SELECT * FROM sas_admin_payment_orders WHERE vendor_id='" . $get_logged_admin_details["id"] . "'");

        if (mysqli_num_rows($get_admin_payment_order_details) == 1) {
            mysqli_query($connection_server, "UPDATE sas_admin_payment_orders SET min_amount='$min_amount', max_amount='$max_amount' WHERE vendor_id='" . $get_logged_admin_details["id"] . "'");
            //Payment Order Limits Information Updated Successfully
            $json_response_array = array("desc" => "Payment Order Limits Information Updated Successfully");
            $json_response_encode = json_encode($json_response_array, true);
        } else {
            if (mysqli_num_rows($get_admin_payment_order_details) == 0) {
                mysqli_query($connection_server, "INSERT INTO sas_admin_payment_orders (vendor_id, min_amount, max_amount) VALUES ('" . $get_logged_admin_details["id"] . "', '$min_amount', '$max_amount')");
                //Payment Order Limits Information Created Successfully
                $json_response_array = array("desc" => "Payment Order Limits Information Created Successfully");
                $json_response_encode = json_encode($json_response_array, true);
            } else {
                if (mysqli_num_rows($get_admin_payment_order_details) > 1) {
                    //Duplicated Details, Contact Admin
                    $json_response_array = array("desc" => "Duplicated Details, Contact Admin");
                    $json_response_encode = json_encode($json_response_array, true);
                }
            }
        }
    } else {
        if (empty($min_amount)) {
            //Minimum Amount Field Empty
            $json_response_array = array("desc" => "Minimum Amount Field Empty");
            $json_response_encode = json_encode($json_response_array, true);
        } else {
            if (!is_numeric($min_amount)) {
                //Non-numeric Minimum Amount
                $json_response_array = array("desc" => "Non-numeric Minimum Amount");
                $json_response_encode = json_encode($json_response_array, true);
            } else {
                if (($min_amount < 0)) {
                    //Minimum Amount Must Be Greater Than Zero (0)
                    $json_response_array = array("desc" => "Minimum Amount MUst Be Greater Than Zero (0)");
                    $json_response_encode = json_encode($json_response_array, true);
                } else {
                    if (empty($max_amount)) {
                        //Maximum Amount Field Empty
                        $json_response_array = array("desc" => "Maximum Amount Field Empty");
                        $json_response_encode = json_encode($json_response_array, true);
                    } else {
                        if (!is_numeric($max_amount)) {
                            //Non-numeric Maximum Amount
                            $json_response_array = array("desc" => "Non-numeric Maximum Amount");
                            $json_response_encode = json_encode($json_response_array, true);
                        } else {
                            if (($max_amount < 0)) {
                                //Maximum Amount Must Be Greater Than Zero (0)
                                $json_response_array = array("desc" => "Maximum Amount MUst Be Greater Than Zero (0)");
                                $json_response_encode = json_encode($json_response_array, true);
                            } else {
                                if (($min_amount > $max_amount)) {
                                    //Minimum Amount Must Not Be Greater Than Maximum Amount
                                    $json_response_array = array("desc" => "Minimum Amount Must Not Be Greater Than Maximum Amount");
                                    $json_response_encode = json_encode($json_response_array, true);
                                } else {
                                    if (($min_amount == $max_amount)) {
                                        //Minimum Amount Must Not Equal Maximum Amount
                                        $json_response_array = array("desc" => "Minimum Amount Must Not Equal Maximum Amount");
                                        $json_response_encode = json_encode($json_response_array, true);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    $json_response_decode = json_decode($json_response_encode, true);
    $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
    header("Location: " . $_SERVER["REQUEST_URI"]);
}

if (isset($_POST["update-recaptcha-details"])) {
    $site_key = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["site-key"])));
    $secret_key = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["secret-key"])));

    if (!empty($site_key) && !empty($secret_key)) {
        $get_recaptcha_details = mysqli_query($connection_server, "SELECT * FROM sas_recaptcha_setting WHERE vendor_id='" . $get_logged_admin_details["id"] . "'");

        if (mysqli_num_rows($get_recaptcha_details) == 1) {
            mysqli_query($connection_server, "UPDATE sas_recaptcha_setting SET site_key='$site_key', secret_key='$secret_key' WHERE vendor_id='" . $get_logged_admin_details["id"] . "'");
            //Recaptcha Information Updated Successfully
            $json_response_array = array("desc" => "Recaptcha Information Updated Successfully");
            $json_response_encode = json_encode($json_response_array, true);
        } else {
            if (mysqli_num_rows($get_recaptcha_details) == 0) {
                mysqli_query($connection_server, "INSERT INTO sas_recaptcha_setting (vendor_id, site_key, secret_key) VALUES ('" . $get_logged_admin_details["id"] . "', '$site_key', '$secret_key')");
                //Recaptcha Information Created Successfully
                $json_response_array = array("desc" => "Recaptcha Information Created Successfully");
                $json_response_encode = json_encode($json_response_array, true);
            } else {
                if (mysqli_num_rows($get_recaptcha_details) > 1) {
                    //Duplicated Details, Contact Admin
                    $json_response_array = array("desc" => "Duplicated Details, Contact Admin");
                    $json_response_encode = json_encode($json_response_array, true);
                }
            }
        }
    } else {
        if (empty($site_key)) {
            //Site Key Field Empty
            $json_response_array = array("desc" => "Site Key Field Empty");
            $json_response_encode = json_encode($json_response_array, true);
        } else {
            if (empty($secret_key)) {
                //Secret Key Field Empty
                $json_response_array = array("desc" => "Secret Key Field Empty");
                $json_response_encode = json_encode($json_response_array, true);
            }
        }
    }

    $json_response_decode = json_decode($json_response_encode, true);
    $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
    header("Location: " . $_SERVER["REQUEST_URI"]);
}

if (isset($_POST["update-upgrade-details"])) {
    $level_id = $_POST["level-id"];
    $level_percent = $_POST["level-price"];
    $referral_level_array = array(1, 2, 3);

    if ((count($level_id) > 0) && (count($level_percent) > 0) && (count($level_id) == count($level_percent))) {
        foreach ($level_id as $index => $id) {
            $each_level_id = mysqli_real_escape_string($connection_server, preg_replace("/[^0-9.]+/", "", trim(strip_tags($level_id[$index]))));
            $each_level_percent = mysqli_real_escape_string($connection_server, preg_replace("/[^0-9.]+/", "", trim(strip_tags($level_percent[$index]))));
            if (in_array($each_level_id, $referral_level_array)) {
                $get_referral_percent_details = mysqli_query($connection_server, "SELECT * FROM sas_user_upgrade_price WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && account_type='$each_level_id'");
                if (mysqli_num_rows($get_referral_percent_details) == 1) {
                    mysqli_query($connection_server, "UPDATE sas_user_upgrade_price SET `price`='$each_level_percent' WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && account_type='$each_level_id'");
                    //User Upgrade Price Information Updated Successfully
                    $json_response_array = array("desc" => "User Upgrade Price Information Updated Successfully");
                    $json_response_encode = json_encode($json_response_array, true);
                } else {
                    if (mysqli_num_rows($get_referral_percent_details) == 0) {
                        mysqli_query($connection_server, "INSERT INTO sas_user_upgrade_price (vendor_id, account_type, `price`) VALUES ('" . $get_logged_admin_details["id"] . "', '$each_level_id', '$each_level_percent')");
                        //User Upgrade Price Information Created Successfully
                        $json_response_array = array("desc" => "User Upgrade Price Information Created Successfully");
                        $json_response_encode = json_encode($json_response_array, true);
                    } else {
                        if (mysqli_num_rows($get_referral_percent_details) > 1) {
                            //Duplicated Details, Contact Admin
                            $json_response_array = array("desc" => "Duplicated Details, Contact Admin");
                            $json_response_encode = json_encode($json_response_array, true);
                        }
                    }
                }
            } else {
                //cannot show the error once
            }
        }
    } else {
        if ((count($level_id) < 1)) {
            //Level Field Not Available
            $json_response_array = array("desc" => "Level Field Not Available");
            $json_response_encode = json_encode($json_response_array, true);
        } else {
            if ((count($level_percent) < 1)) {
                //Level Price Field Not Available
                $json_response_array = array("desc" => "Level Price Field Not Available");
                $json_response_encode = json_encode($json_response_array, true);
            } else {
                if (count($level_id) !== count($level_percent)) {
                    //Incomplete Field
                    $json_response_array = array("desc" => "Incomplete Field");
                    $json_response_encode = json_encode($json_response_array, true);
                }
            }
        }
    }

    $json_response_decode = json_decode($json_response_encode, true);
    $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
    header("Location: " . $_SERVER["REQUEST_URI"]);
}

if (isset($_POST["update-referral-details"])) {
    $level_id = $_POST["level-id"];
    $level_percent = $_POST["level-percent"];
    $referral_level_array = array(2, 3);

    if ((count($level_id) > 0) && (count($level_percent) > 0) && (count($level_id) == count($level_percent))) {
        foreach ($level_id as $index => $id) {
            $each_level_id = mysqli_real_escape_string($connection_server, preg_replace("/[^0-9.]+/", "", trim(strip_tags($level_id[$index]))));
            $each_level_percent = mysqli_real_escape_string($connection_server, preg_replace("/[^0-9.]+/", "", trim(strip_tags($level_percent[$index]))));
            if (in_array($each_level_id, $referral_level_array)) {
                $get_referral_percent_details = mysqli_query($connection_server, "SELECT * FROM sas_referral_percents WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && account_level='$each_level_id'");
                if (mysqli_num_rows($get_referral_percent_details) == 1) {
                    mysqli_query($connection_server, "UPDATE sas_referral_percents SET `percentage`='$each_level_percent' WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && account_level='$each_level_id'");
                    //Referral Percentage Information Updated Successfully
                    $json_response_array = array("desc" => "Referral Percentage Information Updated Successfully");
                    $json_response_encode = json_encode($json_response_array, true);
                } else {
                    if (mysqli_num_rows($get_referral_percent_details) == 0) {
                        mysqli_query($connection_server, "INSERT INTO sas_referral_percents (vendor_id, account_level, `percentage`) VALUES ('" . $get_logged_admin_details["id"] . "', '$each_level_id', '$each_level_percent')");
                        //Referral Percentage Information Created Successfully
                        $json_response_array = array("desc" => "Referral Percentage Information Created Successfully");
                        $json_response_encode = json_encode($json_response_array, true);
                    } else {
                        if (mysqli_num_rows($get_referral_percent_details) > 1) {
                            //Duplicated Details, Contact Admin
                            $json_response_array = array("desc" => "Duplicated Details, Contact Admin");
                            $json_response_encode = json_encode($json_response_array, true);
                        }
                    }
                }
            } else {
                //cannot show the error once
            }
        }
    } else {
        if ((count($level_id) < 1)) {
            //Level Field Not Available
            $json_response_array = array("desc" => "Level Field Not Available");
            $json_response_encode = json_encode($json_response_array, true);
        } else {
            if ((count($level_percent) < 1)) {
                //Level Percentage Field Not Available
                $json_response_array = array("desc" => "Level Percentage Field Not Available");
                $json_response_encode = json_encode($json_response_array, true);
            } else {
                if (count($level_id) !== count($level_percent)) {
                    //Incomplete Field
                    $json_response_array = array("desc" => "Incomplete Field");
                    $json_response_encode = json_encode($json_response_array, true);
                }
            }
        }
    }

    $json_response_decode = json_decode($json_response_encode, true);
    $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
    header("Location: " . $_SERVER["REQUEST_URI"]);
}

if (isset($_POST["update-site-details"])) {
    $site_title = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["site-title"])));
    $site_desc = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["site-desc"])));

    if (!empty($site_title) && !empty($site_desc)) {
        $get_site_details = mysqli_query($connection_server, "SELECT * FROM sas_site_details WHERE vendor_id='" . $get_logged_admin_details["id"] . "'");

        if (mysqli_num_rows($get_site_details) == 1) {
            mysqli_query($connection_server, "UPDATE sas_site_details SET site_title='$site_title', site_desc='$site_desc' WHERE vendor_id='" . $get_logged_admin_details["id"] . "'");
            //Site Information Updated Successfully
            $json_response_array = array("desc" => "Site Information Updated Successfully");
            $json_response_encode = json_encode($json_response_array, true);
        } else {
            if (mysqli_num_rows($get_site_details) == 0) {
                mysqli_query($connection_server, "INSERT INTO sas_site_details (vendor_id, site_title, site_desc) VALUES ('" . $get_logged_admin_details["id"] . "', '$site_title', '$site_desc')");
                //Site Information Created Successfully
                $json_response_array = array("desc" => "Site Information Created Successfully");
                $json_response_encode = json_encode($json_response_array, true);
            } else {
                if (mysqli_num_rows($get_site_details) > 1) {
                    //Duplicated Details, Contact Admin
                    $json_response_array = array("desc" => "Duplicated Details, Contact Admin");
                    $json_response_encode = json_encode($json_response_array, true);
                }
            }
        }
    } else {
        if (empty($site_title)) {
            //Site Title Field Empty
            $json_response_array = array("desc" => "Site Title Field Empty");
            $json_response_encode = json_encode($json_response_array, true);
        } else {
            if (empty($site_desc)) {
                //Site Desc Field Empty
                $json_response_array = array("desc" => "Site Description Field Empty");
                $json_response_encode = json_encode($json_response_array, true);
            }
        }
    }

    $json_response_decode = json_decode($json_response_encode, true);
    $_SESSION["product_purchase_response"] = $json_response_decode["desc"];
    header("Location: " . $_SERVER["REQUEST_URI"]);
}

if (isset($_POST["update-primary-color"])) {
    $primary_color = mysqli_real_escape_string($connection_server, trim(strip_tags($_POST["primary-color"])));

    // First, ensure the column exists
    $check_column_query = "SHOW COLUMNS FROM `sas_site_details` LIKE 'primary_color'";
    $check_column_result = mysqli_query($connection_server, $check_column_query);
    if ($check_column_result && mysqli_num_rows($check_column_result) == 0) {
        $alter_table_query = "ALTER TABLE `sas_site_details` ADD `primary_color` VARCHAR(7) NOT NULL DEFAULT '#198754'";
        mysqli_query($connection_server, $alter_table_query);
    }

    // Check if row exists for this vendor
    $check_row_query = "SELECT * FROM sas_site_details WHERE vendor_id='" . $get_logged_admin_details["id"] . "'";
    $check_row_result = mysqli_query($connection_server, $check_row_query);

    if ($check_row_result && mysqli_num_rows($check_row_result) > 0) {
        // Now, update the color
        $update_color_query = "UPDATE sas_site_details SET primary_color='$primary_color' WHERE vendor_id='" . $get_logged_admin_details["id"] . "'";
        if (mysqli_query($connection_server, $update_color_query)) {
            $_SESSION["product_purchase_response"] = "Primary color updated successfully.";
        } else {
            $_SESSION["product_purchase_response"] = "Error updating primary color.";
        }
    } else {
        // Insert new row if it doesn't exist
        $insert_color_query = "INSERT INTO sas_site_details (vendor_id, site_title, site_desc, primary_color) VALUES ('" . $get_logged_admin_details["id"] . "', 'My Site', 'Site Description', '$primary_color')";
        if (mysqli_query($connection_server, $insert_color_query)) {
            $_SESSION["product_purchase_response"] = "Primary color saved successfully.";
        } else {
            $_SESSION["product_purchase_response"] = "Error saving primary color.";
        }
    }

    echo '<script>window.location.href = "' . $_SERVER["REQUEST_URI"] . '";</script>';
    exit();
}


$get_admin_payment_details = mysqli_query_and_fetch_array($connection_server, "SELECT * FROM sas_admin_payments WHERE vendor_id='" . $get_logged_admin_details["id"] . "' LIMIT 1");
$get_admin_payment_order_details = mysqli_query_and_fetch_array($connection_server, "SELECT * FROM sas_admin_payment_orders WHERE vendor_id='" . $get_logged_admin_details["id"] . "' LIMIT 1");
$get_user_daily_purchase_limit_details = mysqli_query_and_fetch_array($connection_server, "SELECT * FROM sas_daily_purchase_limit WHERE vendor_id='" . $get_logged_admin_details["id"] . "' LIMIT 1");
$get_user_minimum_funding_details = mysqli_query_and_fetch_array($connection_server, "SELECT * FROM sas_user_minimum_funding WHERE vendor_id='" . $get_logged_admin_details["id"] . "' LIMIT 1");
$get_recaptcha_details = mysqli_query_and_fetch_array($connection_server, "SELECT * FROM sas_recaptcha_setting WHERE vendor_id='" . $get_logged_admin_details["id"] . "' LIMIT 1");
$get_site_details = mysqli_query_and_fetch_array($connection_server, "SELECT * FROM sas_site_details WHERE vendor_id='" . $get_logged_admin_details["id"] . "' LIMIT 1");


?>
<!DOCTYPE html>

<head>
    <title>Account Settings | <?php echo $get_all_super_admin_site_details["site_title"]; ?></title>
    <meta charset="UTF-8" />
    <meta name="description" content="<?php echo substr($get_all_super_admin_site_details["site_desc"], 0, 160); ?>" />
    <meta http-equiv="Content-Type" content="text/html; " />
    <meta name="theme-color" content="<?php echo $get_all_super_admin_site_details["primary_color"] ?? "#198754"; ?>" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="<?php echo $css_style_template_location; ?>">
    <link rel="stylesheet" href="/cssfile/bc-style.css">
    <meta name="author" content="BeeCodes Titan">
    <meta name="dc.creator" content="BeeCodes Titan">
    
      <!-- Vendor CSS Files -->
  <link href="../assets-2/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="../assets-2/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="../assets-2/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="../assets-2/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="../assets-2/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="../assets-2/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="../assets-2/vendor/simple-datatables/style.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="../assets-2/css/style.css" rel="stylesheet">

</head>

<body>
    <?php include ("../func/bc-admin-header.php"); ?>
  <div class="pagetitle">
      <h1>VENDOR ACCOUNT SETTINGS</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="#">Home</a></li>
          <li class="breadcrumb-item active">Account Settings</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="col-12">
  
    <div style="text-align: center;"
        class="card info-card px-5 py-5">
        <span style="user-select: auto;"
            class="text-dark h3">CHANGE
            LOGO</span><br>
        <form method="post" enctype="multipart/form-data" action="">
            <?php
            if (file_exists("../uploaded-image/" . str_replace([".", ":"], "-", $_SERVER["HTTP_HOST"]) . "_logo.png") == true) {
                $logo_image = '<img src="' . $web_http_host . '/uploaded-image/' . str_replace([".", ":"], "-", $_SERVER["HTTP_HOST"]) . '_logo.png" class="col-2" /><br/>';
            } else {
                $logo_image = '<span class="fw-bld h5">No Logo Image</span><br/>';
            }
            echo $logo_image;
            ?>
            <div style="text-align: center;"
                class="text-dark h5">
                <span id="admin-status-span" class="h5" style="user-select: auto;">LOGO IMAGE</span>
            </div><br />
            <input style="text-align: center;" name="logo" type="file" accept=".png,.jpg" placeholder="Choose Image"
                class="form-control mb-1"
                required /><br />

            <button name="change-logo" type="submit" style="user-select: auto;"
                class="btn btn-success col-12 mt-3">
                CHANGE LOGO
            </button><br>
        </form>
    </div><br />

    <!-- <div style="text-align: center;"
        class="card info-card px-5 py-5">
        <span style="user-select: auto;"
            class="text-dark h3">STYLE
            TEMPLATE</span><br>
        <form method="post" enctype="multipart/form-data" action="">
            <div style="text-align: center;"
                class="text-dark h5">
                <span id="admin-status-span" class="h5" style="user-select: auto;">CHOOSE TEMPLATE</span>
            </div><br />
            <div style="text-align: center;"
                class="color-4 bg-3 m-inline-block-dp s-inline-block-dp m-font-size-14 s-font-size-16 m-width-60 s-width-47">
                <div class="color-2 bg-3 m-flex-row-dp s-flex-row-dp">
                    <?php
                    foreach ($css_style_template_array as $template_id => $template_name) {
                        $template_name = $template_name . ".css";
                        $template_id_name = "Temp " . $template_id;
                        echo
                            '<div class="m-width-20 s-width-20 m-height-auto s-height-auto">
                                <span id="admin-status-span" class="h5" style="user-select: auto;">'.$template_id_name.'</span><br/>
                                <input type="radio" name="template-name" value="'.$template_name.'" required/>
                                <img alt="Template '.$template_id.'" src="../asset/template/temp-'.$template_id.'.png" class="col-10">
                            </div>';
                    }
                    ?>
                </div>

            </div><br />

            <button name="update-template" type="submit" style="user-select: auto;"
                class="btn btn-success col-12 mt-3">
                UPDATE TEMPLATE
            </button><br>
        </form>
    </div><br /> -->

    <div style="text-align: center;"
        class="card info-card px-5 py-5">
        <span style="user-select: auto;"
            class="text-dark h3">CRON
            REQUERY URL</span><br>
        <input style="text-align: center;" name="" type="text" placeholder="Automatic Requery Url"
            value="<?php echo $web_http_host . "/automated-cron-requery.php"; ?>"
            class="form-control mb-1"
            readonly /><br />
    </div><br />

    <div style="text-align: center;"
        class="card info-card px-5 py-5">
        <span style="user-select: auto;"
            class="text-dark h3">PRODUCT
            WEBHOOKS</span><br>
        <?php
        $count_product_webhook = array();
        foreach (scandir($_SERVER["DOCUMENT_ROOT"] . "/webhook") as $webhook) {
            $api_website_address = str_replace("-", ".", str_replace(".php", "", $webhook));
            $select_api_if_exists = mysqli_query($connection_server, "SELECT * FROM sas_apis WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && api_base_url='$api_website_address' LIMIT 1");
            if (mysqli_num_rows($select_api_if_exists) == 1) {
                array_push($count_product_webhook, $api_website_address);
                echo '<div style="text-align: center;" class="text-dark h5">
								<span id="admin-status-span" class="h5" style="user-select: auto;">' . strtoupper($api_website_address) . '</span>
							</div><br/>
						<input style="text-align: center;" name="" type="text" placeholder="' . $api_website_address . '" value="' . $web_http_host . '/webhook/' . $webhook . '" class="form-control mb-1" readonly/><br/>';
            }
        }

        if ((count(scandir($_SERVER["DOCUMENT_ROOT"] . "/webhook")) < 1) || (count($count_product_webhook) < 1)) {
            echo '<div style="text-align: center;" class="text-dark h5">
							<span id="admin-status-span" class="h5" style="user-select: auto;">PRODUCT WEBHOOK EMPTY</span>
						</div><br/>';
        }
        ?>
    </div><br />

    <div style="text-align: center;"
        class="card info-card px-5 py-5">
        <span style="user-select: auto;"
            class="text-dark h3">UPDATE
            PROFILE</span><br>
        <form method="post" action="">
            <div style="text-align: center;"
                class="text-dark h5">
                <span id="admin-status-span" class="h5" style="user-select: auto;">PERSONAL INFORMATION</span>
            </div><br />
            <input style="text-align: center;" name="first" type="text"
                value="<?php echo $get_logged_admin_details['firstname']; ?>" placeholder="Firstname"
                pattern="[a-zA-Z ]{3,}" title="Firstname must be atleast 3 letters long"
                class="form-control mb-1"
                required /><br />
            <input style="text-align: center;" name="last" type="text"
                value="<?php echo $get_logged_admin_details['lastname']; ?>" placeholder="Lastname"
                pattern="[a-zA-Z ]{3,}" title="Lastname must be atleast 3 letters long"
                class="form-control mb-1"
                required /><br />
            <input style="text-align: center;" name="address" type="text"
                value="<?php echo $get_logged_admin_details['home_address']; ?>" placeholder="Home Address"
                class="form-control mb-1"
                required /><br />

            <div style="text-align: center;"
                class="text-dark h5">
                <span id="admin-status-span" class="h5" style="user-select: auto;">DOMAIN SETUP INSTRUCTIONS</span>
            </div><br />
            <?php
            $nameservers = '';
            $ip_address = '';
            $registrar_url = '';
            $sql_fetch_options = "SELECT * FROM sas_super_admin_options WHERE option_name IN ('domain_nameservers', 'domain_ip_address', 'domain_registrar_url')";
            $result_options = mysqli_query($connection_server, $sql_fetch_options);
            if ($result_options) {
                while ($row = mysqli_fetch_assoc($result_options)) {
                    if ($row['option_name'] == 'domain_nameservers') {
                        $nameservers = $row['option_value'];
                    }
                    if ($row['option_name'] == 'domain_ip_address') {
                        $ip_address = $row['option_value'];
                    }
                    if ($row['option_name'] == 'domain_registrar_url') {
                        $registrar_url = $row['option_value'];
                    }
                }
            }
            ?>
            <div class="alert alert-info text-start mb-3">
                <p><strong>Nameservers:</strong><br><?php echo nl2br(htmlspecialchars($nameservers)); ?></p>
                <p><strong>A Record IP:</strong> <?php echo htmlspecialchars($ip_address); ?></p>
                <p><strong>Recommended Registrar:</strong> <a href="<?php echo htmlspecialchars($registrar_url); ?>" target="_blank"><?php echo htmlspecialchars($registrar_url); ?></a></p>
            </div>

            <div style="text-align: center;"
                class="text-dark h5">
                <span id="admin-status-span" class="h5" style="user-select: auto;">WEBSITE ADDRESS</span>
            </div><br />
            <input style="text-align: center;" name="" type="text"
                value="<?php echo $get_logged_admin_details['website_url']; ?>" placeholder="Website Url"
                class="form-control mb-1"
                readonly /><br />
            <button name="update-profile" type="submit" style="user-select: auto;"
                class="btn btn-success col-12 mt-3">
                UPDATE PROFILE
            </button><br>
        </form>
    </div><br />

    <div style="text-align: center;"
        class="card info-card px-5 py-5">
        <span style="user-select: auto;"
            class="text-dark h3">VENDOR
            KYC</span><br>
        <form method="post" action="">

            <div style="text-align: center;"
                class="text-dark h5">
                <span id="user-status-span" class="h5" style="user-select: auto;">BANK INFORMATION</span>
            </div><br />

            <select style="text-align: center;" id="" name="bank-code" onchange=""
                class="form-control mb-1"
                required>
                <option value="" default hidden selected>Choose Bank</option>
                <?php
                //Bank Lists
                $get_monnify_access_token_2 = json_decode(getVendorMonnifyAccessToken(), true);
                if ($get_monnify_access_token_2["status"] == "success") {
                    $get_monnify_bank_lists = json_decode(getMonnifyBanks($get_monnify_access_token_2["token"]), true);

                    if ($get_monnify_bank_lists["status"] == "success") {
                        foreach ($get_monnify_bank_lists["banks"] as $bank_json) {
                            $decode_bank_json = $bank_json;
                            if ($decode_bank_json["code"] == $get_logged_admin_details["bank_code"]) {
                                echo '<option value="' . $decode_bank_json["code"] . '" selected>' . $decode_bank_json["name"] . '</option>';
                            } else {
                                echo '<option value="' . $decode_bank_json["code"] . '">' . $decode_bank_json["name"] . '</option>';
                            }
                        }
                    }
                }

                ?>
            </select><br />
            <input style="text-align: center;" name="account-number" type="text"
                value="<?php echo $get_logged_admin_details['account_number']; ?>" placeholder="Account Number *"
                pattern="[0-9]{10}" title="Account number must be 10 digit long"
                class="form-control mb-1"
                required /><br />

            <div style="text-align: center;"
                class="text-dark h5">
                <span id="user-status-span" class="h5" style="user-select: auto;">BVN/NIN INFORMATION</span>
            </div><br />
            <select style="text-align: center;" id="" name="verification-type" onchange=""
                class="form-control mb-1"
                required>
                <option value="" default hidden selected>Choose Verification Type</option>
                <option value="1">BVN</option>
                <option value="2">NIN</option>
            </select><br />
            <input style="text-align: center;" name="bvnnin" type="text" value="" placeholder="BVN or NIN *"
                pattern="[0-9]{11}" title="BVN must be 11 digit long"
                class="form-control mb-1"
                required /><br />

            <button name="update-verification" type="submit" style="user-select: auto;"
                class="btn btn-success col-12 mt-3">
                UPDATE DETAILS
            </button><br>
            <div style="text-align: center;"
                class="col-12 mt-2">
                <span id="user-status-span" class="h5" style="user-select: auto;">NB: Updated BVN & NIN will not
                    be shown for your privacy security and note that certain charge applied for every successful BVN
                    (₦30) and NIN (₦100) verification respectively</span>
            </div><br />
        </form>
    </div><br />

    <div style="text-align: center;"
        class="card info-card px-5 py-5">
        <span style="user-select: auto;"
            class="text-dark h3">CHANGE
            PASSWORD</span><br>
        <form method="post" action="">
            <div style="text-align: center;"
                class="text-dark h5">
                <span id="admin-status-span" class="h5" style="user-select: auto;">OLD PASSWORD</span>
            </div><br />
            <input style="text-align: center;" name="old-pass" type="password" value="" placeholder="Old Password"
                class="form-control mb-1"
                required /><br />
            <div style="text-align: center;"
                class="text-dark h5">
                <span id="admin-status-span" class="h5" style="user-select: auto;">NEW PASSWORD</span>
            </div><br />
            <input style="text-align: center;" name="new-pass" type="password" value="" placeholder="New Password"
                class="form-control mb-1"
                required /><br />
            <input style="text-align: center;" name="con-new-pass" type="password" value=""
                placeholder="Confirm New Password"
                class="form-control mb-1"
                required /><br />
            <button name="change-password" type="submit" style="user-select: auto;"
                class="btn btn-success col-12 mt-3">
                CHANGE PASSWORD
            </button><br>
        </form>
    </div><br />

    <div style="text-align: center;"
        class="card info-card px-5 py-5">
        <span style="user-select: auto;"
            class="text-dark h3">BANK
            INFORMATION</span><br>
        <form method="post" action="">
            <div style="text-align: center;"
                class="text-dark h5">
                <span id="admin-status-span" class="h5" style="user-select: auto;">ACCOUNT NAME</span>
            </div><br />
            <input style="text-align: center;" name="name" type="text"
                value="<?php echo $get_admin_payment_details['account_name']; ?>" placeholder="Fullname"
                pattern="[a-zA-Z ]{3,}" title="Fullname must be atleast 3 letters long"
                class="form-control mb-1"
                required /><br />
            <div style="text-align: center;"
                class="text-dark h5">
                <span id="admin-status-span" class="h5" style="user-select: auto;">BANK NAME</span>
            </div><br />
            <input style="text-align: center;" name="bank" type="text"
                value="<?php echo $get_admin_payment_details['bank_name']; ?>" placeholder="Bank Name"
                pattern="[a-zA-Z ]{3,}" title="Bank Name must be atleast 3 letters long"
                class="form-control mb-1"
                required /><br />
            <div style="text-align: center;"
                class="text-dark h5">
                <span id="admin-status-span" class="h5" style="user-select: auto;">ACCOUNT NUMBER</span>
            </div><br />
            <input style="text-align: center;" name="number" type="text"
                value="<?php echo $get_admin_payment_details['account_number']; ?>" placeholder="Account Number"
                pattern="[0-9]{10}" title="Account number must be 10 digit long"
                class="form-control mb-1"
                required /><br />
            <div style="text-align: center;"
                class="text-dark h5">
                <span id="admin-status-span" class="h5" style="user-select: auto;">PHONE NUMBER</span>
            </div><br />
            <input style="text-align: center;" name="phone" type="text"
                value="<?php echo $get_admin_payment_details['phone_number']; ?>" placeholder="Phone Number"
                pattern="[0-9]{11}" title="Phone number must be 11 digit long"
                class="form-control mb-1"
                required /><br />
            <div style="text-align: center;"
                class="text-dark h5">
                <span id="admin-status-span" class="h5" style="user-select: auto;">BANK CHARGES</span>
            </div><br />
            <input style="text-align: center;" name="charges" type="text"
                value="<?php echo $get_admin_payment_details['amount_charged']; ?>" placeholder="Bank Charges"
                pattern="[0-9]{1,}" title="Bank Charges must atleast 1 digit long"
                class="form-control mb-1"
                required /><br />

            <button name="update-bank-details" type="submit" style="user-select: auto;"
                class="btn btn-success col-12 mt-3">
                UPDATE BANK
            </button><br>
        </form>
    </div>

    <div style="text-align: center;"
        class="card info-card px-5 py-5">
        <span style="user-select: auto;"
            class="text-dark h3">DAILY
            USER PURCHASE LIMIT</span><br>
        <form method="post" action="">
            <div style="text-align: center;"
                class="text-dark h5">
                <span id="admin-status-span" class="h5" style="user-select: auto;">DAILY LIMIT</span>
            </div><br />
            <input style="text-align: center;" name="limit" type="text"
                value="<?php echo $get_user_daily_purchase_limit_details['limit']; ?>" placeholder="Minimum Amount"
                pattern="[0-9]{1,}" title="Minimum Amount must be atleast 2 digit long"
                class="form-control mb-1"
                required /><br />

            <button name="update-daily-purchase-limit-details" type="submit" style="user-select: auto;"
                class="btn btn-success col-12 mt-3">
                UPDATE LIMIT
            </button><br>
        </form>
    </div>

    <div style="text-align: center;"
        class="card info-card px-5 py-5">
        <span style="user-select: auto;"
            class="text-dark h3">REFRESH
            PURCHASE ID LIMIT</span><br>
        <form method="post" action="">
            <div style="text-align: center;"
                class="text-dark h5">
                <span id="admin-status-span" class="h5" style="user-select: auto;">PURCHASE ID</span>
            </div><br />
            <input style="text-align: center;" name="purchase-id" type="text" value=""
                placeholder="Phone number, Cable IUC, Meter number" pattern="[0-9]{1,}"
                title="Purchase ID must be atleast 2 digit long"
                class="form-control mb-1"
                required /><br />

            <button name="refresh-purchase-id" type="submit" style="user-select: auto;"
                class="btn btn-success col-12 mt-3">
                REFRESH LIMIT
            </button><br>
        </form>
    </div>

    <div style="text-align: center;"
        class="card info-card px-5 py-5">
        <span style="user-select: auto;"
            class="text-dark h3">WHITELIST
            PURCHASE ID</span><br>
        <form method="post" action="">
            <div style="text-align: center;"
                class="text-dark h5">
                <span id="admin-status-span" class="h5" style="user-select: auto;">PURCHASE ID</span>
            </div><br />
            <input style="text-align: center;" name="purchase-id" type="text" value=""
                placeholder="Phone number, Cable IUC, Meter number" pattern="[0-9]{1,}"
                title="Purchase ID must be atleast 2 digit long"
                class="form-control mb-1"
                required /><br />

            <select style="text-align: center;" id="" name="type" onchange=""
                class="form-control mb-1"
                required>
                <option value="" default hidden selected>Choose Type</option>
                <option value="whitelist">Whitelist</option>
                <option value="blacklist">Remove</option>
            </select><br />
            <button name="whitelist-purchase-id" type="submit" style="user-select: auto;"
                class="btn btn-success col-12 mt-3">
                SAVE CHANGES
            </button><br>
        </form>
    </div>

    <div style="text-align: center;"
        class="card info-card px-5 py-5">
        <span style="user-select: auto;"
            class="text-dark h3">USER
            MINIMUM FUNDING</span><br>
        <form method="post" action="">
            <div style="text-align: center;"
                class="text-dark h5">
                <span id="admin-status-span" class="h5" style="user-select: auto;">MINIMUM AMOUNT</span>
            </div><br />
            <input style="text-align: center;" name="min" type="text"
                value="<?php echo $get_user_minimum_funding_details['min_amount']; ?>" placeholder="Minimum Amount"
                pattern="[0-9]{1,}" title="Minimum Amount must be atleast 2 digit long"
                class="form-control mb-1"
                required /><br />

            <button name="update-user-minimum-funding-details" type="submit" style="user-select: auto;"
                class="btn btn-success col-12 mt-3">
                UPDATE FUNDING
            </button><br>
        </form>
    </div>

    <div style="text-align: center;"
        class="card info-card px-5 py-5">
        <span style="user-select: auto;"
            class="text-dark h3">PAYMENT
            ORDER</span><br>
        <form method="post" action="">
            <div style="text-align: center;"
                class="text-dark h5">
                <span id="admin-status-span" class="h5" style="user-select: auto;">MINIMUM AMOUNT</span>
            </div><br />
            <input style="text-align: center;" name="min" type="text"
                value="<?php echo $get_admin_payment_order_details['min_amount']; ?>" placeholder="Minimum Amount"
                pattern="[0-9]{2,}" title="Minimum Amount must be atleast 2 digit long"
                class="form-control mb-1"
                required /><br />
            <div style="text-align: center;"
                class="text-dark h5">
                <span id="admin-status-span" class="h5" style="user-select: auto;">MAXIMUM AMOUNT</span>
            </div><br />
            <input style="text-align: center;" name="max" type="text"
                value="<?php echo $get_admin_payment_order_details['max_amount']; ?>" placeholder="Maximum Amount"
                pattern="[0-9]{2,}" title="Maximum Amount must be atleast 2 digit long"
                class="form-control mb-1"
                required /><br />

            <button name="update-payment-order-details" type="submit" style="user-select: auto;"
                class="btn btn-success col-12 mt-3">
                UPDATE PAYMENT ORDER
            </button><br>
        </form>
    </div>

    <div style="text-align: center;"
        class="card info-card px-5 py-5">
        <span style="user-select: auto;"
            class="text-dark h3">RECAPTCHA
            SET-UP</span><br>
        <form method="post" action="">
            <div style="text-align: center;"
                class="text-dark h5">
                <span id="admin-status-span" class="h5" style="user-select: auto;">SITE KEY</span>
            </div><br />
            <input style="text-align: center;" name="site-key" type="text"
                value="<?php echo $get_recaptcha_details['site_key']; ?>" placeholder="Site Key"
                class="form-control mb-1"
                required /><br />
            <div style="text-align: center;"
                class="text-dark h5">
                <span id="admin-status-span" class="h5" style="user-select: auto;">SECRET KEY</span>
            </div><br />
            <input style="text-align: center;" name="secret-key" type="text"
                value="<?php echo $get_recaptcha_details['secret_key']; ?>" placeholder="Secret Key"
                class="form-control mb-1"
                required /><br />

            <button name="update-recaptcha-details" type="submit" style="user-select: auto;"
                class="btn btn-success col-12 mt-3">
                UPDATE RECAPTCHA KEY
            </button><br>
        </form>
    </div>

    <div style="text-align: center;"
        class="card info-card px-5 py-5">
        <span style="user-select: auto;"
            class="text-dark h3">USER
            UPGRADE PRICE</span><br>
        <form method="post" action="">
            <?php
            $upgrade_level_array = array(1 => "smart user", 2 => "agent vendor", 3 => "api vendor");
            foreach ($upgrade_level_array as $index => $level_name) {
                $get_upgrade_price_details = mysqli_query_and_fetch_array($connection_server, "SELECT * FROM sas_user_upgrade_price WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && account_type='$index'");
                echo '<div style="text-align: center;" class="text-dark h5">
                                    <span id="admin-status-span" class="h5" style="user-select: auto;">' . strtoupper($level_name) . '</span>
                                </div><br/>
                                <input style="text-align: center;" name="level-id[]" value="' . $index . '" hidden required/>
                                <input style="text-align: center;" name="level-price[]" type="number" placeholder="' . ucwords($level_name) . ' Price" step="0.1" min="0" value="' . $get_upgrade_price_details["price"] . '" class="form-control mb-1" required/><br/>';
            }
            ?>

            <button name="update-upgrade-details" type="submit" style="user-select: auto;"
                class="btn btn-success col-12 mt-3">
                UPDATE UPGRADE PRICE
            </button><br>
        </form>
    </div>

    <div style="text-align: center;"
        class="card info-card px-5 py-5">
        <span style="user-select: auto;"
            class="text-dark h3">REFERRAL
            PERCENTAGE</span><br>
        <form method="post" action="">
            <?php
            $referral_level_array = array(2 => "agent vendor", 3 => "api vendor");
            foreach ($referral_level_array as $index => $level_name) {
                $get_referral_percent_details = mysqli_query_and_fetch_array($connection_server, "SELECT * FROM sas_referral_percents WHERE vendor_id='" . $get_logged_admin_details["id"] . "' && account_level='$index'");
                echo '<div style="text-align: center;" class="text-dark h5">
                                    <span id="admin-status-span" class="h5" style="user-select: auto;">' . strtoupper($level_name) . '</span>
                                </div><br/>
                                <input style="text-align: center;" name="level-id[]" value="' . $index . '" hidden required/>
                                <input style="text-align: center;" name="level-percent[]" type="number" placeholder="' . ucwords($level_name) . ' %" step="0.1" min="0" max="100" value="' . $get_referral_percent_details["percentage"] . '" class="form-control mb-1" required/><br/>';
            }
            ?>

            <button name="update-referral-details" type="submit" style="user-select: auto;"
                class="btn btn-success col-12 mt-3">
                UPDATE REFERRAL PERCENTS
            </button><br>
        </form>
    </div>

    <div style="text-align: center;"
        class="card info-card px-5 py-5">
        <span style="user-select: auto;"
            class="text-dark h3">SITE
            DETAILS</span><br>
        <form method="post" action="">
            <div style="text-align: center;"
                class="text-dark h5">
                <span id="admin-status-span" class="h5" style="user-select: auto;">SITE TITLE</span>
            </div><br />
            <input style="text-align: center;" name="site-title" type="text"
                value="<?php echo $get_site_details['site_title']; ?>" placeholder="Site Title"
                class="form-control mb-1"
                required /><br />
            <div style="text-align: center;"
                class="text-dark h5">
                <span id="admin-status-span" class="h5" style="user-select: auto;">SITE DESCRIPTION</span>
            </div><br />
            <input style="text-align: center;" name="site-desc" type="text"
                value="<?php echo $get_site_details['site_desc']; ?>" placeholder="Site Description"
                class="form-control mb-1"
                required /><br />

            <button name="update-site-details" type="submit" style="user-select: auto;"
                class="btn btn-success col-12 mt-3">
                UPDATE SITE DETAILS
            </button><br>
        </form>
    </div>

    <div style="text-align: center;" class="card info-card px-5 py-5">
        <span style="user-select: auto;" class="text-dark h3">PRIMARY COLOR</span><br>
        <form method="post" action="">
            <div style="text-align: center;" class="text-dark h5">
                <span id="admin-status-span" class="h5" style="user-select: auto;">CHOOSE PRIMARY COLOR</span>
            </div><br />
            <?php
            // Ensure the primary_color column exists before trying to fetch it
            $primary_color_value = '#198754'; // Default color
            $check_column_query = "SHOW COLUMNS FROM `sas_site_details` LIKE 'primary_color'";
            $check_column_result = mysqli_query($connection_server, $check_column_query);
            if (mysqli_num_rows($check_column_result) > 0) {
                $get_color_query = "SELECT primary_color FROM sas_site_details WHERE vendor_id='" . $get_logged_admin_details["id"] . "' LIMIT 1";
                $get_color_result = mysqli_query($connection_server, $get_color_query);
                if ($get_color_result && mysqli_num_rows($get_color_result) > 0) {
                    $color_data = mysqli_fetch_assoc($get_color_result);
                    if (!empty($color_data['primary_color'])) {
                        $primary_color_value = $color_data['primary_color'];
                    }
                }
            }
            ?>
            <input style="text-align: center;" name="primary-color" type="color" value="<?php echo $primary_color_value; ?>" class="form-control form-control-color" required /><br />
            <button name="update-primary-color" type="submit" style="user-select: auto;" class="btn btn-success col-12 mt-3">
                UPDATE PRIMARY COLOR
            </button><br>
        </form>
    </div>
  </div>
</section>

    <?php include ("../func/bc-admin-footer.php"); ?>

</body>

</html>