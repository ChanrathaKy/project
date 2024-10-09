<?php
session_start();
require_once '../database/database.php'; // Include database connection

// Check if the user is logged in as admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php"); // Redirect to login if not an admin
    exit();
}

// Fetch pending orders from the database
$stmt = $pdo->prepare("
    SELECT o.id AS order_id, o.contact, o.status, p.name AS product_name, c.name AS client_name, o.address 
    FROM orders o 
    JOIN products p ON o.product_id = p.id 
    JOIN users c ON o.client_id = c.id 
    WHERE o.status = 'pending'  -- Only fetch pending orders
");
$stmt->execute();
$pendingOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch users with driver role from the database
$driversStmt = $pdo->prepare("SELECT id, name FROM users WHERE role = 'driver'"); // Adjusted to fetch drivers from users table
$driversStmt->execute();
$drivers = $driversStmt->fetchAll(PDO::FETCH_ASSOC);

// Handle driver assignment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_driver'])) {
    $orderId = $_POST['order_id'];
    $driverId = $_POST['driver_id'];

    // Update the order with the assigned driver
    $updateStmt = $pdo->prepare("UPDATE orders SET driver_id = :driver_id, status = 'assigned' WHERE id = :order_id");
    $updateStmt->execute(['driver_id' => $driverId, 'order_id' => $orderId]);

    // Redirect back to the same page with a success message
    header("Location: assign_driver.php?success=1");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Driver</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="path/to/your/custom.css"> <!-- Custom CSS -->
</head>
<body>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="#">
            Rindra Delivery Service
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
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
    <h2 class="text-center">Manage Driver Assignment</h2>

    <!-- Success/Error Messages -->
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">Driver assigned successfully!</div>
    <?php endif; ?>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Client Name</th>
                <th>Status</th>
                <th>Product Name</th>
                <th>Contact</th>
                <th>Address</th> <!-- New column for Address -->
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($pendingOrders) > 0): ?>
                <?php foreach ($pendingOrders as $order): ?>
                <tr>
                    <td><?= htmlspecialchars($order['order_id']) ?></td>
                    <td><?= htmlspecialchars($order['client_name']) ?></td>
                    <td><?= htmlspecialchars($order['status']) ?></td>
                    <td><?= htmlspecialchars($order['product_name']) ?></td>
                    <td><?= htmlspecialchars($order['contact']) ?></td>
                    <td><?= isset($order['address']) ? htmlspecialchars($order['address']) : 'Not Provided' ?></td> <!-- Updated this line -->
                    <td>
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#assignDriverModal<?= $order['order_id'] ?>">
                            Assign Driver
                        </button>
                    </td>
                </tr>

                <!-- Driver Assignment Modal -->
                <div class="modal fade" id="assignDriverModal<?= $order['order_id'] ?>" tabindex="-1" role="dialog" aria-labelledby="assignDriverModalLabel<?= $order['order_id'] ?>" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="assignDriverModalLabel<?= $order['order_id'] ?>">Assign Driver for Order ID: <?= $order['order_id'] ?></h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="assign_driver.php">
                                    <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                                    <h6>Select a Driver:</h6>
                                    <select name="driver_id" class="form-control">
                                        <option value="">--Select Driver--</option>
                                        <?php foreach ($drivers as $driver): ?>
                                            <option value="<?= $driver['id'] ?>"><?= htmlspecialchars($driver['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" name="assign_driver" class="btn btn-success">Assign Driver</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                                </form>
                            </div>
                        </div>
                    </div>

                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center">No pending orders available.</td> <!-- Updated colspan to 7 -->
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
