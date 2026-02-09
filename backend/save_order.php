<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input || !isset($input['phoneNumber']) || !isset($input['orderData'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Phone number and order data required']);
        exit;
    }
    
    $phone = preg_replace('/[^0-9]/', '', $input['phoneNumber']);
    $orderData = $input['orderData'];
    
    if (empty($phone)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid phone number']);
        exit;
    }
    
    // Create data directory if it doesn't exist
    $dataDir = __DIR__ . '/data';
    if (!file_exists($dataDir)) {
        mkdir($dataDir, 0777, true);
    }
    
    // File path for user's orders
    $filePath = $dataDir . '/' . $phone . '_orders.json';
    
    // Read existing orders
    $orders = [];
    if (file_exists($filePath)) {
        $orders = json_decode(file_get_contents($filePath), true);
        if (!is_array($orders)) {
            $orders = [];
        }
    }
    
    // Add new order at the beginning
    array_unshift($orders, $orderData);
    
    // Keep only last 50 orders
    if (count($orders) > 50) {
        $orders = array_slice($orders, 0, 50);
    }
    
    // Save orders
    if (file_put_contents($filePath, json_encode($orders, JSON_PRETTY_PRINT))) {
        echo json_encode(['success' => true, 'message' => 'Order saved successfully']);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to save order']);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}
?>
