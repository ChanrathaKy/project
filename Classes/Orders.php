<?php
session_start();
if ($_SESSION['user']['role'] !== 'admin') {
    header("Location: dashboard.php");
    exit();
}

// Fetch orders from the database
// $orders = fetchOrdersFromDB();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Manage Orders</h1>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Client Name</th>
                    <th>Product</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <!-- Loop through the orders and display them here -->
                <?php //foreach($orders as $order): ?>
                <tr>
                    <td><?php //echo $order['id']; ?></td>
                    <td><?php //echo $order['client_name']; ?></td>
                    <td><?php //echo $order['product_name']; ?></td>
                    <td><?php //echo $order['status']; ?></td>
                    <td>
                        <a href="edit_order.php?id=<?php //echo $order['id']; ?>" class="btn btn-warning">Edit</a>
                        <a href="delete_order.php?id=<?php //echo $order['id']; ?>" class="btn btn-danger">Delete</a>
                    </td>
                </tr>
                <?php //endforeach; ?>
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
