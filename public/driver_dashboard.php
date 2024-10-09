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
    $deliveryDate = date('Y-m-d H:i:s'); // Get the current date and time

    // Validate the order ID
    if (!empty($orderId) && is_numeric($orderId)) {
        try {
            // Insert into completed deliveries
            $insertStmt = $pdo->prepare("INSERT INTO completed_deliveries (order_id, driver_id, delivery_date) VALUES (:order_id, :driver_id, :delivery_date)");
            $insertStmt->execute(['order_id' => $orderId, 'driver_id' => $driverId, 'delivery_date' => $deliveryDate]);

            // Update order status to completed
            $updateStmt = $pdo->prepare("UPDATE orders SET status = 'completed' WHERE id = :order_id");
            $updateStmt->execute(['order_id' => $orderId]);

            // Redirect back to the driver dashboard with a success message
            header("Location: driver_dashboard.php?success=1");
            exit();
        } catch (PDOException $e) {
            // Handle the exception
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "Invalid order ID.";
    }
}


// Fetch deliveries assigned to the logged-in driver with client information
$driverId = $_SESSION['user']['id'];
$deliveriesStmt = $pdo->prepare("
    SELECT o.id AS order_id, p.name AS product_name, o.status, o.contact, o.address, u.name AS client_name 
    FROM orders o 
    JOIN products p ON o.product_id = p.id 
    JOIN users u ON o.client_id = u.id  -- Adjust this line to match your database structure
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
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar {
            margin-bottom: 20px;
        }
        .table {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .alert {
            margin-top: 20px;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(45deg, #007bff, #6610f2);">
    <a class="navbar-brand" href="#" style="font-weight: bold;">Rindra Delivery Service</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="delivery_history.php">Delivery History</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php">Logout</a>
            </li>
        </ul>
    </div>
</nav>

<div class="container">
    <h2 class="text-center">Assigned Deliveries</h2>

    <!-- Success/Error Messages -->
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success text-center">Delivery marked as completed!</div>
    <?php endif; ?>

    <table class="table table-bordered table-striped">
        <thead class="thead-light">
            <tr>
                <th>Order ID</th>
                <th>Product Name</th>
                <th>Status</th>
                <th>Contact</th>
                <th>Address</th> <!-- New Column for Address -->
                <th>Client Name</th> <!-- New Column for Client Name -->
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
                    <td><?= htmlspecialchars($delivery['address']) ?></td> <!-- Display Address -->
                    <td><?= htmlspecialchars($delivery['client_name']) ?></td> <!-- Display Client Name -->
                    <td>
                        <form method="POST" action="driver_dashboard.php">
                            <input type="hidden" name="order_id" value="<?= $delivery['order_id'] ?>">
                            <button type="submit" name="mark_delivered" class="btn btn-success">Mark as Delivered</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center">No deliveries assigned.</td> <!-- Adjusted colspan -->
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
