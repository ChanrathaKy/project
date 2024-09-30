<?php
session_start();
require_once '../database/database.php'; // Include database connection

// Check if the user is logged in as admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php"); // Redirect to login if not an admin
    exit();
}

// Fetch completed deliveries from the database
$completedDeliveriesStmt = $pdo->prepare("
    SELECT cd.*, u.name AS driver_name, o.status, p.name AS product_name, o.contact 
    FROM completed_deliveries cd 
    JOIN users u ON cd.driver_id = u.id 
    JOIN orders o ON cd.order_id = o.id 
    JOIN products p ON o.product_id = p.id
");
$completedDeliveriesStmt->execute();
$completedDeliveries = $completedDeliveriesStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Page Title</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="path/to/your/custom.css"> <!-- Custom CSS -->

</head>
<body>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <!-- Brand/Logo -->
        <a class="navbar-brand" href="#">
            Rindra Delivery Service
        </a>
        <!-- Toggler button for mobile view -->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <!-- Navigation links -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="manage_users.php">Manage Users</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="manage_driver.php">Manage Drivers</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="manage_orders.php">Manage Orders</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="assign_driver.php">Assign Drivers</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>


    <div class="container">
        <h2 class="text-center">Manage Completed Deliveries</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Contact</th>
                    <th>Status</th>
                    <th>Product Name</th>
                    <th>Driver Name</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($completedDeliveries)): ?>
                    <?php foreach ($completedDeliveries as $delivery): ?>
                    <tr>
                        <td><?= htmlspecialchars($delivery['order_id'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($delivery['contact'] ?? 'N/A') ?></td> <!-- Use null coalescing operator -->
                        <td><?= htmlspecialchars($delivery['status'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($delivery['product_name'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($delivery['driver_name'] ?? 'N/A') ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">No completed deliveries available.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
