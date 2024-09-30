<?php
session_start();
require 'database.php'; // Include your database connection file

// Check if admin is logged in
if ($_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Fetch pending orders
$sql = "SELECT o.id, o.product_id, o.status, u.name AS client_name 
        FROM orders o 
        JOIN users u ON o.user_id = u.id 
        WHERE o.status = 'pending'";
$result_orders = $conn->query($sql);

// Fetch available drivers
$sql_drivers = "SELECT id, name FROM users WHERE role = 'driver'";
$result_drivers = $conn->query($sql_drivers);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assign Order</title>
    <link rel="stylesheet" href="path/to/bootstrap.css"> <!-- Link your CSS file -->
</head>
<body>
<?php if (isset($_GET['message'])): ?>
    <div class="alert alert-info"><?php echo htmlspecialchars($_GET['message']); ?></div>
<?php endif; ?>

    <div class="container">
        <h1>Assign Orders</h1>
        <table class="table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Client Name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result_orders->num_rows > 0): ?>
                    <?php while ($order = $result_orders->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $order['id']; ?></td>
                            <td><?php echo $order['client_name']; ?></td>
                            <td>
                                <form action="assign_order_process.php" method="POST">
                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                    <select name="driver_id" required>
                                        <option value="">Select Driver</option>
                                        <?php while ($driver = $result_drivers->fetch_assoc()): ?>
                                            <option value="<?php echo $driver['id']; ?>"><?php echo $driver['name']; ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                    <button type="submit" class="btn btn-primary">Assign</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3">No pending orders.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
