<?php
session_start();
require_once '../database/database.php'; // Include the database connection

// Handle form submission for creating a new order
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'];
    $client_id = $_SESSION['user']['id'];
    $delivery_date = $_POST['delivery_date'] ?? null;

    $stmt = $pdo->prepare("INSERT INTO orders (product_id, client_id, delivery_date, status) VALUES (:product_id, :client_id, :delivery_date, 'pending')");
    $stmt->execute([
        'product_id' => $product_id,
        'client_id' => $client_id,
        'delivery_date' => $delivery_date
    ]);
}

// Fetch all products for the dropdown
$stmt = $pdo->query("SELECT * FROM products");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Order</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center">Create Order</h2>

        <form method="POST">
            <div class="form-group">
                <label for="product_id">Product:</label>
                <select class="form-control" name="product_id" required>
                    <?php foreach ($products as $product): ?>
                        <option value="<?= htmlspecialchars($product['id']) ?>"><?= htmlspecialchars($product['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="delivery_date">Delivery Date:</label>
                <input type="datetime-local" class="form-control" name="delivery_date" required>
            </div>
            <button type="submit" class="btn btn-primary">Create Order</button>
        </form>
    </div>
</body>
</html>
