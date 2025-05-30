<?php
// Database connection
$db = new mysqli('localhost', 'root', '', 'courier');

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Prepare the insert statement
$stmt = $db->prepare("INSERT INTO stores (name, address, city, prefecture, postal_code, hours, latitude, longitude) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

// Store data array
$stores = [
    ['ΛΥΚΟΒΡΥΣΗ', 'ΛΕΩΦ. ΣΟΦΟΚΛΗ ΒΕΝΙΖΕΛΟΥ 14', 'ΛΥΚΟΒΡΥΣΗ', 'ΑΤΤΙΚΗΣ', '14123', 'ΚΑΘΗΜΕΡΙΝΕΣ: 09.00-19.00 ΣΑΒΒΑΤΟ: 10.00-14.00', 38.015323, 23.687415],
    ['ΜΕΓΑΡΑ', '28ης ΟΚΤΩΒΡΙΟΥ 18', 'ΜΕΓΑΡΑ', 'ΑΤΤΙΚΗΣ', '19100', 'ΚΑΘΗΜΕΡΙΝΕΣ: 08.30-17.00 ΣΑΒΒΑΤΟ: 09.00-14.00', 37.995723, 23.347639],
    ['ΕΛΕΥΣΙΝΑ', '25ο ΧΛΜ ΝΕΟΑΚ (ΔΙΠΛΑ ΑΠΟ ΔΡΑΚΟΥΛΑΚΗ)', 'ΑΝΩ ΕΛΕΥΣΙΝΑ', 'ΑΤΤΙΚΗΣ', '19200', 'ΚΑΘΗΜΕΡΙΝΕΣ: 09.00-17.00 ΣΑΒΒΑΤΟ: 09.00-14.00', 38.055595, 23.512802],
    ['ΑΓΙΟΣ ΣΤΕΦΑΝΟΣ', 'ΛΕΩΦ. ΑΘΗΝΩΝ 1', 'ΑΓΙΟΣ ΣΤΕΦΑΝΟΣ', 'ΑΤΤΙΚΗΣ', '14565', 'ΚΑΘΗΜΕΡΙΝΕΣ: 09.00-17.00 ΣΑΒΒΑΤΟ: 09.00-14.00', 38.139523, 23.857415],
    ['ΑΓΙΟΣ ΔΗΜΗΤΡΙΟΣ', 'ΛΕΩΦ. ΑΘΗΝΩΝ 2', 'ΑΓΙΟΣ ΔΗΜΗΤΡΙΟΣ', 'ΑΤΤΙΚΗΣ', '17343', 'ΚΑΘΗΜΕΡΙΝΕΣ: 09.00-17.00 ΣΑΒΒΑΤΟ: 09.00-14.00', 37.932523, 23.737415],
    ['ΑΓΙΑ ΠΑΡΑΣΚΕΥΗ', 'ΛΕΩΦ. ΑΘΗΝΩΝ 3', 'ΑΓΙΑ ΠΑΡΑΣΚΕΥΗ', 'ΑΤΤΙΚΗΣ', '15341', 'ΚΑΘΗΜΕΡΙΝΕΣ: 09.00-17.00 ΣΑΒΒΑΤΟ: 09.00-14.00', 38.015323, 23.807415],
    ['ΑΓΙΑ ΒΑΡΒΑΡΑ', 'ΛΕΩΦ. ΑΘΗΝΩΝ 4', 'ΑΓΙΑ ΒΑΡΒΑΡΑ', 'ΑΤΤΙΚΗΣ', '12351', 'ΚΑΘΗΜΕΡΙΝΕΣ: 09.00-17.00 ΣΑΒΒΑΤΟ: 09.00-14.00', 37.985323, 23.657415],
    ['ΑΓΙΑ ΜΑΡΙΝΑ', 'ΛΕΩΦ. ΑΘΗΝΩΝ 5', 'ΑΓΙΑ ΜΑΡΙΝΑ', 'ΑΤΤΙΚΗΣ', '19014', 'ΚΑΘΗΜΕΡΙΝΕΣ: 09.00-17.00 ΣΑΒΒΑΤΟ: 09.00-14.00', 38.135323, 23.857415],
    ['ΑΓΙΑ ΣΟΦΙΑ', 'ΛΕΩΦ. ΑΘΗΝΩΝ 6', 'ΑΓΙΑ ΣΟΦΙΑ', 'ΑΤΤΙΚΗΣ', '15124', 'ΚΑΘΗΜΕΡΙΝΕΣ: 09.00-17.00 ΣΑΒΒΑΤΟ: 09.00-14.00', 38.025323, 23.757415],
    ['ΑΓΙΑ ΤΡΙΑΔΑ', 'ΛΕΩΦ. ΑΘΗΝΩΝ 7', 'ΑΓΙΑ ΤΡΙΑΔΑ', 'ΑΤΤΙΚΗΣ', '10443', 'ΚΑΘΗΜΕΡΙΝΕΣ: 09.00-17.00 ΣΑΒΒΑΤΟ: 09.00-14.00', 37.975323, 23.727415],
    ['ΑΓΙΑ ΕΙΡΗΝΗ', 'ΛΕΩΦ. ΑΘΗΝΩΝ 8', 'ΑΓΙΑ ΕΙΡΗΝΗ', 'ΑΤΤΙΚΗΣ', '10560', 'ΚΑΘΗΜΕΡΙΝΕΣ: 09.00-17.00 ΣΑΒΒΑΤΟ: 09.00-14.00', 37.965323, 23.717415],
    ['ΑΓΙΑ ΑΝΑΣΤΑΣΙΑ', 'ΛΕΩΦ. ΑΘΗΝΩΝ 9', 'ΑΓΙΑ ΑΝΑΣΤΑΣΙΑ', 'ΑΤΤΙΚΗΣ', '10431', 'ΚΑΘΗΜΕΡΙΝΕΣ: 09.00-17.00 ΣΑΒΒΑΤΟ: 09.00-14.00', 37.955323, 23.707415],
    ['ΑΓΙΑ ΕΥΘΥΜΙΑ', 'ΛΕΩΦ. ΑΘΗΝΩΝ 10', 'ΑΓΙΑ ΕΥΘΥΜΙΑ', 'ΑΤΤΙΚΗΣ', '10432', 'ΚΑΘΗΜΕΡΙΝΕΣ: 09.00-17.00 ΣΑΒΒΑΤΟ: 09.00-14.00', 37.945323, 23.697415],
    ['ΑΓΙΑ ΚΥΡΙΑΚΗ', 'ΛΕΩΦ. ΑΘΗΝΩΝ 11', 'ΑΓΙΑ ΚΥΡΙΑΚΗ', 'ΑΤΤΙΚΗΣ', '10433', 'ΚΑΘΗΜΕΡΙΝΕΣ: 09.00-17.00 ΣΑΒΒΑΤΟ: 09.00-14.00', 37.935323, 23.687415],
    ['ΑΓΙΑ ΠΕΛΑΓΙΑ', 'ΛΕΩΦ. ΑΘΗΝΩΝ 12', 'ΑΓΙΑ ΠΕΛΑΓΙΑ', 'ΑΤΤΙΚΗΣ', '10434', 'ΚΑΘΗΜΕΡΙΝΕΣ: 09.00-17.00 ΣΑΒΒΑΤΟ: 09.00-14.00', 37.925323, 23.677415],
    ['ΑΓΙΑ ΦΩΤΕΙΝΗ', 'ΛΕΩΦ. ΑΘΗΝΩΝ 13', 'ΑΓΙΑ ΦΩΤΕΙΝΗ', 'ΑΤΤΙΚΗΣ', '10435', 'ΚΑΘΗΜΕΡΙΝΕΣ: 09.00-17.00 ΣΑΒΒΑΤΟ: 09.00-14.00', 37.915323, 23.667415],
    ['ΑΓΙΑ ΖΩΗ', 'ΛΕΩΦ. ΑΘΗΝΩΝ 14', 'ΑΓΙΑ ΖΩΗ', 'ΑΤΤΙΚΗΣ', '10436', 'ΚΑΘΗΜΕΡΙΝΕΣ: 09.00-17.00 ΣΑΒΒΑΤΟ: 09.00-14.00', 37.905323, 23.657415],
    ['ΑΓΙΑ ΣΚΕΠΗ', 'ΛΕΩΦ. ΑΘΗΝΩΝ 15', 'ΑΓΙΑ ΣΚΕΠΗ', 'ΑΤΤΙΚΗΣ', '10437', 'ΚΑΘΗΜΕΡΙΝΕΣ: 09.00-17.00 ΣΑΒΒΑΤΟ: 09.00-14.00', 37.895323, 23.647415],
    ['ΑΓΙΑ ΤΡΟΦΗ', 'ΛΕΩΦ. ΑΘΗΝΩΝ 16', 'ΑΓΙΑ ΤΡΟΦΗ', 'ΑΤΤΙΚΗΣ', '10438', 'ΚΑΘΗΜΕΡΙΝΕΣ: 09.00-17.00 ΣΑΒΒΑΤΟ: 09.00-14.00', 37.885323, 23.637415],
    ['ΑΓΙΑ ΠΑΡΑΣΚΕΥΗ', 'ΛΕΩΦ. ΑΘΗΝΩΝ 17', 'ΑΓΙΑ ΠΑΡΑΣΚΕΥΗ', 'ΑΤΤΙΚΗΣ', '10439', 'ΚΑΘΗΜΕΡΙΝΕΣ: 09.00-17.00 ΣΑΒΒΑΤΟ: 09.00-14.00', 37.875323, 23.627415],
    ['ΑΓΙΑ ΒΑΡΒΑΡΑ', 'ΛΕΩΦ. ΑΘΗΝΩΝ 18', 'ΑΓΙΑ ΒΑΡΒΑΡΑ', 'ΑΤΤΙΚΗΣ', '10440', 'ΚΑΘΗΜΕΡΙΝΕΣ: 09.00-17.00 ΣΑΒΒΑΤΟ: 09.00-14.00', 37.865323, 23.617415]
];

$successCount = 0;
$errorCount = 0;

foreach ($stores as $store) {
    $stmt->bind_param("ssssssdd", 
        $store[0], // name
        $store[1], // address
        $store[2], // city
        $store[3], // prefecture
        $store[4], // postal_code
        $store[5], // hours
        $store[6], // latitude
        $store[7]  // longitude
    );
    
    if ($stmt->execute()) {
        $successCount++;
    } else {
        $errorCount++;
    }
}

echo "Import completed:<br>";
echo "Successfully imported: $successCount stores<br>";
echo "Failed to import: $errorCount stores";

$stmt->close();
$db->close(); 