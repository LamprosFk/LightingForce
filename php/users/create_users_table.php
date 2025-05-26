<?php
require_once '../config/database.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create users table
    $sql = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM('user', 'manager', 'admin') DEFAULT 'user',
        status ENUM('active', 'inactive') DEFAULT 'active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )";

    $pdo->exec($sql);
    echo "Users table created successfully";

    // Insert default admin user if not exists
    $checkAdmin = $pdo->query("SELECT id FROM users WHERE username = 'admin'")->fetch();
    if (!$checkAdmin) {
        $defaultPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (username, password, email, role) VALUES ('admin', :password, 'admin@lightingforcecourier.gr', 'admin')";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['password' => $defaultPassword]);
        echo "\nDefault admin user created";
    }

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 