<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Get phone number from query parameter
    $phone = isset($_GET['phone']) ? preg_replace('/[^0-9]/', '', $_GET['phone']) : '';
    
    if (empty($phone)) {
        http_response_code(400);
        echo json_encode(['error' => 'Phone number required']);
        exit;
    }
    
    // File path for user's orders
    $filePath = __DIR__ . '/data/' . $phone . '_orders.json';
    
    // Read orders
    if (file_exists($filePath)) {
        $orders = json_decode(file_get_contents($filePath), true);
        if (is_array($orders)) {
            echo json_encode(['orders' => $orders]);
        } else {
            echo json_encode(['orders' => []]);
        }
    } else {
        echo json_encode(['orders' => []]);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>
