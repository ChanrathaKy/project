<?php
session_start();
require_once '../database/database.php'; // Include the database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'];

    try {
        // Begin a transaction
        $pdo->beginTransaction();

        // First, delete any related records in the completed_deliveries table
        $deleteCompletedDeliveriesStmt = $pdo->prepare("DELETE FROM completed_deliveries WHERE order_id = :order_id");
        $deleteCompletedDeliveriesStmt->bindValue(':order_id', $order_id, PDO::PARAM_INT);
        $deleteCompletedDeliveriesStmt->execute();

        // Now delete the order
        $deleteOrderStmt = $pdo->prepare("DELETE FROM orders WHERE id = :order_id");
        $deleteOrderStmt->bindValue(':order_id', $order_id, PDO::PARAM_INT);
        $deleteOrderStmt->execute();

        // Commit the transaction
        $pdo->commit();
        
        // Redirect back to manage orders page with a success message
        header('Location: manage_orders.php?message=Order deleted successfully');
        exit();
    } catch (PDOException $e) {
        // Rollback the transaction in case of an error
        $pdo->rollBack();
        
        // Redirect back to manage orders page with an error message
        header('Location: manage_orders.php?error=' . urlencode($e->getMessage()));
        exit();
    }
}
?>
