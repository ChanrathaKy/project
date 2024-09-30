<?php
session_start(); // Start the session
require_once '../database/database.php'; // Include the database connection

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($newPassword === $confirmPassword) {
        // Check if the token is valid
        $stmt = $pdo->prepare("SELECT * FROM password_resets WHERE token = :token");
        $stmt->execute(['token' => $token]);
        $resetRequest = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($resetRequest) {
            // Hash the new password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            // Update the user's password
            $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE email = :email");
            $stmt->execute(['password' => $hashedPassword, 'email' => $resetRequest['email']]);

            // Optionally delete the token from the database
            $stmt = $pdo->prepare("DELETE FROM password_resets WHERE token = :token");
            $stmt->execute(['token' => $token]);

            $successMessage = "Your password has been reset successfully! You can now <a href='index.php'>login</a>.";
        } else {
            $error = "Invalid or expired token.";
        }
    } else {
        $error = "Passwords do not match.";
    }
} else {
    $token = $_GET['token'] ?? '';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 100px;
            max-width: 400px;
        }
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #007bff; 
            color: white; 
            padding: 20px;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            text-align: center;
            font-weight: 600;
        }
        .error {
            color: red;
            text-align: center;
        }
        .success {
            color: green;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="header">
                <h2>Reset Password</h2>
            </div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <p class="error"><?= htmlspecialchars($error); ?></p>
                <?php endif; ?>
                <?php if (isset($successMessage)): ?>
                    <p class="success"><?= htmlspecialchars($successMessage); ?></p>
                <?php endif; ?>
                <form method="POST" action="">
                    <input type="hidden" name="token" value="<?= htmlspecialchars($token); ?>">
                    <div class
