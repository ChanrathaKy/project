<?php
session_start();
require_once '../database/database.php'; // Include your database connection

// Check if the user is logged in as admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php"); // Redirect to login if not an admin
    exit();
}

// Fetch all active drivers
$stmt = $pdo->prepare("SELECT * FROM drivers WHERE status = 'active'");
$stmt->execute();
$drivers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Drivers</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>
    <div class="container">
        <h2 class="text-center">Registered Drivers</h2>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>License Number</th>
                    <th>Status</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($drivers) > 0): ?>
                    <?php foreach ($drivers as $driver): ?>
                    <tr>
                        <td><?= htmlspecialchars($driver['id']) ?></td>
                        <td><?= htmlspecialchars($driver['name']) ?></td>
                        <td><?= htmlspecialchars($driver['email']) ?></td>
                        <td><?= htmlspecialchars($driver['phone']) ?></td>
                        <td><?= htmlspecialchars($driver['license_number']) ?></td>
                        <td><?= htmlspecialchars($driver['status']) ?></td>
                        <td><?= htmlspecialchars($driver['created_at']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">No active drivers available.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <a href="assign_driver.php" class="btn btn-primary">Back to Assign Drivers</a>
    </div>
</body>
</html>
