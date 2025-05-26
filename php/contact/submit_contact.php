<?php
header('Content-Type: application/json');

// Database connection
$db = new mysqli('localhost', 'root', '', 'courier');

if ($db->connect_error) {
    die(json_encode([
        'success' => false,
        'error' => 'Database connection failed'
    ]));
}

// Get POST data
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$message = $_POST['message'] ?? '';

// Validate input
if (empty($name) || empty($email) || empty($message)) {
    die(json_encode([
        'success' => false,
        'error' => 'All fields are required'
    ]));
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die(json_encode([
        'success' => false,
        'error' => 'Invalid email format'
    ]));
}

// Prepare and execute the query
$stmt = $db->prepare("INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $name, $email, $message);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Το μήνυμά σας στάλθηκε επιτυχώς!'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Σφάλμα κατά την αποθήκευση του μηνύματος'
    ]);
}

$stmt->close();
$db->close(); 