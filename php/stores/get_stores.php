<?php
header('Content-Type: application/json');

// Database connection
$db = new mysqli('localhost', 'root', '', 'courier');

if ($db->connect_error) {
    die(json_encode(['error' => 'Connection failed: ' . $db->connect_error]));
}

// Fetch all stores
$sql = "SELECT id, name, address, postal_code, hours, latitude, longitude FROM stores";
$result = $db->query($sql);

$stores = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $stores[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'address' => $row['address'],
            'postal_code' => $row['postal_code'],
            'hours' => $row['hours'],
            'latitude' => $row['latitude'],
            'longitude' => $row['longitude']
        ];
    }
}

echo json_encode($stores);
$db->close(); 