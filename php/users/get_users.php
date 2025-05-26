<?php
header('Content-Type: application/json');
error_reporting(0); // Disable error reporting to prevent HTML errors in JSON

require_once '../config/database.php';

try {
    $stmt = $conn->prepare("SELECT id, username, email, role, status FROM users");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $users = [];
    
    while ($user = $result->fetch_assoc()) {
        $users[] = $user;
    }
    
    echo json_encode($users);
} catch (Exception $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}

$stmt->close();
$conn->close();
?> 