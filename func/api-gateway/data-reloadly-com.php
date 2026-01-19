<?php
// Reloadly International Data Gateway

// 1. Get Access Token
$client_id = $api_detail["encrypt_key"];
$client_secret = $api_detail["api_key"];

$auth_url = "https://auth.reloadly.com/oauth/token";
$auth_data = [
    "client_id" => $client_id,
    "client_secret" => $client_secret,
    "grant_type" => "client_credentials",
    "audience" => "https://topups.reloadly.com"
];

$ch = curl_init($auth_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($auth_data));
curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json"]);
$auth_response = curl_exec($ch);
curl_close($ch);

$auth_json = json_decode($auth_response, true);

if (isset($auth_json["access_token"])) {
    $access_token = $auth_json["access_token"];

    // 2. Make Data Top-up Request
    $topup_url = "https://topups.reloadly.com/topups/data";
    $topup_data = [
        "dataPackageId" => $data_plan_id,
        "recipientPhone" => [
            "countryCode" => $country_code,
            "number" => $phone_no
        ]
    ];

    $ch = curl_init($topup_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($topup_data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $access_token",
        "Content-Type: application/json",
        "Accept: application/com.reloadly.topups-v1+json"
    ]);
    $topup_response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $topup_json = json_decode($topup_response, true);

    if ($http_code == 200 || $http_code == 201) {
        $api_response = "successful";
        $api_response_status = 1;
        $api_response_description = "International Data Successful. Ref: " . $topup_json["transactionId"];
        $api_response_reference = $topup_json["transactionId"];
        $api_response_text = "Transaction Successful";
    } else {
        $api_response = "failed";
        $api_response_description = "Reloadly Error: " . ($topup_json["message"] ?? "Unknown error");
    }
} else {
    $api_response = "failed";
    $api_response_description = "Reloadly Auth Failed: " . ($auth_json["message"] ?? "Unknown error");
}
?>
