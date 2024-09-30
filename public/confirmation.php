<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php"); // Redirect to login if not logged in
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container text-center" style="margin-top: 50px;">
        <h2>Order Confirmation</h2>
        <p>Your order has been placed successfully!</p>
        <a href="client_dashboard.php" class="btn btn-primary">Back to Dashboard</a>
    </div>
</body>
</html>
