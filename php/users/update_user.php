<?php
header('Content-Type: application/json');
require_once '../config/database.php';

// Get PUT data
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($_GET['id']) || !isset($data['username']) || !isset($data['email'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

$id = intval($_GET['id']);
$username = $data['username'];
$email = $data['email'];
$role = $data['role'] ?? 'user';
$status = $data['status'] ?? 'active';

try {
    // Check if username or email already exists for other users
    $stmt = $conn->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?");
    $stmt->bind_param("ssi", $username, $email, $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => 'Username or email already exists']);
        exit;
    }
    
    // Update user
    if (!empty($data['password'])) {
        // Update with new password
        $password = password_hash($data['password'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, password = ?, role = ?, status = ? WHERE id = ?");
        $stmt->bind_param("sssssi", $username, $email, $password, $role, $status, $id);
    } else {
        // Update without changing password
        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, role = ?, status = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $username, $email, $role, $status, $id);
    }
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'User updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update user']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

$stmt->close();
$conn->close();
?> 