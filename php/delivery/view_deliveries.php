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
    
    // Handle delete action
    if (isset($_POST['delete_id'])) {
        $delete_id = (int)$_POST['delete_id'];
        $stmt = $conn->prepare("DELETE FROM deliveries WHERE id = ?");
        $stmt->bind_param("i", $delete_id);
        if ($stmt->execute()) {
            echo "<script>alert('Record deleted successfully!');</script>";
        } else {
            echo "<script>alert('Error deleting record!');</script>";
        }
        $stmt->close();
    }
    
    // Get search term if any
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    $where = '';
    if (!empty($search)) {
        $search = $conn->real_escape_string($search);
        $where = "WHERE sender_name LIKE '%$search%' 
                 OR recipient_name LIKE '%$search%' 
                 OR service_type LIKE '%$search%'";
    }
    
    // Fetch records
    $result = $conn->query("SELECT * FROM deliveries $where ORDER BY id DESC");
    
    if ($result) {
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <title>Delivery Records</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .container { max-width: 1200px; margin: 0 auto; }
                h2 { color: #333; }
                .search-box { margin: 20px 0; }
                .search-box input { padding: 8px; width: 300px; }
                .search-box button { padding: 8px 15px; background: #4CAF50; color: white; border: none; cursor: pointer; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { padding: 12px; text-align: left; border: 1px solid #ddd; }
                th { background-color: #4CAF50; color: white; }
                tr:nth-child(even) { background-color: #f2f2f2; }
                tr:hover { background-color: #ddd; }
                .back-link { margin: 20px 0; }
                .back-link a { color: #4CAF50; text-decoration: none; }
                .back-link a:hover { text-decoration: underline; }
                .action-btn {
                    padding: 6px 12px;
                    margin: 0 3px;
                    border: none;
                    border-radius: 4px;
                    cursor: pointer;
                    color: white;
                }
                .edit-btn { background-color: #2196F3; }
                .delete-btn { background-color: #f44336; }
                .action-btn:hover { opacity: 0.8; }
            </style>
            <script>
                function confirmDelete(id) {
                    if (confirm('Are you sure you want to delete this record?')) {
                        document.getElementById('delete_form_' + id).submit();
                    }
                }
            </script>
        </head>
        <body>
            <div class="container">
                <h2>📦 Delivery Records</h2>
                
                <div class="search-box">
                    <form method="GET">
                        <input type="text" name="search" placeholder="Search by sender, recipient, or service type..." 
                               value="<?php echo htmlspecialchars($search); ?>">
                        <button type="submit">Search</button>
                    </form>
                </div>

                <div class="back-link">
                    <a href="index.html">← Back to Home</a>
                </div>

                <table>
                    <tr>
                        <th>ID</th>
                        <th>Sender</th>
                        <th>Recipient</th>
                        <th>Service Type</th>
                        <th>COD</th>
                        <th>Insurance</th>
                        <th>SMS</th>
                        <th>Actions</th>
                    </tr>
                    <?php
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['sender_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['recipient_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['service_type']) . "</td>";
                        echo "<td>" . ($row['cod'] ? 'Yes' : 'No') . "</td>";
                        echo "<td>" . ($row['insurance'] ? 'Yes' : 'No') . "</td>";
                        echo "<td>" . ($row['sms'] ? 'Yes' : 'No') . "</td>";
                        echo "<td>";
                        echo "<a href='edit_delivery.php?id=" . $row['id'] . "' class='action-btn edit-btn'>Edit</a>";
                        echo "<form id='delete_form_" . $row['id'] . "' method='POST' style='display: inline;'>";
                        echo "<input type='hidden' name='delete_id' value='" . $row['id'] . "'>";
                        echo "<button type='button' onclick='confirmDelete(" . $row['id'] . ")' class='action-btn delete-btn'>Delete</button>";
                        echo "</form>";
                        echo "</td>";
                        echo "</tr>";
                    }
                    ?>
                </table>
            </div>
        </body>
        </html>
        <?php
    } else {
        echo "❌ Error fetching records: " . $conn->error;
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?> 