<?php
require_once __DIR__ . '/config.secrets.php';

header('Content-Type: application/json');

// IP parameter
if (!isset($_GET['ipv4address'])) {
    http_response_code(400);
    die(json_encode(['error' => 'Missing parameter: ipv4address']));
}

$ip = $_GET['ipv4address'];
if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
    http_response_code(400);
    die(json_encode(['error' => "$ip is not a valid IPv4 address"]));
}

// Set timezone
date_default_timezone_set($timezone ?? 'UTC');

// Initialize cURL
$ch = curl_init();
$responses = [];

// Loop on zones and rrsets
foreach ($zones as $zone => $record_ids) {
    foreach ($record_ids as $record_id) {
        $url = "https://api.hetzner.cloud/v1/zones/$zone/rrsets/$record_id/actions/set_records";

        $body = json_encode([
            'records' => [[
                'value'   => $ip,
                'comment' => 'Updated by script on ' . date('Y-m-d H:i:s P')
            ]]
        ], JSON_UNESCAPED_SLASHES);

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json",
                "Authorization: Bearer $apitoken",
            ],
            CURLOPT_POSTFIELDS => $body,
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $responses[$zone][$record_id] = $response === false
            ? [
                'success' => false,
                'error'   => curl_error($ch),
                'code'    => curl_errno($ch)
              ]
            : [
                'success'   => $http_code >= 200 && $http_code < 300,
                'http_code' => $http_code,
                'response'  => json_decode($response, true)
              ];
    }
}

// Close cURL
curl_close($ch);

// JSON response
echo json_encode($responses, JSON_PRETTY_PRINT);
