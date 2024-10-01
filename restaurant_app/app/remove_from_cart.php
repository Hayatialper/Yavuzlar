<?php
session_start();

require 'db_connect.php';



if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cart_id = $_POST['cart_id'];


    $query = 'DELETE FROM cart WHERE id = :cart_id';
    $stmt = $pdo->prepare($query);
    $stmt->execute(['cart_id' => $cart_id]);

    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}
