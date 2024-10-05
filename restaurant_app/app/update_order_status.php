<?php
session_start();
require 'db_connect.php';
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'restaurant_user' && $_SESSION['role'] !== 'company')) {
    echo "Bu sayfaya erişim izniniz yok.";
    exit;
}




if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['order_id'], $_POST['new_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['new_status'];
    $query = 'UPDATE orders SET status = :new_status WHERE id = :order_id';
    $stmt = $pdo->prepare($query);
    $stmt->execute(['new_status' => $new_status, 'order_id' => $order_id]);
    header('Location: restaurant_orders.php');
    exit;
} else {
    echo "Sipariş ID'si veya yeni durum belirtilmedi.";
    exit;
}
?>
