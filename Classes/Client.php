<?php
require_once 'User.php';

class Client extends User {
    public function viewOrderStatus($order) {
        return $order->getOrderDetails();
    }
}
?>
