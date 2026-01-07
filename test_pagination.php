<?php
// Mock Session
session_start();
$_SESSION['user_id'] = 1; // Assuming user 1 exists, otherwise test fails but structure checks OK

ob_start();
require __DIR__ . '/backend/get_user_messages.php';
$output = ob_get_clean();

$json = json_decode($output, true);

if (isset($json['data']) && isset($json['pagination'])) {
    echo "SUCCESS: Response structure is correct.\n";
    echo "Current Page: " . $json['pagination']['current_page'] . "\n";
    echo "Total Items: " . $json['pagination']['total_items'] . "\n";
} else {
    echo "FAILURE: Response structure incorrect.\n";
    print_r($json);
}
