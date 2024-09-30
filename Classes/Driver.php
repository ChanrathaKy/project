<?php
require_once 'User.php';

class Driver extends User {
    public function updateOrderStatus($order, $status) {
        $order->updateStatus($status);
    }
}
?>
