<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
if ($connection_server) {
//Create Super Admin Table
$create_super_admin_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_super_admin (id INT NOT NULL AUTO_INCREMENT, email VARCHAR(225) NOT NULL, password VARCHAR(225) NOT NULL, firstname VARCHAR(225) NOT NULL, lastname VARCHAR(225) NOT NULL, phone_number VARCHAR(225) NOT NULL, gender VARCHAR(225) NOT NULL, home_address VARCHAR(225) NOT NULL, status INT UNSIGNED NOT NULL, reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP, last_login VARCHAR(225), PRIMARY KEY (id))");

//Create Super Admin Status Message Table
$create_super_admin_status_message_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_super_admin_status_messages (message LONGTEXT NOT NULL, date TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");

//Create Vendors Table
$create_vendor_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_vendors (id INT NOT NULL AUTO_INCREMENT, email VARCHAR(225) NOT NULL, password VARCHAR(225) NOT NULL, firstname VARCHAR(225) NOT NULL, lastname VARCHAR(225) NOT NULL, phone_number VARCHAR(225) NOT NULL, balance DECIMAL(65,30) UNSIGNED NOT NULL, website_url VARCHAR(225) NOT NULL, home_address VARCHAR(225) NOT NULL, bank_code VARCHAR(225), account_number VARCHAR(225), bvn VARCHAR(225), nin VARCHAR(225), status INT UNSIGNED NOT NULL, reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP, last_login VARCHAR(225), PRIMARY KEY (id))");

if ($create_vendor_table) {
    // Check for and add subscription columns if they don't exist
    $vendor_columns = mysqli_query($connection_server, "SHOW COLUMNS FROM sas_vendors");
    $existing_columns = [];
    while($col = mysqli_fetch_assoc($vendor_columns)) {
        $existing_columns[] = $col['Field'];
    }

    if (!in_array('expiry_date', $existing_columns)) {
        mysqli_query($connection_server, "ALTER TABLE sas_vendors ADD COLUMN expiry_date DATE NULL");
    }

    if (!in_array('start_date', $existing_columns)) {
        mysqli_query($connection_server, "ALTER TABLE sas_vendors ADD COLUMN start_date DATE NULL");
    }

    if (!in_array('current_billing_id', $existing_columns)) {
        mysqli_query($connection_server, "ALTER TABLE sas_vendors ADD COLUMN current_billing_id INT NULL");
    }

    if (!in_array('expiry_notified', $existing_columns)) {
        mysqli_query($connection_server, "ALTER TABLE sas_vendors ADD COLUMN expiry_notified INT(1) NOT NULL DEFAULT 0");
    }

    if (!in_array('suspended', $existing_columns)) {
        mysqli_query($connection_server, "ALTER TABLE sas_vendors ADD COLUMN suspended TINYINT(1) NOT NULL DEFAULT 0");
    }
}

//Create Vendor Banks Table
$create_vendor_banks_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_vendor_banks (vendor_id INT UNSIGNED NOT NULL, reference VARCHAR(225) NOT NULL, bank_code VARCHAR(225) NOT NULL, bank_name VARCHAR(225) NOT NULL, account_name VARCHAR(225) NOT NULL, account_number VARCHAR(225) NOT NULL)");

//Create Vendors Billing Table
$create_vendor_billing_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_vendor_billings (id INT NOT NULL AUTO_INCREMENT, amount VARCHAR(225) NOT NULL, bill_type VARCHAR(225) NOT NULL, description LONGTEXT NOT NULL, starting_date VARCHAR(225), ending_date VARCHAR(225), date TIMESTAMP DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (id))");

//Create Vendors Paid Bills Table
$create_vendor_paid_bills_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_vendor_paid_bills (id INT NOT NULL AUTO_INCREMENT, vendor_id INT UNSIGNED NOT NULL, bill_id INT UNSIGNED NOT NULL, amount VARCHAR(225) NOT NULL, bill_type VARCHAR(225) NOT NULL, description LONGTEXT NOT NULL, starting_date VARCHAR(225), ending_date VARCHAR(225), date TIMESTAMP DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (id))");

//Create Pending Vendors Table
$create_pending_vendors_table = mysqli_query($connection_server, "
CREATE TABLE IF NOT EXISTS sas_pending_vendors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    website_url VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    firstname VARCHAR(255) NOT NULL,
    lastname VARCHAR(255) NOT NULL,
    phone_number VARCHAR(50) NOT NULL,
    home_address TEXT NOT NULL,
    billing_package_id INT NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    payment_status VARCHAR(50) NOT NULL DEFAULT 'pending', -- Added here
    paystack_reference VARCHAR(255) NULL,
    status INT NOT NULL DEFAULT 0,
    reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (email),
    UNIQUE (website_url))");    
    

//Create Billing Packages Table
$create_billing_packages_table = mysqli_query($connection_server, "
    CREATE TABLE IF NOT EXISTS sas_billing_packages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    duration_days INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");

//Create Vendor Status Message Table
$create_vendor_status_message_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_vendor_status_messages (vendor_id INT UNSIGNED NOT NULL, message LONGTEXT NOT NULL, date TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");

//Create User Table
$create_user_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_users (id INT NOT NULL AUTO_INCREMENT, vendor_id INT UNSIGNED NOT NULL, email VARCHAR(225) NOT NULL, username VARCHAR(225) NOT NULL, password VARCHAR(225) NOT NULL, phone_number VARCHAR(225) NOT NULL, balance DECIMAL(65,30) UNSIGNED NOT NULL, firstname VARCHAR(225) NOT NULL, lastname VARCHAR(225) NOT NULL, othername VARCHAR(225), home_address VARCHAR(225) NOT NULL, bank_code VARCHAR(225), account_number VARCHAR(225), bvn VARCHAR(225), nin VARCHAR(225), transaction_pin BIGINT, security_quest BIGINT, security_answer VARCHAR(225), referral_id VARCHAR(225), account_level INT UNSIGNED NOT NULL, api_key VARCHAR(225) NOT NULL, api_status INT UNSIGNED NOT NULL, status INT UNSIGNED NOT NULL, reg_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP, last_login VARCHAR(225), PRIMARY KEY (id))");

//Create User Banks Table
$create_user_banks_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_user_banks (vendor_id INT UNSIGNED NOT NULL, username VARCHAR(225) NOT NULL, reference VARCHAR(225) NOT NULL, bank_code VARCHAR(225) NOT NULL, bank_name VARCHAR(225) NOT NULL, account_name VARCHAR(225) NOT NULL, account_number VARCHAR(225) NOT NULL)");

//Create User Minimum Funding Table
$create_user_minimum_funding_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_user_minimum_funding (vendor_id INT UNSIGNED NOT NULL, min_amount VARCHAR(225) NOT NULL)");

//Create ID Blocking System Table
$create_id_blocking_system_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_id_blocking_system (vendor_id INT UNSIGNED NOT NULL, product_id VARCHAR(225) NOT NULL, date TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");

//Create Recaptcha Setting
$recaptcha_setting = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_recaptcha_setting (id INT NOT NULL AUTO_INCREMENT, vendor_id INT UNSIGNED NOT NULL, site_key VARCHAR(225) NOT NULL, secret_key VARCHAR(225) NOT NULL, PRIMARY KEY (id))");

//Create Security Question Table
$create_security_question_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_security_quests (id INT NOT NULL AUTO_INCREMENT, quest VARCHAR(225) NOT NULL, PRIMARY KEY (id))");
if ($create_security_question_table == true) {
	$select_sas_security_quests = mysqli_query($connection_server, "SELECT * FROM sas_security_quests");
	if (mysqli_num_rows($select_sas_security_quests) == 0) {
		//Security Quests
		mysqli_query($connection_server, "INSERT INTO sas_security_quests (quest) VALUES ('What is your favorite childhood pets name?')");
		mysqli_query($connection_server, "INSERT INTO sas_security_quests (quest) VALUES ('In which city were you born?')");
		mysqli_query($connection_server, "INSERT INTO sas_security_quests (quest) VALUES ('What is the name of your maternal grandmother?')");
		mysqli_query($connection_server, "INSERT INTO sas_security_quests (quest) VALUES ('What is your favorite movie or book?')");
		mysqli_query($connection_server, "INSERT INTO sas_security_quests (quest) VALUES ('What is the first school you attended?')");
		mysqli_query($connection_server, "INSERT INTO sas_security_quests (quest) VALUES ('What is your mothers maiden name?')");
		mysqli_query($connection_server, "INSERT INTO sas_security_quests (quest) VALUES ('Which street did you grow up on?')");
		mysqli_query($connection_server, "INSERT INTO sas_security_quests (quest) VALUES ('What is the name of your best childhood friend?')");
		mysqli_query($connection_server, "INSERT INTO sas_security_quests (quest) VALUES ('In which year did you graduate from high school?')");
		mysqli_query($connection_server, "INSERT INTO sas_security_quests (quest) VALUES ('What is your favorite holiday destination?')");
	}
}

//Create Referral Percentage Table
$create_referral_percentage_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_referral_percents (id INT NOT NULL AUTO_INCREMENT, vendor_id INT UNSIGNED NOT NULL, account_level INT UNSIGNED NOT NULL, percentage VARCHAR(225), PRIMARY KEY (id))");

//Create Product Table
$create_product_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_products (id INT NOT NULL AUTO_INCREMENT, vendor_id INT UNSIGNED NOT NULL, product_name VARCHAR(225) NOT NULL, description LONGTEXT, status INT UNSIGNED NOT NULL, PRIMARY KEY (id))");

//Create APIS Table
$create_apis_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_apis (id INT NOT NULL AUTO_INCREMENT, vendor_id INT UNSIGNED NOT NULL, api_base_url VARCHAR(225) NOT NULL, api_type VARCHAR(225) NOT NULL, api_key LONGTEXT NOT NULL, status INT UNSIGNED NOT NULL, PRIMARY KEY (id))");

//Create User Ugrade Price Table
$create_user_upgrade_price_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_user_upgrade_price (id INT NOT NULL AUTO_INCREMENT, vendor_id INT UNSIGNED NOT NULL, account_type VARCHAR(225) NOT NULL, price VARCHAR(225), PRIMARY KEY (id))");

//Create User Payment Checkout Table
$create_user_payment_checkout_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_user_payment_checkouts (id INT NOT NULL AUTO_INCREMENT, vendor_id INT UNSIGNED NOT NULL, username VARCHAR(225) NOT NULL, reference VARCHAR(225) NOT NULL, status INT UNSIGNED NOT NULL, payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (id))");

//Create Vendor Payment Checkout Table
$create_vendor_payment_checkout_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_vendor_payment_checkouts (id INT NOT NULL AUTO_INCREMENT, vendor_id INT UNSIGNED NOT NULL, reference VARCHAR(225) NOT NULL, status INT UNSIGNED NOT NULL, payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (id))");

//Create Crypto Status Table
$create_crypto_status_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_crypto_status (id INT NOT NULL AUTO_INCREMENT, vendor_id INT UNSIGNED NOT NULL, api_id INT UNSIGNED NOT NULL, product_name VARCHAR(225) NOT NULL, description LONGTEXT, status INT UNSIGNED NOT NULL, PRIMARY KEY (id))");

//Create Airtime Status Table
$create_airtime_status_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_airtime_status (id INT NOT NULL AUTO_INCREMENT, vendor_id INT UNSIGNED NOT NULL, api_id INT UNSIGNED NOT NULL, product_name VARCHAR(225) NOT NULL, description LONGTEXT, status INT UNSIGNED NOT NULL, PRIMARY KEY (id))");

//Create Betting Status Table
$create_betting_status_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_betting_status (id INT NOT NULL AUTO_INCREMENT, vendor_id INT UNSIGNED NOT NULL, api_id INT UNSIGNED NOT NULL, product_name VARCHAR(225) NOT NULL, description LONGTEXT, status INT UNSIGNED NOT NULL, PRIMARY KEY (id))");

//Create Bulk SMS Status Table
$create_bulk_sms_status_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_bulk_sms_status (id INT NOT NULL AUTO_INCREMENT, vendor_id INT UNSIGNED NOT NULL, api_id INT UNSIGNED NOT NULL, product_name VARCHAR(225) NOT NULL, description LONGTEXT, status INT UNSIGNED NOT NULL, PRIMARY KEY (id))");

//Create Bulk SMS Sender ID Table
$create_bulk_sms_sender_id_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_bulk_sms_sender_id (id INT NOT NULL AUTO_INCREMENT, vendor_id INT UNSIGNED NOT NULL, username VARCHAR(225) NOT NULL, sender_id VARCHAR(225) NOT NULL, status INT UNSIGNED NOT NULL, date TIMESTAMP DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (id))");

//Alter Bulk SMS Sender ID Table
$check_col_sms = mysqli_query($connection_server, "SHOW COLUMNS FROM `sas_bulk_sms_sender_id` LIKE 'sample_message'");
if (mysqli_num_rows($check_col_sms) == 0) {
    mysqli_query($connection_server, "ALTER TABLE sas_bulk_sms_sender_id ADD COLUMN sample_message VARCHAR(225) NOT NULL AFTER sender_id");
}

//Create Shared Data Status Table
$create_shared_data_status_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_shared_data_status (id INT NOT NULL AUTO_INCREMENT, vendor_id INT UNSIGNED NOT NULL, api_id INT UNSIGNED NOT NULL, product_name VARCHAR(225) NOT NULL, description LONGTEXT, status INT UNSIGNED NOT NULL, PRIMARY KEY (id))");

//Create SME Data Status Table
$create_sme_data_status_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_sme_data_status (id INT NOT NULL AUTO_INCREMENT, vendor_id INT UNSIGNED NOT NULL, api_id INT UNSIGNED NOT NULL, product_name VARCHAR(225) NOT NULL, description LONGTEXT, status INT UNSIGNED NOT NULL, PRIMARY KEY (id))");

//Create CG Data Status Table
$create_cg_data_status_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_cg_data_status (id INT NOT NULL AUTO_INCREMENT, vendor_id INT UNSIGNED NOT NULL, api_id INT UNSIGNED NOT NULL, product_name VARCHAR(225) NOT NULL, description LONGTEXT, status INT UNSIGNED NOT NULL, PRIMARY KEY (id))");

//Create DD Data Status Table
$create_dd_data_status_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_dd_data_status (id INT NOT NULL AUTO_INCREMENT, vendor_id INT UNSIGNED NOT NULL, api_id INT UNSIGNED NOT NULL, product_name VARCHAR(225) NOT NULL, description LONGTEXT, status INT UNSIGNED NOT NULL, PRIMARY KEY (id))");

//Create Bulk Airtime & Data Table
$create_bulk_product_purchase_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_bulk_product_purchase (id INT NOT NULL AUTO_INCREMENT, vendor_id INT UNSIGNED NOT NULL, username VARCHAR(225) NOT NULL, product_name VARCHAR(225) NOT NULL, batch_number INT UNSIGNED NOT NULL, date TIMESTAMP DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (id))");

//Create Cable Status Table
$create_cable_status_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_cable_status (id INT NOT NULL AUTO_INCREMENT, vendor_id INT UNSIGNED NOT NULL, api_id INT UNSIGNED NOT NULL, product_name VARCHAR(225) NOT NULL, description LONGTEXT, status INT UNSIGNED NOT NULL, PRIMARY KEY (id))");

//Create Exam Status Table
$create_exam_status_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_exam_status (id INT NOT NULL AUTO_INCREMENT, vendor_id INT UNSIGNED NOT NULL, api_id INT UNSIGNED NOT NULL, product_name VARCHAR(225) NOT NULL, description LONGTEXT, status INT UNSIGNED NOT NULL, PRIMARY KEY (id))");

//Create Electric Status Table
$create_electric_status_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_electric_status (id INT NOT NULL AUTO_INCREMENT, vendor_id INT UNSIGNED NOT NULL, api_id INT UNSIGNED NOT NULL, product_name VARCHAR(225) NOT NULL, description LONGTEXT, status INT UNSIGNED NOT NULL, PRIMARY KEY (id))");

//Create Electric Purchased Table
$create_electric_purchased_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_electric_purchaseds (id INT NOT NULL AUTO_INCREMENT, vendor_id INT UNSIGNED NOT NULL, username VARCHAR(225) NOT NULL, reference VARCHAR(225) NOT NULL, meter_provider VARCHAR(225) NOT NULL, meter_type VARCHAR(225) NOT NULL, meter_number VARCHAR(225), meter_token VARCHAR(225) NOT NULL, token_unit VARCHAR(225) NOT NULL, date TIMESTAMP DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (id))");

if ($create_electric_purchased_table) {
    //Alter Electric Purchase Table
    $check_col_electric = mysqli_query($connection_server, "SHOW COLUMNS FROM `sas_electric_purchaseds` LIKE 'meter_owner_name'");
    if (mysqli_num_rows($check_col_electric) == 0) {
        mysqli_query($connection_server, "ALTER TABLE sas_electric_purchaseds ADD COLUMN meter_owner_name VARCHAR(225) NOT NULL AFTER reference, ADD COLUMN meter_owner_address VARCHAR(225) NOT NULL AFTER meter_owner_name");
    }
}

//Create Datacard Status Table
$create_datacard_status_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_datacard_status (id INT NOT NULL AUTO_INCREMENT, vendor_id INT UNSIGNED NOT NULL, api_id INT UNSIGNED NOT NULL, product_name VARCHAR(225) NOT NULL, description LONGTEXT, status INT UNSIGNED NOT NULL, PRIMARY KEY (id))");

//Create Rechargecard Status Table
$create_rechargecard_status_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_rechargecard_status (id INT NOT NULL AUTO_INCREMENT, vendor_id INT UNSIGNED NOT NULL, api_id INT UNSIGNED NOT NULL, product_name VARCHAR(225) NOT NULL, description LONGTEXT, status INT UNSIGNED NOT NULL, PRIMARY KEY (id))");

//Create Card Table
$create_card_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_cards (id INT NOT NULL AUTO_INCREMENT, vendor_id INT UNSIGNED NOT NULL, card_name VARCHAR(225) NOT NULL, cards LONGTEXT, dial_code VARCHAR(225) NOT NULL, PRIMARY KEY (id))");

//Create Card Purchased Table
$create_card_purchased_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_card_purchaseds (id INT NOT NULL AUTO_INCREMENT, vendor_id INT UNSIGNED NOT NULL, reference VARCHAR(225) NOT NULL, card_type VARCHAR(225) NOT NULL, username VARCHAR(225) NOT NULL, business_name VARCHAR(225), card_name VARCHAR(225) NOT NULL, cards LONGTEXT, date TIMESTAMP DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (id))");

//Create Naira Card Status Table
$create_nairacard_status_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_nairacard_status (id INT NOT NULL AUTO_INCREMENT, vendor_id INT UNSIGNED NOT NULL, api_id INT UNSIGNED NOT NULL, product_name VARCHAR(225) NOT NULL, description LONGTEXT, status INT UNSIGNED NOT NULL, PRIMARY KEY (id))");

//Create Dollar Card Status Table
$create_dollarcard_status_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_dollarcard_status (id INT NOT NULL AUTO_INCREMENT, vendor_id INT UNSIGNED NOT NULL, api_id INT UNSIGNED NOT NULL, product_name VARCHAR(225) NOT NULL, description LONGTEXT, status INT UNSIGNED NOT NULL, PRIMARY KEY (id))");

//Create Virtual Card Holder Table
$create_virtualcard_holder_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_virtualcard_holders (id INT NOT NULL AUTO_INCREMENT, vendor_id INT UNSIGNED NOT NULL, holder_id VARCHAR(225) NOT NULL, username VARCHAR(225) NOT NULL, firstname VARCHAR(225), lastname VARCHAR(225), email VARCHAR(225) NOT NULL, phone_number VARCHAR(225) NOT NULL, `address` VARCHAR(225) NOT NULL, `city` VARCHAR(225) NOT NULL, `state` VARCHAR(225) NOT NULL, country VARCHAR(225) NOT NULL, zipcode VARCHAR(225) NOT NULL, kyc_mode VARCHAR(225) NOT NULL, kyc_id VARCHAR(225) NOT NULL, selfie_id_url VARCHAR(225), api_website VARCHAR(225) NOT NULL, card_holder_status VARCHAR(225) NOT NULL, date TIMESTAMP DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (id))");

//Create Virtual Card Purchased Table
$create_virtualcard_purchased_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_virtualcard_purchaseds (id INT NOT NULL AUTO_INCREMENT, vendor_id INT UNSIGNED NOT NULL, reference VARCHAR(225) NOT NULL, card_id VARCHAR(225) NOT NULL, holder_id VARCHAR(225) NOT NULL, card_holder_id VARCHAR(225) NOT NULL, card_type VARCHAR(225) NOT NULL, username VARCHAR(225) NOT NULL, fullname VARCHAR(225), card_balance DECIMAL(65,30) UNSIGNED NOT NULL, card_name VARCHAR(225) NOT NULL, card_cvv VARCHAR(225) NOT NULL, card_validity VARCHAR(225) NOT NULL, card_address VARCHAR(225) NOT NULL, card_state VARCHAR(225) NOT NULL, card_country VARCHAR(225) NOT NULL, card_zipcode VARCHAR(225) NOT NULL, card_number LONGTEXT, card_pin VARCHAR(225) NOT NULL, card_currency VARCHAR(225) NOT NULL, card_brand VARCHAR(225) NOT NULL, card_callback_url VARCHAR(225), api_website VARCHAR(225) NOT NULL, card_status VARCHAR(225) NOT NULL, date TIMESTAMP DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (id))");

//Create Virtual Card OTP Table
$create_virtualcard_transaction_otp_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_virtualcard_transaction_otp (vendor_id INT UNSIGNED NOT NULL, reference VARCHAR(225) NOT NULL, api_reference VARCHAR(225), username VARCHAR(225) NOT NULL, card_id VARCHAR(225) NOT NULL, card_pan VARCHAR(225) NOT NULL, otp VARCHAR(225) NOT NULL, request_source VARCHAR(225) NOT NULL, api_website VARCHAR(225) NOT NULL, date TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");

//Create SMART PARAMETER VALUE Table
$create_smart_parameter_value_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_smart_parameter_values (vendor_id INT UNSIGNED NOT NULL, api_id INT UNSIGNED NOT NULL, product_id INT UNSIGNED NOT NULL, val_1 VARCHAR(225), val_2 VARCHAR(225), val_3 VARCHAR(225), val_4 VARCHAR(225), val_5 VARCHAR(225), val_6 VARCHAR(225), val_7 VARCHAR(225), val_8 VARCHAR(225), val_9 VARCHAR(225), val_10 VARCHAR(225))");

//Create AGENT PARAMETER VALUE Table
$create_agent_parameter_value_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_agent_parameter_values (vendor_id INT UNSIGNED NOT NULL, api_id INT UNSIGNED NOT NULL, product_id INT UNSIGNED NOT NULL, val_1 VARCHAR(225), val_2 VARCHAR(225), val_3 VARCHAR(225), val_4 VARCHAR(225), val_5 VARCHAR(225), val_6 VARCHAR(225), val_7 VARCHAR(225), val_8 VARCHAR(225), val_9 VARCHAR(225), val_10 VARCHAR(225))");

//Create API PARAMETER VALUE Table
$create_api_parameter_value_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_api_parameter_values (vendor_id INT UNSIGNED NOT NULL, api_id INT UNSIGNED NOT NULL, product_id INT UNSIGNED NOT NULL, val_1 VARCHAR(225), val_2 VARCHAR(225), val_3 VARCHAR(225), val_4 VARCHAR(225), val_5 VARCHAR(225), val_6 VARCHAR(225), val_7 VARCHAR(225), val_8 VARCHAR(225), val_9 VARCHAR(225), val_10 VARCHAR(225))");

//Create DOLLAR EXCHANGE RATE Table
$create_dollar_exchange_rate_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_dollar_exchange_rates (vendor_id INT UNSIGNED NOT NULL, product_type VARCHAR(225), currency VARCHAR(225), credit_amount VARCHAR(225), debit_amount VARCHAR(225))");

//Create Virtual Card Holder Table
// $create_virtualcard_holder_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_crypto_customer_holders (id INT NOT NULL AUTO_INCREMENT, vendor_id INT UNSIGNED NOT NULL, customer_id VARCHAR(225) NOT NULL, api_customer_id VARCHAR(225), username VARCHAR(225) NOT NULL, firstname VARCHAR(225), lastname VARCHAR(225), email VARCHAR(225) NOT NULL, phone_number VARCHAR(225) NOT NULL, `address` VARCHAR(225) NOT NULL, `city` VARCHAR(225) NOT NULL, `state` VARCHAR(225) NOT NULL, country VARCHAR(225) NOT NULL, zipcode VARCHAR(225) NOT NULL, api_website VARCHAR(225) NOT NULL, customer_status VARCHAR(225) NOT NULL, date TIMESTAMP DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (id))");

//Create User Transaction Table
$create_user_crypto_wallet_transaction_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_user_crypto_wallet_transactions (vendor_id INT UNSIGNED NOT NULL,  username VARCHAR(225) NOT NULL, from_currency VARCHAR(225) NOT NULL, to_currency VARCHAR(225) NOT NULL, from_address VARCHAR(225) NOT NULL, beneficiary_id VARCHAR(225) NOT NULL, amount DECIMAL(65,30) UNSIGNED NOT NULL, discounted_amount DECIMAL(65,30) UNSIGNED NOT NULL, balance_before DECIMAL(65,30) UNSIGNED NOT NULL, balance_after DECIMAL(65,30) UNSIGNED NOT NULL, description LONGTEXT NOT NULL, mode VARCHAR(225) NOT NULL, reference VARCHAR(225) NOT NULL, api_reference VARCHAR(225), api_website VARCHAR(225) NOT NULL, status INT UNSIGNED NOT NULL, date TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");

//Create Crypto Customer Holder Table
$create_crypto_customer_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_crypto_customer_holders (id INT NOT NULL AUTO_INCREMENT, vendor_id INT UNSIGNED NOT NULL, customer_id VARCHAR(225) NOT NULL, api_customer_id VARCHAR(225), username VARCHAR(225) NOT NULL, firstname VARCHAR(225), lastname VARCHAR(225), email VARCHAR(225) NOT NULL, phone_number VARCHAR(225) NOT NULL, `address` VARCHAR(225) NOT NULL, `city` VARCHAR(225) NOT NULL, `state` VARCHAR(225) NOT NULL, country VARCHAR(225) NOT NULL, zipcode VARCHAR(225) NOT NULL, api_website VARCHAR(225) NOT NULL, customer_status VARCHAR(225) NOT NULL, date TIMESTAMP DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (id))");

//Create Crypto Transfer Beneficiary Table
$create_crypto_beneficiary_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_crypto_beneficiary (id INT NOT NULL AUTO_INCREMENT, vendor_id INT UNSIGNED NOT NULL, beneficiary_id VARCHAR(225) NOT NULL, api_beneficiary_id VARCHAR(225), username VARCHAR(225) NOT NULL, label VARCHAR(225), currency VARCHAR(225), crypto_chain VARCHAR(225) NOT NULL, crypto_address VARCHAR(225) NOT NULL, api_website VARCHAR(225) NOT NULL, date TIMESTAMP DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (id))");

//Create Crypto Ledger Table
$create_crypto_ledger_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_user_crypto_ledger_balance (vendor_id INT UNSIGNED NOT NULL, username VARCHAR(225) NOT NULL, email VARCHAR(225), phone_number VARCHAR(225), currency VARCHAR(225) NOT NULL, crypto_address VARCHAR(225), crypto_chain VARCHAR(225), wallet_balance DECIMAL(65,30) UNSIGNED NOT NULL, wallet_id VARCHAR(225), api_wallet_id VARCHAR(225), api_customer_id VARCHAR(225), status INT UNSIGNED NOT NULL)");

//Create User Crypto Transaction Table
$create_user_crypto_transaction_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_crypto_transactions (vendor_id INT UNSIGNED NOT NULL, currency VARCHAR(225) NOT NULL, type_alternative VARCHAR(225), reference VARCHAR(225) NOT NULL, api_reference VARCHAR(225), username VARCHAR(225) NOT NULL, amount DECIMAL(65,30) UNSIGNED NOT NULL, discounted_amount DECIMAL(65,30) UNSIGNED NOT NULL, balance_before DECIMAL(65,30) UNSIGNED NOT NULL, balance_after DECIMAL(65,30) UNSIGNED NOT NULL, description LONGTEXT NOT NULL, mode VARCHAR(225) NOT NULL, api_website VARCHAR(225) NOT NULL, status INT UNSIGNED NOT NULL, date TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");

//Create Crypto Swap Table
$create_crypto_swap_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_user_crypto_swap (vendor_id INT UNSIGNED NOT NULL, username VARCHAR(225) NOT NULL, source_currency VARCHAR(225) NOT NULL, target_currency VARCHAR(225) NOT NULL, rate DECIMAL(65,30) UNSIGNED NOT NULL, type VARCHAR(225), swap_id VARCHAR(225), api_swap_id VARCHAR(225), api_website VARCHAR(225) NOT NULL, status INT UNSIGNED NOT NULL)");

//Create SMART CRYPTO EXCHANGE FEE PARAMETER VALUE Table
$create_smart_exchange_fee_parameter_value_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_smart_exchange_fee_parameter_values (vendor_id INT UNSIGNED NOT NULL, api_id INT UNSIGNED NOT NULL, product_id INT UNSIGNED NOT NULL, val_1 VARCHAR(225), val_2 VARCHAR(225), val_3 VARCHAR(225), val_4 VARCHAR(225), val_5 VARCHAR(225), val_6 VARCHAR(225), val_7 VARCHAR(225), val_8 VARCHAR(225), val_9 VARCHAR(225), val_10 VARCHAR(225))");

//Create AGENT CRYPTO EXCHANGE FEE PARAMETER VALUE Table
$create_agent_exchange_fee_parameter_value_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_agent_exchange_fee_parameter_values (vendor_id INT UNSIGNED NOT NULL, api_id INT UNSIGNED NOT NULL, product_id INT UNSIGNED NOT NULL, val_1 VARCHAR(225), val_2 VARCHAR(225), val_3 VARCHAR(225), val_4 VARCHAR(225), val_5 VARCHAR(225), val_6 VARCHAR(225), val_7 VARCHAR(225), val_8 VARCHAR(225), val_9 VARCHAR(225), val_10 VARCHAR(225))");

//Create API CRYPTO EXCHANGE FEE  PARAMETER VALUE Table
$create_api_exchange_fee_parameter_value_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_api_exchange_fee_parameter_values (vendor_id INT UNSIGNED NOT NULL, api_id INT UNSIGNED NOT NULL, product_id INT UNSIGNED NOT NULL, val_1 VARCHAR(225), val_2 VARCHAR(225), val_3 VARCHAR(225), val_4 VARCHAR(225), val_5 VARCHAR(225), val_6 VARCHAR(225), val_7 VARCHAR(225), val_8 VARCHAR(225), val_9 VARCHAR(225), val_10 VARCHAR(225))");

//Create User Payment Link Transaction Table
$create_user_payment_link_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_payment_links (vendor_id INT UNSIGNED NOT NULL, username VARCHAR(225) NOT NULL, currency VARCHAR(225) NOT NULL, reference VARCHAR(225) NOT NULL, api_reference VARCHAR(225), amount DECIMAL(65,30) UNSIGNED NOT NULL, description LONGTEXT NOT NULL, min_qty VARCHAR(225) NOT NULL, max_qty VARCHAR(225) NOT NULL, api_website VARCHAR(225) NOT NULL, status INT UNSIGNED NOT NULL, date TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");


//Create SMART CARD FUNDING PARAMETER VALUE Table
$create_smart_card_funding_parameter_value_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_smart_card_funding_parameter_values (vendor_id INT UNSIGNED NOT NULL, api_id INT UNSIGNED NOT NULL, product_id INT UNSIGNED NOT NULL, val_1 VARCHAR(225), val_2 VARCHAR(225), val_3 VARCHAR(225), val_4 VARCHAR(225), val_5 VARCHAR(225), val_6 VARCHAR(225), val_7 VARCHAR(225), val_8 VARCHAR(225), val_9 VARCHAR(225), val_10 VARCHAR(225))");

//Create AGENT CARD FUNDING PARAMETER VALUE Table
$create_agent_card_funding_parameter_value_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_agent_card_funding_parameter_values (vendor_id INT UNSIGNED NOT NULL, api_id INT UNSIGNED NOT NULL, product_id INT UNSIGNED NOT NULL, val_1 VARCHAR(225), val_2 VARCHAR(225), val_3 VARCHAR(225), val_4 VARCHAR(225), val_5 VARCHAR(225), val_6 VARCHAR(225), val_7 VARCHAR(225), val_8 VARCHAR(225), val_9 VARCHAR(225), val_10 VARCHAR(225))");

//Create API CARD FUNDING PARAMETER VALUE Table
$create_api_card_funding_parameter_value_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_api_card_funding_parameter_values (vendor_id INT UNSIGNED NOT NULL, api_id INT UNSIGNED NOT NULL, product_id INT UNSIGNED NOT NULL, val_1 VARCHAR(225), val_2 VARCHAR(225), val_3 VARCHAR(225), val_4 VARCHAR(225), val_5 VARCHAR(225), val_6 VARCHAR(225), val_7 VARCHAR(225), val_8 VARCHAR(225), val_9 VARCHAR(225), val_10 VARCHAR(225))");

//Create SMART CARD TRANSACTION PARAMETER VALUE Table
$create_smart_card_transaction_parameter_value_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_smart_card_transaction_parameter_values (vendor_id INT UNSIGNED NOT NULL, api_id INT UNSIGNED NOT NULL, product_id INT UNSIGNED NOT NULL, val_1 VARCHAR(225), val_2 VARCHAR(225), val_3 VARCHAR(225), val_4 VARCHAR(225), val_5 VARCHAR(225), val_6 VARCHAR(225), val_7 VARCHAR(225), val_8 VARCHAR(225), val_9 VARCHAR(225), val_10 VARCHAR(225))");

//Create AGENT CARD TRANSACTION PARAMETER VALUE Table
$create_agent_card_transaction_parameter_value_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_agent_card_transaction_parameter_values (vendor_id INT UNSIGNED NOT NULL, api_id INT UNSIGNED NOT NULL, product_id INT UNSIGNED NOT NULL, val_1 VARCHAR(225), val_2 VARCHAR(225), val_3 VARCHAR(225), val_4 VARCHAR(225), val_5 VARCHAR(225), val_6 VARCHAR(225), val_7 VARCHAR(225), val_8 VARCHAR(225), val_9 VARCHAR(225), val_10 VARCHAR(225))");

//Create API CARD TRANSACTION PARAMETER VALUE Table
$create_api_card_transaction_parameter_value_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_api_card_transaction_parameter_values (vendor_id INT UNSIGNED NOT NULL, api_id INT UNSIGNED NOT NULL, product_id INT UNSIGNED NOT NULL, val_1 VARCHAR(225), val_2 VARCHAR(225), val_3 VARCHAR(225), val_4 VARCHAR(225), val_5 VARCHAR(225), val_6 VARCHAR(225), val_7 VARCHAR(225), val_8 VARCHAR(225), val_9 VARCHAR(225), val_10 VARCHAR(225))");

// Check for and add status column to pricing tables for older databases
$pricing_tables = ['sas_smart_parameter_values', 'sas_agent_parameter_values', 'sas_api_parameter_values'];
foreach ($pricing_tables as $table) {
    $check_col = mysqli_query($connection_server, "SHOW COLUMNS FROM `$table` LIKE 'status'");
    if (mysqli_num_rows($check_col) == 0) {
        mysqli_query($connection_server, "ALTER TABLE `$table` ADD COLUMN status TINYINT(1) NOT NULL DEFAULT 1");
    }
}

//Create User Transaction Table
$create_user_transaction_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_transactions (vendor_id INT UNSIGNED NOT NULL, api_id INT UNSIGNED, product_id INT UNSIGNED, product_unique_id VARCHAR(225) NOT NULL, type_alternative VARCHAR(225), reference VARCHAR(225) NOT NULL, api_reference VARCHAR(225), username VARCHAR(225) NOT NULL, amount DECIMAL(65,30) UNSIGNED NOT NULL, discounted_amount DECIMAL(65,30) UNSIGNED NOT NULL, balance_before DECIMAL(65,30) UNSIGNED NOT NULL, balance_after DECIMAL(65,30) UNSIGNED NOT NULL, description LONGTEXT NOT NULL, mode VARCHAR(225) NOT NULL, api_website VARCHAR(225) NOT NULL, status INT UNSIGNED NOT NULL, date TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");

if ($create_user_transaction_table) {
    //Alter User Transaction Table
    $check_col_trans = mysqli_query($connection_server, "SHOW COLUMNS FROM `sas_transactions` LIKE 'batch_number'");
    if (mysqli_num_rows($check_col_trans) == 0) {
        mysqli_query($connection_server, "ALTER TABLE sas_transactions ADD COLUMN batch_number INT UNSIGNED AFTER reference");
    }
}

//Create Vendor Transaction Table
$create_vendor_transaction_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_vendor_transactions (vendor_id INT UNSIGNED NOT NULL, product_unique_id VARCHAR(225) NOT NULL, type_alternative VARCHAR(225), reference VARCHAR(225) NOT NULL, amount DECIMAL(65,30) UNSIGNED NOT NULL, discounted_amount DECIMAL(65,30) UNSIGNED NOT NULL, balance_before DECIMAL(65,30) UNSIGNED NOT NULL, balance_after DECIMAL(65,30) UNSIGNED NOT NULL, description LONGTEXT NOT NULL, api_website VARCHAR(225) NOT NULL, status INT UNSIGNED NOT NULL, date TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");

//Create Points Log Table
mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS points_log (id INT AUTO_INCREMENT PRIMARY KEY, user_id INT, point_amount INT, log_type VARCHAR(255), timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");

//Create Coin Settings Table
mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_coin_settings (vendor_id INT UNSIGNED PRIMARY KEY, streak_1 INT DEFAULT 20, streak_2 INT DEFAULT 20, streak_3 INT DEFAULT 20, streak_4 INT DEFAULT 20, streak_5 INT DEFAULT 20, streak_6 INT DEFAULT 20, streak_7 INT DEFAULT 20, coins_per_naira INT DEFAULT 20, affiliate_reward INT DEFAULT 100)");

// Check for coins_per_naira column and add if it doesn't exist (handle update from previous implementation)
$check_col_coins = mysqli_query($connection_server, "SHOW COLUMNS FROM `sas_coin_settings` LIKE 'coins_per_naira'");
if (mysqli_num_rows($check_col_coins) == 0) {
    mysqli_query($connection_server, "ALTER TABLE sas_coin_settings ADD COLUMN coins_per_naira INT DEFAULT 20");
}

// Check for affiliate_reward column
$check_col_aff = mysqli_query($connection_server, "SHOW COLUMNS FROM `sas_coin_settings` LIKE 'affiliate_reward'");
if (mysqli_num_rows($check_col_aff) == 0) {
    mysqli_query($connection_server, "ALTER TABLE sas_coin_settings ADD COLUMN affiliate_reward INT DEFAULT 100");
}

//Create Daily Product Tracker Table
$create_daily_product_tracker_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_daily_purchase_tracker (vendor_id INT UNSIGNED NOT NULL, reference VARCHAR(225) NOT NULL, product_type VARCHAR(225) NOT NULL, product_id VARCHAR(225) NOT NULL, username VARCHAR(225) NOT NULL, date_purchased VARCHAR(225) NOT NULL, date TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");

//Create Daily Validated Product Tracker Table
$create_daily_validated_product_tracker_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_validated_user_purchase_id_list (vendor_id INT UNSIGNED NOT NULL, product_id VARCHAR(225) NOT NULL, date TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");

//Create Daily Product Limit Table
$create_daily_product_limit_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_daily_purchase_limit (vendor_id INT UNSIGNED NOT NULL, `limit` INT UNSIGNED NOT NULL, date TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");

//Create Admin Payment Order Table
$create_admin_payment_order_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_admin_payment_orders (vendor_id INT UNSIGNED NOT NULL, min_amount VARCHAR(225) NOT NULL, max_amount VARCHAR(225) NOT NULL)");

//Create Admin Payment Table
$create_admin_payment_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_admin_payments (vendor_id INT UNSIGNED NOT NULL, bank_name VARCHAR(225) NOT NULL, account_name VARCHAR(225) NOT NULL, account_number VARCHAR(225) NOT NULL, phone_number VARCHAR(225) NOT NULL, amount_charged VARCHAR(225) NOT NULL)");

//Create Super Admin Payment Order Table
$create_super_admin_payment_order_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_super_admin_payment_orders (min_amount VARCHAR(225) NOT NULL, max_amount VARCHAR(225) NOT NULL)");

//Create Super Admin Payment Table
$create_super_admin_payment_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_super_admin_payments (bank_name VARCHAR(225) NOT NULL, account_name VARCHAR(225) NOT NULL, account_number VARCHAR(225) NOT NULL, phone_number VARCHAR(225) NOT NULL, amount_charged VARCHAR(225) NOT NULL)");

//Create Submitted Payment Table
$create_submitted_payment_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_submitted_payments (vendor_id INT UNSIGNED NOT NULL, username VARCHAR(225) NOT NULL, reference VARCHAR(225) NOT NULL, amount DECIMAL(65,30) UNSIGNED NOT NULL, discounted_amount DECIMAL(65,30) UNSIGNED NOT NULL, description LONGTEXT NOT NULL, mode VARCHAR(225) NOT NULL, api_website VARCHAR(225) NOT NULL, status INT UNSIGNED NOT NULL, date TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");

//Create Fund Transfer Request Table
$create_fund_transfer_request_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_fund_transfer_requests (vendor_id INT UNSIGNED NOT NULL, username VARCHAR(225) NOT NULL, recipient_username VARCHAR(225) NOT NULL, reference VARCHAR(225) NOT NULL, amount DECIMAL(65,30) UNSIGNED NOT NULL, discounted_amount DECIMAL(65,30) UNSIGNED NOT NULL, description LONGTEXT NOT NULL, mode VARCHAR(225) NOT NULL, api_website VARCHAR(225) NOT NULL, status INT UNSIGNED NOT NULL, date TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");

//Create Super Admin Submitted Payment Table
$create_super_admin_submitted_payment_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_super_admin_submitted_payments (vendor_id INT UNSIGNED NOT NULL, reference VARCHAR(225) NOT NULL, amount DECIMAL(65,30) UNSIGNED NOT NULL, discounted_amount DECIMAL(65,30) UNSIGNED NOT NULL, description LONGTEXT NOT NULL, mode VARCHAR(225) NOT NULL, api_website VARCHAR(225) NOT NULL, status INT UNSIGNED NOT NULL, date TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");

//Create Payment Gateway Table
$create_payment_gateway_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_payment_gateways (vendor_id INT UNSIGNED NOT NULL, gateway_name VARCHAR(225) NOT NULL, public_key VARCHAR(225) NOT NULL, secret_key VARCHAR(225) NOT NULL, encrypt_key VARCHAR(225) NOT NULL, percentage VARCHAR(225) NOT NULL, status INT UNSIGNED NOT NULL, date TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");

//Create Payment Link Gateway Table
$create_payment_link_gateway_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_payment_link_gateways (vendor_id INT UNSIGNED NOT NULL, gateway_name VARCHAR(225) NOT NULL, public_key VARCHAR(225) NOT NULL, secret_key VARCHAR(225) NOT NULL, encrypt_key VARCHAR(225) NOT NULL, percentage VARCHAR(225) NOT NULL, status INT UNSIGNED NOT NULL, date TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");

//Create Bank Transfer Gateway Table
$create_bank_transfer_gateway_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_bank_transfer_gateways (vendor_id INT UNSIGNED NOT NULL, gateway_name VARCHAR(225) NOT NULL, public_key VARCHAR(225) NOT NULL, secret_key VARCHAR(225) NOT NULL, encrypt_key VARCHAR(225) NOT NULL, transfer_fee VARCHAR(225) NOT NULL, status INT UNSIGNED NOT NULL, date TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");

//Create Bank Transfers Table
$create_bank_transfer_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_bank_transfer_history (id INT NOT NULL AUTO_INCREMENT, vendor_id INT UNSIGNED NOT NULL, reference VARCHAR(225) NOT NULL, username VARCHAR(225) NOT NULL, amount DECIMAL(65,30) UNSIGNED NOT NULL, amount_charged DECIMAL(65,30) UNSIGNED NOT NULL, bank_code VARCHAR(225) NOT NULL, bank_name VARCHAR(225) NOT NULL, account_name VARCHAR(225) NOT NULL, account_number VARCHAR(225) NOT NULL, narration VARCHAR(225) NOT NULL, session_id VARCHAR(225) NOT NULL, date TIMESTAMP DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (id))");

//Create KYC Verification Table
$create_kyc_verification_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_kyc_verifications (vendor_id INT UNSIGNED NOT NULL, verification_name VARCHAR(225) NOT NULL, status INT UNSIGNED NOT NULL, date TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");

//Create Super Admin Admin Payment Gateway Table
$create_admin_payment_gateway_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_super_admin_payment_gateways (gateway_name VARCHAR(225) NOT NULL, public_key VARCHAR(225) NOT NULL, secret_key VARCHAR(225) NOT NULL, encrypt_key VARCHAR(225) NOT NULL, percentage VARCHAR(225) NOT NULL, status INT UNSIGNED NOT NULL, date TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");

//Create Super Admin KYC Verification Table
$create_admin_kyc_verification_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_super_admin_kyc_verifications (verification_name VARCHAR(225) NOT NULL, status INT UNSIGNED NOT NULL, date TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");

//Create Account Number Store Table
$create_account_number_store_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_account_number_store (vendor_id INT UNSIGNED NOT NULL, gateway_name VARCHAR(225) NOT NULL, public_key VARCHAR(225) NOT NULL, secret_key VARCHAR(225) NOT NULL, encrypt_key VARCHAR(225) NOT NULL, percentage VARCHAR(225) NOT NULL, status INT UNSIGNED NOT NULL, date TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");

//Create API Marketplace Table
$create_api_marketplace_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_api_marketplace_listings (id INT NOT NULL AUTO_INCREMENT, api_website VARCHAR(225) NOT NULL, api_type VARCHAR(225) NOT NULL, price VARCHAR(225) NOT NULL, description LONGTEXT NOT NULL, status INT UNSIGNED NOT NULL, date TIMESTAMP DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (id))");

//Create Site Details Table
	$create_site_detail_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_site_details (vendor_id INT UNSIGNED NOT NULL, site_title VARCHAR(225) NOT NULL, site_desc VARCHAR(225) NOT NULL, primary_color VARCHAR(7) NOT NULL DEFAULT '#198754')");

//Create Super Admin Site Details Table
$create_super_admin_site_detail_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_super_admin_site_details (site_title VARCHAR(225) NOT NULL, site_desc VARCHAR(225) NOT NULL)");

//Create Email Template Table
$create_email_template_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_email_templates (id INT NOT NULL AUTO_INCREMENT, vendor_id INT UNSIGNED NOT NULL, email_type VARCHAR(225) NOT NULL, subject VARCHAR(225) NOT NULL, body LONGTEXT NOT NULL, PRIMARY KEY (id))");

//Create Super Admin Email Template Table
$create_super_admin_email_template_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_super_admin_email_templates (id INT NOT NULL AUTO_INCREMENT, email_type VARCHAR(225) NOT NULL, subject VARCHAR(225) NOT NULL, body LONGTEXT NOT NULL, PRIMARY KEY (id))");

//Create Vendor Style Template Table
$create_vendor_style_templates_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_vendor_style_templates (vendor_id INT UNSIGNED NOT NULL, template_name VARCHAR(225) NOT NULL, date TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");

//Create Super Admin Style Template Table
$create_spadmin_style_templates_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_spadmin_style_templates (template_name VARCHAR(225) NOT NULL, date TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");

$test_check_admin_table_exists = mysqli_query($connection_server, "SELECT * FROM sas_super_admin");
// Test Infos
if (mysqli_num_rows($test_check_admin_table_exists) == 0) {
	//$def_super_admin_pass = md5("12345678");
	//mysqli_query($connection_server, "INSERT INTO sas_super_admin (email, password, firstname, lastname, phone_number, gender, home_address, status) VALUES ('admin@example.com','$def_super_admin_pass','VTU','Administrator','08124232128','male', 'No 72, Edun Isale Akanni Ilorin', '1')");
	// mysqli_query($connection_server, "INSERT INTO sas_vendors (website_url, email, password, firstname, lastname, phone_number, balance, home_address, status) VALUES ('".$_SERVER["HTTP_HOST"]."', 'beebayads@gmail.com', '".md5("12345678")."', 'Omotere', 'Ebenezer', '08124232128', '500000', 'Ilorin, Kwara', '1')");

	// mysqli_query($connection_server, "INSERT INTO sas_users (vendor_id, email, username, password, phone_number, balance, firstname, lastname, home_address, account_level, api_key, api_status, status) VALUES ('1', 'beebayads@gmail.com', 'realbeebay', '".md5("12345678")."', '08124232128', '1500000', 'Abdulrahaman', 'Habeebullahi', 'Ilorin, Kwara', '1', 'hsye6rtsJdu5sh44wgh589evtoyee6rri654h', '1', '1')");
	// mysqli_query($connection_server, "INSERT INTO sas_users (vendor_id, email, username, password, phone_number, balance, firstname, lastname, home_address, account_level, api_key, api_status, status) VALUES ('1', 'beebsnaija@gmail.com', 'philmore', '".md5("12345678")."', '08124232128', '1500000', 'Omotere', 'Ebenezer', 'Ikeja, Lagos', '1', 'uyuioeuyurioporjkf77685uiuguir78rriu74', '1', '1')");

	// //User Upgrade Price
	// mysqli_query($connection_server, "INSERT INTO sas_user_upgrade_price (vendor_id, account_type, price) VALUES ('1', '1', '1')");
	// mysqli_query($connection_server, "INSERT INTO sas_user_upgrade_price (vendor_id, account_type, price) VALUES ('1', '2', '3500')");
	// mysqli_query($connection_server, "INSERT INTO sas_user_upgrade_price (vendor_id, account_type, price) VALUES ('1', '3', '5000')");

	// //Referral Percentage
	// mysqli_query($connection_server, "INSERT INTO sas_referral_percents (vendor_id, account_level, percentage) VALUES ('1', '2', '15')");
	// mysqli_query($connection_server, "INSERT INTO sas_referral_percents (vendor_id, account_level, percentage) VALUES ('1', '3', '20')");

	//Products List
	/*mysqli_query($connection_server, "INSERT INTO sas_products (vendor_id, product_name, status) VALUES ('1', 'mtn', '1')");
			 mysqli_query($connection_server, "INSERT INTO sas_products (vendor_id, product_name, status) VALUES ('1', 'airtel', '1')");
			 mysqli_query($connection_server, "INSERT INTO sas_products (vendor_id, product_name, status) VALUES ('1', '9mobile', '1')");
			 mysqli_query($connection_server, "INSERT INTO sas_products (vendor_id, product_name, status) VALUES ('1', 'glo', '0')");
			 mysqli_query($connection_server, "INSERT INTO sas_products (vendor_id, product_name, status) VALUES ('1', 'startimes', '1')");
			 mysqli_query($connection_server, "INSERT INTO sas_products (vendor_id, product_name, status) VALUES ('1', 'dstv', '1')");
			 mysqli_query($connection_server, "INSERT INTO sas_products (vendor_id, product_name, status) VALUES ('1', 'gotv', '0')");
			 mysqli_query($connection_server, "INSERT INTO sas_products (vendor_id, product_name, status) VALUES ('1', 'waec', '1')");
			 mysqli_query($connection_server, "INSERT INTO sas_products (vendor_id, product_name, status) VALUES ('1', 'nabteb', '1')");
			 mysqli_query($connection_server, "INSERT INTO sas_products (vendor_id, product_name, status) VALUES ('1', 'neco', '1')");
			 mysqli_query($connection_server, "INSERT INTO sas_products (vendor_id, product_name, status) VALUES ('1', 'ikedc', '1')");
			 mysqli_query($connection_server, "INSERT INTO sas_products (vendor_id, product_name, status) VALUES ('1', 'jedc', '1')");
			 mysqli_query($connection_server, "INSERT INTO sas_products (vendor_id, product_name, status) VALUES ('1', 'ibedc', '1')");
			 */
	// //APIs
	// mysqli_query($connection_server, "INSERT INTO sas_apis (vendor_id, api_base_url, api_type, api_key, status) VALUES ('1', 'smartrechargeapi.com', 'airtime', 'iych6iz31vf8buljy18c87raktrlmjef44heettud98', '1')");
	// mysqli_query($connection_server, "INSERT INTO sas_apis (vendor_id, api_base_url, api_type, api_key, status) VALUES ('1', 'smartrecharge.ng', 'airtime', 'bkxwnqna9pzvqllm5qfdvvm9t6npw1pp5deid4kcu2j56x', '0')");
	// mysqli_query($connection_server, "INSERT INTO sas_apis (vendor_id, api_base_url, api_type, api_key, status) VALUES ('1', 'smartrechargeapi.com', 'sme-data', 'iych6iz31vf8buljy18c87raktrlmjef44heettud98', '1')");
	// mysqli_query($connection_server, "INSERT INTO sas_apis (vendor_id, api_base_url, api_type, api_key, status) VALUES ('1', 'smartrechargeapi.com', 'cable', 'iych6iz31vf8buljy18c87raktrlmjef44heettud98', '1')");
	// mysqli_query($connection_server, "INSERT INTO sas_apis (vendor_id, api_base_url, api_type, api_key, status) VALUES ('1', 'abumpay.com', 'exam', '8rfrusvnkr3wt90wvka5uvousceu8e3tg953sghgxby7', '1')");
	// mysqli_query($connection_server, "INSERT INTO sas_apis (vendor_id, api_base_url, api_type, api_key, status) VALUES ('1', 'smartrechargeapi.com', 'electric', 'iych6iz31vf8buljy18c87raktrlmjef44heettud98', '1')");
	// mysqli_query($connection_server, "INSERT INTO sas_apis (vendor_id, api_base_url, api_type, api_key, status) VALUES ('1', 'localserver', 'datacard', '1', '1')");
	// mysqli_query($connection_server, "INSERT INTO sas_apis (vendor_id, api_base_url, api_type, api_key, status) VALUES ('1', 'localserver', 'rechargecard', '1', '1')");

	//Airtime Status
	/*mysqli_query($connection_server, "INSERT INTO sas_airtime_status (vendor_id, api_id, product_name, status) VALUES ('1', '1', 'mtn', '1')");
			 mysqli_query($connection_server, "INSERT INTO sas_airtime_status (vendor_id, api_id, product_name, status) VALUES ('1', '2', 'airtel', '0')");
			 mysqli_query($connection_server, "INSERT INTO sas_airtime_status (vendor_id, api_id, product_name, status) VALUES ('1', '1', '9mobile', '1')");
			 mysqli_query($connection_server, "INSERT INTO sas_airtime_status (vendor_id, api_id, product_name, status) VALUES ('1', '1', 'glo', '1')");
			 */

	//Bulk SMS Sender ID
	// mysqli_query($connection_server, "INSERT INTO sas_bulk_sms_sender_id (vendor_id, username, sender_id, status) VALUES ('1', 'realbeebay', 'BeeTech', '1')");
	// mysqli_query($connection_server, "INSERT INTO sas_bulk_sms_sender_id (vendor_id, username, sender_id, status) VALUES ('1', 'realbeebay', 'D-Pally', '2')");
	// mysqli_query($connection_server, "INSERT INTO sas_bulk_sms_sender_id (vendor_id, username, sender_id, status) VALUES ('1', 'philmore', 'Datagifting', '1')");


	//Data Status
	/*mysqli_query($connection_server, "INSERT INTO sas_sme_data_status (vendor_id, api_id, product_name, status) VALUES ('1', '3', 'mtn', '1')");
			 mysqli_query($connection_server, "INSERT INTO sas_sme_data_status (vendor_id, api_id, product_name, status) VALUES ('1', '3', 'airtel', '0')");
			 mysqli_query($connection_server, "INSERT INTO sas_sme_data_status (vendor_id, api_id, product_name, status) VALUES ('1', '3', '9mobile', '1')");
			 mysqli_query($connection_server, "INSERT INTO sas_sme_data_status (vendor_id, api_id, product_name, status) VALUES ('1', '3', 'glo', '1')");
			 
			 mysqli_query($connection_server, "INSERT INTO sas_cg_data_status (vendor_id, api_id, product_name, status) VALUES ('1', '3', 'mtn', '1')");
			 mysqli_query($connection_server, "INSERT INTO sas_cg_data_status (vendor_id, api_id, product_name, status) VALUES ('1', '3', 'airtel', '0')");
			 mysqli_query($connection_server, "INSERT INTO sas_cg_data_status (vendor_id, api_id, product_name, status) VALUES ('1', '3', '9mobile', '1')");
			 mysqli_query($connection_server, "INSERT INTO sas_cg_data_status (vendor_id, api_id, product_name, status) VALUES ('1', '3', 'glo', '1')");
			 
			 mysqli_query($connection_server, "INSERT INTO sas_dd_data_status (vendor_id, api_id, product_name, status) VALUES ('1', '3', 'mtn', '1')");
			 mysqli_query($connection_server, "INSERT INTO sas_dd_data_status (vendor_id, api_id, product_name, status) VALUES ('1', '3', 'airtel', '0')");
			 mysqli_query($connection_server, "INSERT INTO sas_dd_data_status (vendor_id, api_id, product_name, status) VALUES ('1', '3', '9mobile', '1')");
			 mysqli_query($connection_server, "INSERT INTO sas_dd_data_status (vendor_id, api_id, product_name, status) VALUES ('1', '3', 'glo', '1')");
			 */
	//Cable Status
	/*mysqli_query($connection_server, "INSERT INTO sas_cable_status (vendor_id, api_id, product_name, status) VALUES ('1', '4', 'startimes', '1')");
			 mysqli_query($connection_server, "INSERT INTO sas_cable_status (vendor_id, api_id, product_name, status) VALUES ('1', '4', 'dstv', '0')");
			 mysqli_query($connection_server, "INSERT INTO sas_cable_status (vendor_id, api_id, product_name, status) VALUES ('1', '4', 'gotv', '1')");
			 */
	//Exam Status
	/*mysqli_query($connection_server, "INSERT INTO sas_exam_status (vendor_id, api_id, product_name, status) VALUES ('1', '5', 'neco', '1')");
			 mysqli_query($connection_server, "INSERT INTO sas_exam_status (vendor_id, api_id, product_name, status) VALUES ('1', '5', 'waec', '1')");
			 mysqli_query($connection_server, "INSERT INTO sas_exam_status (vendor_id, api_id, product_name, status) VALUES ('1', '5', 'nabteb', '0')");
			 */
	//Electric Status
	/*mysqli_query($connection_server, "INSERT INTO sas_electric_status (vendor_id, api_id, product_name, status) VALUES ('1', '6', 'ikedc', '1')");
			 mysqli_query($connection_server, "INSERT INTO sas_electric_status (vendor_id, api_id, product_name, status) VALUES ('1', '6', 'ibedc', '1')");
			 mysqli_query($connection_server, "INSERT INTO sas_electric_status (vendor_id, api_id, product_name, status) VALUES ('1', '6', 'jedc', '0')");
			 */
	//Datacard Status
	/*mysqli_query($connection_server, "INSERT INTO sas_datacard_status (vendor_id, api_id, product_name, status) VALUES ('1', '7', 'mtn', '1')");
			 mysqli_query($connection_server, "INSERT INTO sas_datacard_status (vendor_id, api_id, product_name, status) VALUES ('1', '7', 'airtel', '1')");
			 mysqli_query($connection_server, "INSERT INTO sas_datacard_status (vendor_id, api_id, product_name, status) VALUES ('1', '7', '9mobile', '1')");
			 mysqli_query($connection_server, "INSERT INTO sas_datacard_status (vendor_id, api_id, product_name, status) VALUES ('1', '7', 'glo', '1')");
			 */
	//Rechargecard Status
	/*mysqli_query($connection_server, "INSERT INTO sas_rechargecard_status (vendor_id, api_id, product_name, status) VALUES ('1', '8', 'mtn', '1')");
			 mysqli_query($connection_server, "INSERT INTO sas_rechargecard_status (vendor_id, api_id, product_name, status) VALUES ('1', '8', 'airtel', '1')");
			 mysqli_query($connection_server, "INSERT INTO sas_rechargecard_status (vendor_id, api_id, product_name, status) VALUES ('1', '8', '9mobile', '1')");
			 mysqli_query($connection_server, "INSERT INTO sas_rechargecard_status (vendor_id, api_id, product_name, status) VALUES ('1', '8', 'glo', '1')");
			 */
	//Airtime
	/*mysqli_query($connection_server, "INSERT INTO sas_smart_parameter_values (vendor_id, api_id, product_id, val_1) VALUES ('1', '1', '1', '2.5')");
			 mysqli_query($connection_server, "INSERT INTO sas_agent_parameter_values (vendor_id, api_id, product_id, val_1) VALUES ('1', '1', '1', '2.8')");
			 mysqli_query($connection_server, "INSERT INTO sas_api_parameter_values (vendor_id, api_id, product_id, val_1) VALUES ('1', '1', '1', '3.0')");

			 mysqli_query($connection_server, "INSERT INTO sas_smart_parameter_values (vendor_id, api_id, product_id, val_1) VALUES ('1', '2', '2', '2.5')");
			 mysqli_query($connection_server, "INSERT INTO sas_agent_parameter_values (vendor_id, api_id, product_id, val_1) VALUES ('1', '2', '2', '2.8')");
			 mysqli_query($connection_server, "INSERT INTO sas_api_parameter_values (vendor_id, api_id, product_id, val_1) VALUES ('1', '2', '2', '3.0')");
			 */
	//Data
	/*mysqli_query($connection_server, "INSERT INTO sas_smart_parameter_values (vendor_id, api_id, product_id, val_1, val_2) VALUES ('1', '3', '1', '500mb', '115')");
			 mysqli_query($connection_server, "INSERT INTO sas_agent_parameter_values (vendor_id, api_id, product_id, val_1, val_2) VALUES ('1', '3', '1', '500mb', '111')");
			 mysqli_query($connection_server, "INSERT INTO sas_api_parameter_values (vendor_id, api_id, product_id, val_1, val_2) VALUES ('1', '3', '1', '500mb', '109.5')");
			 
			 mysqli_query($connection_server, "INSERT INTO sas_smart_parameter_values (vendor_id, api_id, product_id, val_1, val_2) VALUES ('1', '3', '2', '500mb', '115')");
			 mysqli_query($connection_server, "INSERT INTO sas_agent_parameter_values (vendor_id, api_id, product_id, val_1, val_2) VALUES ('1', '3', '2', '500mb', '111')");
			 mysqli_query($connection_server, "INSERT INTO sas_api_parameter_values (vendor_id, api_id, product_id, val_1, val_2) VALUES ('1', '3', '2', '500mb', '109.5')");
			 */
	//Cable TV
	/*mysqli_query($connection_server, "INSERT INTO sas_smart_parameter_values (vendor_id, api_id, product_id, val_1, val_2) VALUES ('1', '4', '5', 'nova', '1500')");
			 mysqli_query($connection_server, "INSERT INTO sas_agent_parameter_values (vendor_id, api_id, product_id, val_1, val_2) VALUES ('1', '4', '5', 'nova', '1445')");
			 mysqli_query($connection_server, "INSERT INTO sas_api_parameter_values (vendor_id, api_id, product_id, val_1, val_2) VALUES ('1', '4', '5', 'nova', '1440')");
			 
			 mysqli_query($connection_server, "INSERT INTO sas_smart_parameter_values (vendor_id, api_id, product_id, val_1, val_2) VALUES ('1', '4', '7', 'jolli', '1500')");
			 mysqli_query($connection_server, "INSERT INTO sas_agent_parameter_values (vendor_id, api_id, product_id, val_1, val_2) VALUES ('1', '4', '7', 'jolli', '1445')");
			 mysqli_query($connection_server, "INSERT INTO sas_api_parameter_values (vendor_id, api_id, product_id, val_1, val_2) VALUES ('1', '4', '7', 'jolli', '1440')");
			 */
	//Exam PIN
	/*mysqli_query($connection_server, "INSERT INTO sas_smart_parameter_values (vendor_id, api_id, product_id, val_1, val_2) VALUES ('1', '5', '8', '1', '2500')");
			 mysqli_query($connection_server, "INSERT INTO sas_agent_parameter_values (vendor_id, api_id, product_id, val_1, val_2) VALUES ('1', '5', '8', '1', '2450')");
			 mysqli_query($connection_server, "INSERT INTO sas_api_parameter_values (vendor_id, api_id, product_id, val_1, val_2) VALUES ('1', '5', '8', '1', '2430')");
			 
			 mysqli_query($connection_server, "INSERT INTO sas_smart_parameter_values (vendor_id, api_id, product_id, val_1, val_2) VALUES ('1', '5', '9', '1', '1000')");
			 mysqli_query($connection_server, "INSERT INTO sas_agent_parameter_values (vendor_id, api_id, product_id, val_1, val_2) VALUES ('1', '5', '9', '1', '980')");
			 mysqli_query($connection_server, "INSERT INTO sas_api_parameter_values (vendor_id, api_id, product_id, val_1, val_2) VALUES ('1', '5', '9', '1', '975')");
			 */
	//Electric
	/*mysqli_query($connection_server, "INSERT INTO sas_smart_parameter_values (vendor_id, api_id, product_id, val_1) VALUES ('1', '6', '11', '1')");
			 mysqli_query($connection_server, "INSERT INTO sas_agent_parameter_values (vendor_id, api_id, product_id, val_1) VALUES ('1', '6', '11', '1.5')");
			 mysqli_query($connection_server, "INSERT INTO sas_api_parameter_values (vendor_id, api_id, product_id, val_1) VALUES ('1', '6', '11', '2')");
			 
			 mysqli_query($connection_server, "INSERT INTO sas_smart_parameter_values (vendor_id, api_id, product_id, val_1) VALUES ('1', '6', '13', '0.5')");
			 mysqli_query($connection_server, "INSERT INTO sas_agent_parameter_values (vendor_id, api_id, product_id, val_1) VALUES ('1', '6', '13', '1')");
			 mysqli_query($connection_server, "INSERT INTO sas_api_parameter_values (vendor_id, api_id, product_id, val_1) VALUES ('1', '6', '13', '1.5')");
			 */
	//Datacard
	/*mysqli_query($connection_server, "INSERT INTO sas_smart_parameter_values (vendor_id, api_id, product_id, val_1, val_2) VALUES ('1', '8', '1', '100', '99')");
			 mysqli_query($connection_server, "INSERT INTO sas_agent_parameter_values (vendor_id, api_id, product_id, val_1, val_2) VALUES ('1', '8', '1', '100', '97')");
			 mysqli_query($connection_server, "INSERT INTO sas_api_parameter_values (vendor_id, api_id, product_id, val_1, val_2) VALUES ('1', '8', '1', '100', '95')");

			 mysqli_query($connection_server, "INSERT INTO sas_smart_parameter_values (vendor_id, api_id, product_id, val_1, val_2) VALUES ('1', '8', '1', '200', '198')");
			 mysqli_query($connection_server, "INSERT INTO sas_agent_parameter_values (vendor_id, api_id, product_id, val_1, val_2) VALUES ('1', '8', '1', '200', '194')");
			 mysqli_query($connection_server, "INSERT INTO sas_api_parameter_values (vendor_id, api_id, product_id, val_1, val_2) VALUES ('1', '8', '1', '200', '190')");
			 */
	//Rechargecard
	/*mysqli_query($connection_server, "INSERT INTO sas_smart_parameter_values (vendor_id, api_id, product_id, val_1, val_2) VALUES ('1', '7', '1', '1gb', '315')");
			 mysqli_query($connection_server, "INSERT INTO sas_agent_parameter_values (vendor_id, api_id, product_id, val_1, val_2) VALUES ('1', '7', '1', '1gb', '308')");
			 mysqli_query($connection_server, "INSERT INTO sas_api_parameter_values (vendor_id, api_id, product_id, val_1, val_2) VALUES ('1', '7', '1', '1gb', '300')");
			 
			 mysqli_query($connection_server, "INSERT INTO sas_smart_parameter_values (vendor_id, api_id, product_id, val_1, val_2) VALUES ('1', '7', '1', '2gb', '630')");
			 mysqli_query($connection_server, "INSERT INTO sas_agent_parameter_values (vendor_id, api_id, product_id, val_1, val_2) VALUES ('1', '7', '1', '2gb', '616')");
			 mysqli_query($connection_server, "INSERT INTO sas_api_parameter_values (vendor_id, api_id, product_id, val_1, val_2) VALUES ('1', '7', '1', '2gb', '600')");
			 */
	//Transaction
	// mysqli_query($connection_server, "INSERT INTO sas_transactions (vendor_id, product_unique_id, type_alternative, reference, username, amount, discounted_amount, balance_before, balance_after, description, mode, api_website, status) VALUES ('1', '', 'Wallet Credit', '8754673636', 'realbeebay', '1500000', '1500000', '0', '1500000', 'Account Credited by Admin', 'WEB', '".$_SERVER["HTTP_HOST"]."', '1')");
	// mysqli_query($connection_server, "INSERT INTO sas_transactions (vendor_id, product_unique_id, type_alternative, reference, username, amount, discounted_amount, balance_before, balance_after, description, mode, api_website, status) VALUES ('1', '', 'Wallet Credit', '9874567856', 'philmore', '1500000', '1500000', '0', '1500000', 'Account Credited by Admin', 'WEB', '".$_SERVER["HTTP_HOST"]."', '1')");

	//Admin Payment Details
	// mysqli_query($connection_server, "INSERT INTO sas_admin_payments (vendor_id, bank_name, account_name, account_number, phone_number, amount_charged) VALUES ('1', 'UNITED BANK OF AFRICA', 'ABDULRAHAMAN HABEEBULLAHI', '2161120728', '08124232128', '50')");

	//API Market Listings
	// mysqli_query($connection_server, "INSERT INTO `sas_api_marketplace_listings` (api_website, api_type, price, description, status) VALUES ('https://abumpay.com', 'cable', '1500', 'Cable are as affordable as N1450','1')");
	// mysqli_query($connection_server, "INSERT INTO `sas_api_marketplace_listings` (api_website, api_type, price, description, status) VALUES ('https://clickpay.com.ng', 'airtime', '1500', 'MTN @ 7% per airtime, Airtel @ 9%, Glo @ 5% and 9mobile(formerly Etisalat) @ 8%','1')");
	// mysqli_query($connection_server, "INSERT INTO `sas_api_marketplace_listings` (api_website, api_type, price, description, status) VALUES ('https://clubkonnect.com', 'airtime', '1000', 'Airtel @ %8 per airtime','1')");
	// mysqli_query($connection_server, "INSERT INTO `sas_api_marketplace_listings` (api_website, api_type, price, description, status) VALUES ('https://smartrechargeapi.com', 'airtime', '1200', '9mobile @ %10 per airtime','1')");
	// mysqli_query($connection_server, "INSERT INTO `sas_api_marketplace_listings` (api_website, api_type, price, description, status) VALUES ('https://termii.com', 'bulk-sms', '3567.89', 'MTN SMS @ 2.14 naira','1')");

} else {
	/*echo mysqli_error($connection_server);*/
}

// --- Subscription and Blog Tables ---

//Create Vendor Subscription Log Table
$create_vendor_subscriptions_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS sas_vendor_subscriptions (id INT AUTO_INCREMENT PRIMARY KEY, vendor_id INT NOT NULL, package_id INT NOT NULL, purchase_date DATETIME NOT NULL, expiry_date DATE NOT NULL, amount_paid DECIMAL(10, 2) NOT NULL, INDEX (vendor_id), INDEX (package_id))");

//Create Blog Posts Table
$create_blog_posts_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS blog_posts (id INT AUTO_INCREMENT PRIMARY KEY, author_id INT NOT NULL, title VARCHAR(255) NOT NULL, content TEXT NOT NULL, featured_image VARCHAR(255) NULL, status ENUM('draft', 'published') NOT NULL DEFAULT 'draft', created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, INDEX (author_id), INDEX (status))");

//Create Blog Categories Table
$create_blog_categories_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS blog_categories (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(100) NOT NULL, slug VARCHAR(100) NOT NULL UNIQUE)");

//Create Blog Post/Category Association Table
$create_blog_post_categories_table = mysqli_query($connection_server, "CREATE TABLE IF NOT EXISTS blog_post_categories (id INT AUTO_INCREMENT PRIMARY KEY, post_id INT NOT NULL, category_id INT NOT NULL, INDEX (post_id), INDEX (category_id))");


include("bc-email-templates.php");
}
?>