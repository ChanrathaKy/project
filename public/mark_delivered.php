<?php
session_start();
require_once '../database/database.php'; // Include database connection

// Check if the user is logged in as a driver
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'driver') {
    header("Location: login.php"); // Redirect to login if not a driver
    exit();
}

// Handle mark as delivered
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_delivered'])) {
    $orderId = $_POST['order_id'];
    $driverId = $_SESSION['user']['id']; // Get the current driver's user ID

    // Check if the driver exists in the users table
    $driverCheckStmt = $pdo->prepare("SELECT id FROM users WHERE id = :driver_id AND role = 'driver'");
    $driverCheckStmt->execute(['driver_id' => $driverId]);
    
    if ($driverCheckStmt->rowCount() > 0) {
        // Driver exists, proceed to insert into completed_deliveries
        $stmt = $pdo->prepare("INSERT INTO completed_deliveries (order_id, driver_id, delivery_date) VALUES (:order_id, :driver_id, NOW())");
        
        try {
            $stmt->execute(['order_id' => $orderId, 'driver_id' => $driverId]);
            
            // Update order status to completed
            $newStatus = 'completed'; // Make sure this is valid
            $updateOrder = $pdo->prepare("UPDATE orders SET status = :status WHERE id = :order_id");
            $updateOrder->execute(['status' => $newStatus, 'order_id' => $orderId]);

            // Redirect back to the same page with a success message
            header("Location: driver_dashboard.php?success=1");
            exit();
        } catch (PDOException $e) {
            // Handle error, log it if necessary
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "Error: Driver not found or not authorized.";
    }
}
?>

<!-- HTML and form code here -->
