<?php
session_start();
require_once '../database/database.php'; // Include database connection

// Check if the user is logged in as a client
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'client') {
    header("Location: login.php"); // Redirect to login if not a client
    exit();
}

// Handle product purchase
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['buy'])) {
        $productId = $_POST['product_id']; // Get the product ID from the form
        $userId = $_SESSION['user']['id']; // Get the user ID from the session (logged-in user)
        $clientId = $_SESSION['user']['id']; // Assuming the client_id is the same as user_id for clients
        $contact = isset($_SESSION['user']['contact']) ? $_SESSION['user']['contact'] : 'Not Provided'; // Get contact or set default

        // Fetch the product name
        $productStmt = $pdo->prepare("SELECT name FROM products WHERE id = :product_id");
        $productStmt->execute(['product_id' => $productId]);
        $product = $productStmt->fetch(PDO::FETCH_ASSOC);
        
        // Check if the product exists
        if (!$product) {
            echo "<script>alert('Product not found.');</script>";
            exit();
        }
        
        $productName = $product['name']; // Get the product name

        // Fetch a random driver
        $driverStmt = $pdo->prepare("SELECT id FROM users WHERE role = 'driver' ORDER BY RAND() LIMIT 1");
        $driverStmt->execute();
        $driver = $driverStmt->fetch(PDO::FETCH_ASSOC);

        if ($driver) {
            $driverId = $driver['id']; // Assign the random driver ID

            // Insert order into the database using user_id and client_id
            $stmt = $pdo->prepare("INSERT INTO orders (user_id, product_id, client_id, driver_id, status, contact, product_name) VALUES (:user_id, :product_id, :client_id, :driver_id, 'pending', :contact, :product_name)");
            $stmt->execute([
                'user_id' => $userId, // Store the user ID (for logged-in user)
                'product_id' => $productId,
                'client_id' => $clientId, // Store the client ID (assuming it is the same as user ID)
                'driver_id' => $driverId,  // Store the assigned driver ID
                'contact' => $contact,      // Store the contact information
                'product_name' => $productName // Store the product name
            ]);

            // Optional: Redirect to a confirmation page or show a success message
            echo "<script>alert('Order placed successfully!');</script>";
        } else {
            echo "<script>alert('No drivers are available to deliver the product.');</script>";
        }
    }
}

// Fetch products from the database
$stmt = $pdo->prepare("SELECT * FROM products");
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }
        .navbar {
            background-color: #007bff;
        }
        .navbar-brand {
            font-weight: bold;
            color: #fff !important;
        }
        .navbar-nav .nav-link {
            color: #fff !important;
        }
        .container {
            margin-top: 50px;
        }
        h2 {
            font-size: 2.5rem;
            margin-bottom: 40px;
            color: #333;
        }
        .product-card {
            margin-bottom: 30px;
            padding: 20px;
            border: none;
            border-radius: 12px;
            background-color: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .product-card:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }
        .product-card h5 {
            font-size: 1.25rem;
            color: #007bff;
            font-weight: bold;
        }
        .product-card p {
            font-size: 1rem;
            color: #6c757d;
            margin-bottom: 15px;
        }
        .btn-buy, .btn-cart {
            margin-top: 10px;
            padding: 10px 20px;
            font-size: 1rem;
            border-radius: 5px;
            transition: background-color 0.3s ease-in-out;
        }
        .btn-buy {
            background-color: #28a745;
            color: white;
        }
        .btn-buy:hover {
            background-color: #218838;
        }
        .btn-cart {
            background-color: #007bff;
            color: white;
        }
        .btn-cart:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <a class="navbar-brand" href="#">Rindra Delivery Service</a>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="cart.php"><i class="fas fa-shopping-cart"></i> Cart</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container text-center">
        <h2>Welcome to Your Client Dashboard</h2>
        <div class="row">
            <?php foreach ($products as $product): ?>
            <div class="col-md-4">
                <div class="product-card">
                    <h5><?= htmlspecialchars($product['name']) ?></h5>
                    <p>Price: $<?= htmlspecialchars($product['price']) ?></p>
                    <form method="POST" class="d-inline">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        <button type="submit" name="buy" class="btn btn-buy">Buy Now</button>
                    </form>
                    <button class="btn btn-cart" onclick="addToCart(<?= $product['id'] ?>)">Add to Cart</button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        function addToCart(productId) {
            // Handle add to cart functionality
            alert("Product ID " + productId + " has been added to your cart.");
            // You can implement AJAX here to add the product to the cart without reloading the page
        }
    </script>
</body>
</html>
