<?php
header('Content-Type: application/json');

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$db = new mysqli('localhost', 'root', '', 'courier');

if ($db->connect_error) {
    error_log("Database connection failed: " . $db->connect_error);
    die(json_encode([
        'success' => false,
        'error' => 'Σφάλμα σύνδεσης με τη βάση δεδομένων: ' . $db->connect_error
    ]));
}

// Get tracking ID from request
$tracking_id = isset($_GET['tracking_id']) ? $_GET['tracking_id'] : '';
error_log("Received tracking ID: " . $tracking_id);

if (empty($tracking_id)) {
    die(json_encode([
        'success' => false,
        'error' => 'Παρακαλώ εισάγετε αριθμό αποστολής'
    ]));
}

// Check if table exists
$table_check = $db->query("SHOW TABLES LIKE 'deliveries'");
error_log("Table exists check: " . ($table_check->num_rows > 0 ? 'Yes' : 'No'));

if ($table_check->num_rows === 0) {
    die(json_encode([
        'success' => false,
        'error' => 'Ο πίνακας deliveries δεν υπάρχει στη βάση δεδομένων'
    ]));
}

// Check total records
$count_check = $db->query("SELECT COUNT(*) as count FROM deliveries");
$total_records = $count_check->fetch_assoc()['count'];
error_log("Total records in deliveries table: " . $total_records);

// Prepare and execute query
$stmt = $db->prepare("
    SELECT 
        d.tracking_id,
        d.sender_name,
        d.recipient_name,
        d.service_type,
        d.status,
        d.created_at
    FROM deliveries d
    WHERE d.tracking_id = ?
");

if (!$stmt) {
    error_log("Prepare statement failed: " . $db->error);
    die(json_encode([
        'success' => false,
        'error' => 'Σφάλμα προετοιμασίας ερωτήματος: ' . $db->error
    ]));
}

$stmt->bind_param('s', $tracking_id);
$stmt->execute();
$result = $stmt->get_result();

error_log("Number of results found: " . $result->num_rows);

if ($result->num_rows === 0) {
    die(json_encode([
        'success' => false,
        'error' => 'Δεν βρέθηκε αποστολή με αυτόν τον αριθμό'
    ]));
}

$delivery = $result->fetch_assoc();
error_log("Found delivery: " . print_r($delivery, true));

// Return delivery data
echo json_encode([
    'success' => true,
    'delivery' => $delivery
]);

$stmt->close();
$db->close();
?> 