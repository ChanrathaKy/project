<?php
session_start();
require_once '../database/database.php'; // Include the database connection

// Check if an order ID is provided
if (!isset($_GET['id'])) {
    header('Location: manage_orders.php'); // Redirect if no order ID is found
    exit;
}

// Fetch the order details from the database
$orderId = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = :id");
$stmt->bindValue(':id', $orderId, PDO::PARAM_INT);
$stmt->execute();
$order = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if the order exists
if (!$order) {
    header('Location: manage_orders.php'); // Redirect if order is not found
    exit;
}

// Handle form submission to update order
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get updated data from the form
    $productName = trim($_POST['product_name']);
    $status = trim($_POST['status']);
    $clientId = (int)$_POST['client_id'];
    $driverId = (int)$_POST['driver_id'];
    $address = trim($_POST['address']);

    // Update order in the database
    $updateStmt = $pdo->prepare("UPDATE orders SET product_name = :product_name, status = :status, 
                                  client_id = :client_id, driver_id = :driver_id, address = :address 
                                  WHERE id = :id");
    $updateStmt->bindValue(':product_name', $productName);
    $updateStmt->bindValue(':status', $status);
    $updateStmt->bindValue(':client_id', $clientId);
    $updateStmt->bindValue(':driver_id', $driverId);
    $updateStmt->bindValue(':address', $address);
    $updateStmt->bindValue(':id', $orderId, PDO::PARAM_INT);

    if ($updateStmt->execute()) {
        header('Location: manage_orders.php?message=Order updated successfully'); // Redirect after update
        exit;
    } else {
        $error = 'Error updating the order. Please try again.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Order</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="#">Rindra Delivery Service</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="manage_users.php">Manage Users</a></li>
                <li class="nav-item"><a class="nav-link" href="manage_driver.php">Manage Drivers</a></li>
                <li class="nav-item"><a class="nav-link" href="manage_orders.php">Manage Orders</a></li>
                <li class="nav-item"><a class="nav-link" href="assign_driver.php">Assign Drivers</a></li>
                <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <h2 class="text-center">Edit Order</h2>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="form-group">
            <label for="product_name">Product Name</label>
            <input type="text" class="form-control" name="product_name" id="product_name" 
                   value="<?= htmlspecialchars($order['product_name']) ?>" required>
        </div>
        <div class="form-group">
            <label for="status">Status</label>
            <input type="text" class="form-control" name="status" id="status" 
                   value="<?= htmlspecialchars($order['status']) ?>" required>
        </div>
        <div class="form-group">
            <label for="client_id">Client ID</label>
            <input type="number" class="form-control" name="client_id" id="client_id" 
                   value="<?= htmlspecialchars($order['client_id']) ?>" required>
        </div>
        <div class="form-group">
            <label for="driver_id">Driver ID</label>
            <input type="number" class="form-control" name="driver_id" id="driver_id" 
                   value="<?= htmlspecialchars($order['driver_id']) ?>">
        </div>
        <div class="form-group">
            <label for="address">Address</label>
            <input type="text" class="form-control" name="address" id="address" 
                   value="<?= htmlspecialchars($order['address']) ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Update Order</button>
        <a href="manage_orders.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
