<?php
session_start();
require 'db_connect.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $user_id = $_SESSION['user_id'];
    $meal_id = $_POST['meal_id'];
    $quantity = $_POST['quantity'];
    $note = $_POST['note'];


    $query = 'INSERT INTO cart (user_id, meal_id, quantity, note) VALUES (:user_id, :meal_id, :quantity, :note)';
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'user_id' => $user_id,
        'meal_id' => $meal_id,
        'quantity' => $quantity, 
        'note' => $note
    ]);

    echo "Yemek sepete başarıyla eklendi!";
    header('Location: restaurant.php?id=' . $_POST['restaurant_id']);
    exit;
} else {
    echo "Geçersiz istek!";
}
?>
