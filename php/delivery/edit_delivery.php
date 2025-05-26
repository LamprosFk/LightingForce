<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Database connection parameters
$host = 'localhost';
$db   = 'courier';
$user = 'root';
$pass = '';

try {
    // Create connection
    $conn = new mysqli($host, $user, $pass, $db);
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Get record ID
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $stmt = $conn->prepare("UPDATE deliveries SET 
            sender_name = ?, sender_address = ?, sender_zip = ?, sender_city = ?, sender_phone = ?,
            recipient_name = ?, recipient_address = ?, recipient_zip = ?, recipient_city = ?, recipient_email = ?,
            service_type = ?, cod = ?, insurance = ?, sms = ?
            WHERE id = ?");
            
        $stmt->bind_param("sssssssssssiiii",
            $_POST['sender_name'],
            $_POST['sender_address'],
            $_POST['sender_zip'],
            $_POST['sender_city'],
            $_POST['sender_phone'],
            $_POST['recipient_name'],
            $_POST['recipient_address'],
            $_POST['recipient_zip'],
            $_POST['recipient_city'],
            $_POST['recipient_email'],
            $_POST['service_type'],
            isset($_POST['cod']) ? 1 : 0,
            isset($_POST['insurance']) ? 1 : 0,
            isset($_POST['sms']) ? 1 : 0,
            $id
        );
        
        if ($stmt->execute()) {
            echo "<script>alert('Record updated successfully!'); window.location.href='view_deliveries.php';</script>";
            exit;
        } else {
            echo "<script>alert('Error updating record!');</script>";
        }
        $stmt->close();
    }
    
    // Fetch record
    $stmt = $conn->prepare("SELECT * FROM deliveries WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $record = $result->fetch_assoc();
    
    if (!$record) {
        die("Record not found!");
    }
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Edit Delivery Record</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            .container { max-width: 800px; margin: 0 auto; }
            h2 { color: #333; }
            .form-group { margin-bottom: 15px; }
            label { display: block; margin-bottom: 5px; }
            input[type="text"], input[type="email"] {
                width: 100%;
                padding: 8px;
                border: 1px solid #ddd;
                border-radius: 4px;
            }
            .checkbox-group { margin: 10px 0; }
            .btn {
                padding: 10px 20px;
                border: none;
                border-radius: 4px;
                cursor: pointer;
                color: white;
                margin-right: 10px;
            }
            .save-btn { background-color: #4CAF50; }
            .cancel-btn { background-color: #f44336; }
            .btn:hover { opacity: 0.8; }
        </style>
    </head>
    <body>
        <div class="container">
            <h2>Edit Delivery Record</h2>
            
            <form method="POST">
                <div class="form-group">
                    <h3>Sender Information</h3>
                    <label>Name:</label>
                    <input type="text" name="sender_name" value="<?php echo htmlspecialchars($record['sender_name']); ?>" required>
                    
                    <label>Address:</label>
                    <input type="text" name="sender_address" value="<?php echo htmlspecialchars($record['sender_address']); ?>" required>
                    
                    <label>ZIP Code:</label>
                    <input type="text" name="sender_zip" value="<?php echo htmlspecialchars($record['sender_zip']); ?>" required>
                    
                    <label>City:</label>
                    <input type="text" name="sender_city" value="<?php echo htmlspecialchars($record['sender_city']); ?>" required>
                    
                    <label>Phone:</label>
                    <input type="text" name="sender_phone" value="<?php echo htmlspecialchars($record['sender_phone']); ?>" required>
                </div>

                <div class="form-group">
                    <h3>Recipient Information</h3>
                    <label>Name:</label>
                    <input type="text" name="recipient_name" value="<?php echo htmlspecialchars($record['recipient_name']); ?>" required>
                    
                    <label>Address:</label>
                    <input type="text" name="recipient_address" value="<?php echo htmlspecialchars($record['recipient_address']); ?>" required>
                    
                    <label>ZIP Code:</label>
                    <input type="text" name="recipient_zip" value="<?php echo htmlspecialchars($record['recipient_zip']); ?>" required>
                    
                    <label>City:</label>
                    <input type="text" name="recipient_city" value="<?php echo htmlspecialchars($record['recipient_city']); ?>" required>
                    
                    <label>Email:</label>
                    <input type="email" name="recipient_email" value="<?php echo htmlspecialchars($record['recipient_email']); ?>" required>
                </div>

                <div class="form-group">
                    <h3>Service Options</h3>
                    <label>Service Type:</label>
                    <input type="text" name="service_type" value="<?php echo htmlspecialchars($record['service_type']); ?>" required>
                    
                    <div class="checkbox-group">
                        <label>
                            <input type="checkbox" name="cod" <?php echo $record['cod'] ? 'checked' : ''; ?>>
                            Cash on Delivery
                        </label>
                    </div>
                    
                    <div class="checkbox-group">
                        <label>
                            <input type="checkbox" name="insurance" <?php echo $record['insurance'] ? 'checked' : ''; ?>>
                            Insurance
                        </label>
                    </div>
                    
                    <div class="checkbox-group">
                        <label>
                            <input type="checkbox" name="sms" <?php echo $record['sms'] ? 'checked' : ''; ?>>
                            SMS Notification
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn save-btn">Save Changes</button>
                    <a href="view_deliveries.php" class="btn cancel-btn">Cancel</a>
                </div>
            </form>
        </div>
    </body>
    </html>
    <?php
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?> 