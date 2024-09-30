<?php
session_start();
require_once '../database/database.php'; // Include the database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']) ?? '';
    $password = trim($_POST['password']) ?? '';

    if (!empty($username) && !empty($password)) {
        // Fetch user from the database
        $stmt = $pdo->prepare("SELECT * FROM users WHERE name = :name");
        $stmt->execute(['name' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Password is correct, log the user in
            $_SESSION['user'] = $user; // Store user data in session

            // Insert login log
            $stmt = $pdo->prepare("INSERT INTO login_log (user_id, username, login_time, role) VALUES (:user_id, :username, NOW(), :role)");
            $stmt->execute([
                'user_id' => $user['id'], // Assuming the users table has an 'id' column
                'username' => $user['name'],
                'role' => $user['role']
            ]);

            // Redirect based on the role
            if ($user['role'] === 'admin') {
                header("Location: dashboard.php"); // Admin dashboard
            } elseif ($user['role'] === 'client') {
                header("Location: client_dashboard.php"); // Client dashboard
            } elseif ($user['role'] === 'driver') {
                header("Location: driver_dashboard.php"); // Driver dashboard
            }
            exit();
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Please fill in both fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f4f4f4;
        }
        .container {
            margin-top: 100px;
            max-width: 400px;
        }
        .error {
            color: red;
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container bg-white p-4 border rounded shadow">
        <h2 class="text-center">Login</h2>
        <?php if (isset($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" class="form-control" name="username" id="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" class="form-control" name="password" id="password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Login</button>
        </form>
        <p class="text-center">Don't have an account? <a href="register.php">Register</a></p>
        <p class="text-center"><a href="forgotpassword.php">Forgot Password?</a></p>
    </div>
</body>
</html>
