<?php
error_reporting(0);
require_once '../config/database.php';

try {
    $sql = "CREATE TABLE IF NOT EXISTS deliveries (
        id INT AUTO_INCREMENT PRIMARY KEY,
        tracking_id VARCHAR(50) UNIQUE,
        sender_name VARCHAR(100) NOT NULL,
        recipient_name VARCHAR(100) NOT NULL,
        status ENUM('pending', 'in-transit', 'delivered') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(['success' => true, 'message' => 'Deliveries table created successfully']);
    } else {
        throw new Exception("Error creating deliveries table: " . $conn->error);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
?> 