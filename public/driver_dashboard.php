<?php
session_start();
require_once '../database/database.php'; // Include database connection

// Check if the user is logged in as a driver
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'driver') {
    header("Location: login.php"); // Redirect to login if not a driver
    exit();
}

// Handle the delivery confirmation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_delivered'])) {
    $orderId = $_POST['order_id'];
    $driverId = $_SESSION['user']['id']; // Get the logged-in driver's ID

    // Debugging: Output driver ID
    var_dump($driverId); // Check what driver ID is being used

    try {
        // Insert into completed deliveries
        $insertStmt = $pdo->prepare("INSERT INTO completed_deliveries (order_id, driver_id) VALUES (:order_id, :driver_id)");
        $insertStmt->execute(['order_id' => $orderId, 'driver_id' => $driverId]);

        // Update order status to delivered
        $updateStmt = $pdo->prepare("UPDATE orders SET status = 'delivered' WHERE id = :order_id");
        $updateStmt->execute(['order_id' => $orderId]);

        // Redirect back to the driver dashboard with a success message
        header("Location: driver_dashboard.php?success=1");
        exit();
    } catch (PDOException $e) {
        // Handle the exception
        echo "Error: " . $e->getMessage();
    }
}

// Fetch deliveries assigned to the logged-in driver
$driverId = $_SESSION['user']['id'];
$deliveriesStmt = $pdo->prepare("
    SELECT o.id AS order_id, p.name AS product_name, o.status, o.contact 
    FROM orders o 
    JOIN products p ON o.product_id = p.id 
    WHERE o.driver_id = :driver_id AND o.status = 'assigned'
");
$deliveriesStmt->execute(['driver_id' => $driverId]);
$assignedDeliveries = $deliveriesStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h2 class="text-center">Assigned Deliveries</h2>

        <!-- Success/Error Messages -->
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">Delivery marked as completed!</div>
        <?php endif; ?>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Product Name</th>
                    <th>Status</th>
                    <th>Contact</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($assignedDeliveries) > 0): ?>
                    <?php foreach ($assignedDeliveries as $delivery): ?>
                    <tr>
                        <td><?= htmlspecialchars($delivery['order_id']) ?></td>
                        <td><?= htmlspecialchars($delivery['product_name']) ?></td>
                        <td><?= htmlspecialchars($delivery['status']) ?></td>
                        <td><?= htmlspecialchars($delivery['contact']) ?></td>
                        <td>
                            <form method="POST" action="mark_delivered.php">
                                <input type="hidden" name="order_id" value="<?= $delivery['order_id'] ?>">
                                <button type="submit" name="mark_delivered" class="btn btn-success">Mark as Delivered</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">No deliveries assigned.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
