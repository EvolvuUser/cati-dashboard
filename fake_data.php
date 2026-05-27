<?php

$apiUrl = "http://127.0.0.1:8000/api/v1/mobile-calls";
$apiKey = "mysecretkey123";

// Number of fake records
$totalRecords = 10;

$statuses = [
    "Completed",
    "Busy",
    "No Answer",
    "Disconnected",
    "Callback"
];

for ($i = 1; $i <= $totalRecords; $i++) {

    // Random timestamps
    $startEpoch = time() - rand(0, 86400 * 30); // last 30 days
    $duration = rand(30, 900); // 30 sec to 15 mins
    $endEpoch = $startEpoch + $duration;

    $data = [
        "db_no" => "DB" . rand(100000, 999999),

        // Random campaign ID from 1 to 1016
        "campaign_id" => (string) rand(1, 1016),

        "call_date" => date("Y-m-d", $startEpoch),
        "start_epoch" => $startEpoch,
        "end_epoch" => $endEpoch,
        "user" => "agent_" . rand(1, 20),
        "status_name" => $statuses[array_rand($statuses)]
    ];

    $ch = curl_init($apiUrl);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "X-CATI-KEY: $apiKey"
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        echo "cURL Error: " . curl_error($ch) . PHP_EOL;
    } else {
        echo "[$i] HTTP $httpCode - $response" . PHP_EOL;
    }

    curl_close($ch);

    // Small delay
    usleep(100000);
}

echo "Done inserting fake data." . PHP_EOL;