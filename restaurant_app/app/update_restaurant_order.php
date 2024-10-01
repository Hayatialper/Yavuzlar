<?php
session_start();
require 'db_connect.php';
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'company' && $_SESSION['role'] !== 'restaurant_user')) {
    echo "Bu sayfaya erişim izniniz yok.";
    exit;
}


$order_id = $_POST['order_id'];
$new_status = $_POST['new_status'];
$update_query = 'UPDATE orders SET status = :new_status WHERE id = :order_id';
$update_stmt = $pdo->prepare($update_query);
$update_stmt->execute(['new_status' => $new_status, 'order_id' => $order_id]);



header('Location: restaurant_orders.php');
exit;
