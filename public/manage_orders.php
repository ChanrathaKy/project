<?php
session_start();
require_once '../database/database.php'; // Include the database connection

// Pagination setup
$limit = 10; // Number of records per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$user = $_SESSION['user'];
$role = $user['role'] ?? ''; // Ensuring $role is set properly

// Variable to hold fetched orders
$orders = [];

// Fetch orders based on user role
if ($role === 'admin') {
    // Fetch all orders for admin
    $stmt = $pdo->prepare("SELECT o.*, c.name as client_name, d.name as driver_name FROM orders o 
                            LEFT JOIN users c ON o.client_id = c.id 
                            LEFT JOIN users d ON o.driver_id = d.id 
                            LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get total orders count for pagination
    $totalOrdersStmt = $pdo->query("SELECT COUNT(*) FROM orders");
    $totalOrders = $totalOrdersStmt->fetchColumn();
} elseif ($role === 'client') {
    // Fetch orders for the logged-in client
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE client_id = :client_id LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':client_id', $user['id'], PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get total orders count for pagination
    $totalOrdersStmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE client_id = :client_id");
    $totalOrdersStmt->execute(['client_id' => $user['id']]);
    $totalOrders = $totalOrdersStmt->fetchColumn();
} elseif ($role === 'driver') {
    // Fetch deliveries for the logged-in driver
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE driver_id = :driver_id LIMIT :limit OFFSET :offset");
    $stmt->bindValue(':driver_id', $user['id'], PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get total deliveries count for pagination
    $totalOrdersStmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE driver_id = :driver_id");
    $totalOrdersStmt->execute(['driver_id' => $user['id']]);
    $totalOrders = $totalOrdersStmt->fetchColumn();
} else {
    $totalOrders = 0; // If no valid role, no orders to display
}

// Calculate total pages
$totalPages = $totalOrders > 0 ? ceil($totalOrders / $limit) : 1;
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

    <div class="container mt-5">
        <h2 class="text-center">Order and Delivery History</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Product Name</th>
                    <th>Status</th>
                    <th>Client</th>
                    <?php if ($role === 'admin' || $role === 'driver'): ?>
                        <th>Driver</th>
                    <?php endif; ?>
                    <th>Created At</th>
                    <th>Delivery Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($orders)): ?>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?= htmlspecialchars($order['id']) ?></td>
                            <!-- Check if product_name exists and is not null -->
                            <td><?= isset($order['product_name']) ? htmlspecialchars($order['product_name']) : 'N/A' ?></td>
                            <td><?= htmlspecialchars($order['status']) ?></td>
                            <td><?= htmlspecialchars($order['client_name'] ?? 'N/A') ?></td>
                            <?php if ($role === 'admin' || $role === 'driver'): ?>
                                <td><?= htmlspecialchars($order['driver_name'] ?? 'Unassigned') ?></td>
                            <?php endif; ?>
                            <!-- Check if created_at exists and is not null -->
                            <td><?= isset($order['created_at']) ? htmlspecialchars($order['created_at']) : 'N/A' ?></td>
                            <td><?= isset($order['delivery_date']) ? htmlspecialchars($order['delivery_date']) : 'N/A' ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">No orders found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <nav>
            <ul class="pagination justify-content-center">
                <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $page - 1 ?>">Previous</a>
                </li>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $page + 1 ?>">Next</a>
                </li>
            </ul>
        </nav>
    </div>
</body>
</html>
