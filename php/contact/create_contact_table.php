<?php
// Database connection
$db = new mysqli('localhost', 'root', '', 'courier');

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// SQL to create table
$sql = "CREATE TABLE contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('new', 'read', 'replied') DEFAULT 'new'
)";

// Execute query
if ($db->query($sql) === TRUE) {
    echo "Table contact_messages created successfully";
} else {
    echo "Error creating table: " . $db->error;
}

$db->close();
?> 