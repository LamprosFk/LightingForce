<?php
// Enable error reporting for debugging
ini_set('display_errors', 0); // Disable display of errors
error_reporting(E_ALL);

// Set headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Log the request
error_log("Request received: " . $_SERVER['REQUEST_METHOD']);
error_log("POST data: " . print_r($_POST, true));

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'error' => 'Method not allowed. Only POST requests are accepted.'
    ]);
    exit();
}

// Database connection
$host = 'localhost';
$db   = 'courier';
$user = 'root';
$pass = '';

try {
    $conn = new mysqli($host, $user, $pass, $db);

    if ($conn->connect_error) {
        throw new Exception('Database connection failed: ' . $conn->connect_error);
    }

    // Function to generate unique tracking ID
    function generateTrackingId($conn) {
        do {
            $random = str_pad(mt_rand(0, 999), 3, '0', STR_PAD_LEFT);
            $tracking_id = "013" . $random;
            
            $stmt = $conn->prepare("SELECT COUNT(*) as count FROM deliveries WHERE tracking_id = ?");
            if (!$stmt) {
                throw new Exception('Failed to prepare tracking ID check statement: ' . $conn->error);
            }
            
            $stmt->bind_param("s", $tracking_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->close();
            
        } while ($row['count'] > 0);
        
        return $tracking_id;
    }

    // Get form data
    $sender = $_POST['sender'] ?? '';
    $recipient = $_POST['recipient'] ?? '';
    $serviceType = $_POST['service-type'] ?? '';

    // Log the processed data
    error_log("Processed data: " . print_r([
        'sender' => $sender,
        'recipient' => $recipient,
        'serviceType' => $serviceType
    ], true));

    // Validate required fields
    if (empty($sender) || empty($recipient) || empty($serviceType)) {
        throw new Exception('Required fields are missing');
    }

    // Generate tracking ID
    $tracking_id = generateTrackingId($conn);

    // Insert into database with only the fields that exist
    $stmt = $conn->prepare("INSERT INTO deliveries (
        tracking_id, sender_name, recipient_name, service_type, status
    ) VALUES (?, ?, ?, ?, 'pending')");

    if (!$stmt) {
        throw new Exception('Failed to prepare statement: ' . $conn->error);
    }

    $stmt->bind_param("ssss",
        $tracking_id,
        $sender,
        $recipient,
        $serviceType
    );

    if (!$stmt->execute()) {
        throw new Exception('Failed to execute statement: ' . $stmt->error);
    }

    // Success response
    echo json_encode([
        'success' => true,
        'tracking_id' => $tracking_id,
        'message' => 'Η αποστολή καταχωρήθηκε επιτυχώς!'
    ]);

    $stmt->close();
} catch (Exception $e) {
    error_log("Error in submit.php: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Σφάλμα: ' . $e->getMessage()
    ]);
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>
