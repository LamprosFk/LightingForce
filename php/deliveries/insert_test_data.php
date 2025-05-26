<?php
header('Content-Type: application/json');
error_reporting(0);
require_once '../config/database.php';

try {
    // Test deliveries data
    $deliveries = [
        [
            'tracking_id' => '013891',
            'sender_name' => 'Γιώργος Παπαδόπουλος',
            'recipient_name' => 'Μαρία Κωνσταντίνου',
            'status' => 'in-transit'
        ],
        [
            'tracking_id' => '013892',
            'sender_name' => 'Νίκος Δημητρίου',
            'recipient_name' => 'Ελένη Παπαδοπούλου',
            'status' => 'pending'
        ],
        [
            'tracking_id' => '013893',
            'sender_name' => 'Αννα Κωνσταντίνου',
            'recipient_name' => 'Δημήτρης Αλεξίου',
            'status' => 'delivered'
        ]
    ];

    $stmt = $conn->prepare("INSERT INTO deliveries (tracking_id, sender_name, recipient_name, status) VALUES (?, ?, ?, ?)");
    
    $success_count = 0;
    foreach ($deliveries as $delivery) {
        $stmt->bind_param("ssss", 
            $delivery['tracking_id'],
            $delivery['sender_name'],
            $delivery['recipient_name'],
            $delivery['status']
        );
        
        if ($stmt->execute()) {
            $success_count++;
        }
    }

    echo json_encode([
        'success' => true,
        'message' => "Successfully inserted $success_count test deliveries"
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

$stmt->close();
$conn->close();
?> 