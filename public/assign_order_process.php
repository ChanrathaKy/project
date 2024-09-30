<?php
session_start();
require 'database.php'; // Include your database connection file

// Check if admin is logged in
if ($_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Check if POST data is set
if (isset($_POST['order_id']) && isset($_POST['driver_id'])) {
    $order_id = $_POST['order_id'];
    $driver_id = $_POST['driver_id'];

    // Update the order with the selected driver
    $sql = "UPDATE orders SET driver_id = ?, status = 'assigned' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $driver_id, $order_id);

    if ($stmt->execute()) {
        // Redirect to the assignment page with a success message
        header('Location: assign_order.php?message=Order assigned successfully.');
    } else {
        // Redirect to the assignment page with an error message
        header('Location: assign_order.php?message=Error assigning order.');
    }

    $stmt->close();
} else {
    header('Location: assign_order.php');
}
$conn->close();
?>
