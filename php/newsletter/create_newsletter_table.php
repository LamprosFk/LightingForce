<?php
// Database connection
$db = new mysqli('localhost', 'root', '', 'courier');

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Create table
$sql = "CREATE TABLE IF NOT EXISTS newsletter_subscribers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('active', 'unsubscribed') DEFAULT 'active'
)";

if ($db->query($sql) === TRUE) {
    echo "Newsletter subscribers table created successfully";
} else {
    echo "Error creating table: " . $db->error;
}

$db->close(); 