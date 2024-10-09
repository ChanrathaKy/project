<?php
session_start();
require_once '../database/database.php'; // Include database connection

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}

// Fetch the delivery history for the logged-in driver
$driverId = $_SESSION['user']['id'];
$historyStmt = $pdo->prepare("
    SELECT cd.order_id, p.name AS product_name, o.contact, o.address, cd.delivery_date
    FROM completed_deliveries cd
    JOIN orders o ON cd.order_id = o.id
    JOIN products p ON o.product_id = p.id
    WHERE cd.driver_id = :driver_id
");
$historyStmt->execute(['driver_id' => $driverId]);
$deliveryHistory = $historyStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery History</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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
                <a class="nav-link" href="driver_dashboard.php">Assigned Deliveries</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php">Logout</a>
            </li>
        </ul>
    </div>
</nav>

<div class="container">
    <h2 class="text-center">Delivery History</h2>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Product Name</th>
                <th>Contact</th>
                <th>Address</th>
                <th>Delivery Date</th> <!-- Added Delivery Date Column -->
            </tr>
        </thead>
        <tbody>
            <?php if (count($deliveryHistory) > 0): ?>
                <?php foreach ($deliveryHistory as $delivery): ?>
                    <tr>
                        <td><?= htmlspecialchars($delivery['order_id']) ?></td>
                        <td><?= htmlspecialchars($delivery['product_name']) ?></td>
                        <td><?= htmlspecialchars($delivery['contact'] ?? 'Not Provided') ?></td>
                        <td><?= htmlspecialchars($delivery['address'] ?? 'Not Provided') ?></td>
                        <td><?= htmlspecialchars($delivery['delivery_date'] ?? 'Not Provided') ?></td> <!-- Display Delivery Date -->
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center">No delivery history available.</td>
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
