<?php
header('Content-Type: application/json');
error_reporting(0);
require_once '../config/database.php';

try {
    // Check if users table exists
    $result = $conn->query("SHOW TABLES LIKE 'users'");
    if ($result->num_rows == 0) {
        throw new Exception("Users table does not exist");
    }

    // Get user count
    $result = $conn->query("SELECT COUNT(*) as count FROM users");
    $count = $result->fetch_assoc()['count'];

    // Get sample user data
    $result = $conn->query("SELECT id, username, email, role, status FROM users LIMIT 5");
    $users = [];
    while ($user = $result->fetch_assoc()) {
        $users[] = $user;
    }

    echo json_encode([
        'success' => true,
        'message' => 'Users table check completed',
        'data' => [
            'total_users' => $count,
            'sample_users' => $users
        ]
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$conn->close();
?> 