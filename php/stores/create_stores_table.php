<?php
// Database connection
$db = new mysqli('localhost', 'root', '', 'courier');

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// SQL to create stores table
$sql = "CREATE TABLE IF NOT EXISTS stores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    address VARCHAR(255) NOT NULL,
    city VARCHAR(100) NOT NULL,
    prefecture VARCHAR(100) NOT NULL,
    postal_code VARCHAR(10),
    hours VARCHAR(255),
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($db->query($sql) === TRUE) {
    echo "Table 'stores' created successfully";
} else {
    echo "Error creating table: " . $db->error;
}

$db->close(); 