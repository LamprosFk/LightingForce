<?php
header('Content-Type: application/json');
error_reporting(0); // Disable error reporting to prevent HTML errors in JSON

require_once '../config/database.php';

try {
    $tracking_id = isset($_GET['tracking_id']) ? $_GET['tracking_id'] : null;
    
    if ($tracking_id) {
        $stmt = $conn->prepare("
            SELECT 
                d.id,
                d.tracking_id,
                d.sender_name as sender,
                d.recipient_name as recipient,
                d.status,
                d.created_at
            FROM deliveries d
            WHERE d.tracking_id = ?
            ORDER BY d.created_at DESC
        ");
        $stmt->bind_param("s", $tracking_id);
    } else {
        $stmt = $conn->prepare("
            SELECT 
                d.id,
                d.tracking_id,
                d.sender_name as sender,
                d.recipient_name as recipient,
                d.status,
                d.created_at
            FROM deliveries d
            ORDER BY d.created_at DESC
        ");
    }
    
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $deliveries = [];
    
    while ($delivery = $result->fetch_assoc()) {
        $deliveries[] = $delivery;
    }
    
    echo json_encode($deliveries);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}

$stmt->close();
$conn->close();
?> 