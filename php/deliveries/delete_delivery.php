<?php
header('Content-Type: application/json');
require_once '../config/database.php';

// Check if it's a DELETE request
if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get delivery ID from URL parameter
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid delivery ID']);
    exit;
}

try {
    // Prepare and execute delete query
    $stmt = $conn->prepare("DELETE FROM deliveries WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Delivery deleted successfully']);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Delivery not found']);
        }
    } else {
        throw new Exception("Error executing delete query");
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error deleting delivery: ' . $e->getMessage()]);
}

$stmt->close();
$conn->close();
?> 