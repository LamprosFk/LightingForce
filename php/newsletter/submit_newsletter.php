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

// Get email
$email = $_POST['email'] ?? '';

// Validate email
if (empty($email)) {
    die(json_encode([
        'success' => false,
        'error' => 'Το email είναι υποχρεωτικό'
    ]));
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die(json_encode([
        'success' => false,
        'error' => 'Μη έγκυρη διεύθυνση email'
    ]));
}

// Check if email already exists
$stmt = $db->prepare("SELECT id FROM newsletter_subscribers WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    die(json_encode([
        'success' => false,
        'error' => 'Αυτό το email είναι ήδη εγγεγραμμένο'
    ]));
}

// Insert new subscriber
$stmt = $db->prepare("INSERT INTO newsletter_subscribers (email) VALUES (?)");
$stmt->bind_param("s", $email);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Εγγραφήκατε επιτυχώς στο newsletter μας!'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'error' => 'Σφάλμα κατά την εγγραφή'
    ]);
}

$stmt->close();
$db->close(); 