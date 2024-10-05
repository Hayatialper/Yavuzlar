<?php
session_start();
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $meal_id = $_POST['meal_id'];
    $query = 'INSERT INTO cart (user_id, meal_id, quantity) VALUES (:user_id, :meal_id, 1)';
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'user_id' => $_SESSION['user_id'],
        'meal_id' => $meal_id
    ]);

    echo "Yemek sepete eklendi!";
}
