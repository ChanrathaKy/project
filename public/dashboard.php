<?php
session_start();
require_once '../database/database.php';

// Check if the user is logged in as an admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch all users
$stmt = $pdo->prepare("SELECT * FROM users"); 
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch pending orders
$pendingOrdersStmt = $pdo->prepare("SELECT o.id AS order_id, o.contact, o.status, p.name AS product_name 
FROM orders o 
JOIN products p ON o.product_id = p.id 
WHERE o.status = 'pending'");
$pendingOrdersStmt->execute();
$pendingOrders = $pendingOrdersStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch drivers from the database
$driversStmt = $pdo->prepare("SELECT id, name FROM users WHERE role = 'driver'");
$driversStmt->execute();
$drivers = $driversStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rindra Delivery Service</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        .sidebar {
            background-color: #343a40;
            min-height: 100vh;
        }
        .sidebar a {
            color: #fff;
            padding: 15px;
            text-decoration: none;
            display: block;
        }
        .sidebar a:hover {
            background-color: #495057;
        }
        .navbar {
            background-color: #007bff;
        }
        .navbar-brand, .navbar-nav .nav-link {
            color: #fff !important;
        }
        .dashboard-header {
            margin-top: 30px;
            margin-bottom: 20px;
            text-align: center;
        }
        .card {
            margin-bottom: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .card-header {
            background-color: #007bff;
            color: white;
        }
        .btn {
            margin-top: 10px;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark">
        <a class="navbar-brand" href="#">Rindra Delivery Service</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 sidebar">
                <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a href="manage_users.php"><i class="fas fa-users"></i> Manage Users</a>
                <a href="manage_orders.php"><i class="fas fa-shopping-cart"></i> Manage Orders</a>
                <a href="manage_driver.php"><i class="fas fa-box"></i> Manage Drivers</a>
                <a href="assign_driver.php"><i class="fas fa-chart-line"></i> Assign Drivers</a>
            </div>

            <div class="col-md-10">
                <div class="container">
                    <h2 class="dashboard-header">Welcome, Admin!</h2>

                    <div class="row">
                        <?php foreach ($users as $user): ?>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    User ID: <?= htmlspecialchars($user['id']) ?>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($user['name']) ?></h5>
                                    <p class="card-text"><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                                    <p class="card-text"><strong>Role:</strong> <?= htmlspecialchars($user['role']) ?></p>
                                    <a href="edit_user.php?id=<?= $user['id'] ?>" class="btn btn-primary">Edit User</a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        function assignDriver(orderId, driverId) {
            if (confirm("Are you sure you want to assign this driver?")) {
                // Make an AJAX call to update the order in the database
                $.ajax({
                    url: 'assign_driver.php',
                    method: 'POST',
                    data: {
                        order_id: orderId,
                        driver_id: driverId
                    },
                    success: function(response) {
                        alert(response);
                        location.reload(); // Reload the page to see the changes
                    },
                    error: function() {
                        alert("Error assigning driver.");
                    }
                });
            }
        }
    </script>
</body>
</html>
