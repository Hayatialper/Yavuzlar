<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    echo "Giriş yapmadınız.";
    exit;
}

$user_id = $_SESSION['user_id'];

$cart_query = 'SELECT cart.id AS cart_id, meals.id AS meal_id, meals.price, cart.quantity, cart.note, meals.restaurant_id 
               FROM cart 
               JOIN meals ON cart.meal_id = meals.id 
               WHERE cart.user_id = :user_id';
$cart_stmt = $pdo->prepare($cart_query);
$cart_stmt->execute(['user_id' => $user_id]);
$cart_items = $cart_stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($cart_items)) {
    echo "Sepetiniz boş.";
    exit;
}

$restaurant_id = $cart_items[0]['restaurant_id'];

$total_price = 0;
foreach ($cart_items as $item) {
    $total_price += $item['price'] * $item['quantity'];
}

$discount = 0;
$coupon_code = isset($_SESSION['coupon_code']) ? $_SESSION['coupon_code'] : null;
if ($coupon_code) {
    $coupon_query = 'SELECT * FROM coupons WHERE code = :code AND valid_until >= CURDATE()';
    $coupon_stmt = $pdo->prepare($coupon_query);
    $coupon_stmt->execute(['code' => $coupon_code]);
    $coupon = $coupon_stmt->fetch(PDO::FETCH_ASSOC);

    if ($coupon) {
        $discount = ($total_price * $coupon['discount_percentage']) / 100;
        $total_price -= $discount;
    }
}

$balance_query = 'SELECT balance FROM users WHERE id = :user_id';
$balance_stmt = $pdo->prepare($balance_query);
$balance_stmt->execute(['user_id' => $user_id]);
$balance = $balance_stmt->fetchColumn();

if ($balance < $total_price) {
    echo "Bakiye yetersiz, lütfen bakiye yükleyin!";
    exit;
}
foreach ($cart_items as $item) {
    $order_query = 'INSERT INTO orders (user_id, meal_id, restaurant_id, quantity, total_price, note, status, coupon_code, discount_applied) 
                    VALUES (:user_id, :meal_id, :restaurant_id, :quantity, :total_price, :note, "Hazırlanıyor", :coupon_code, :discount_applied)';
    $order_stmt = $pdo->prepare($order_query);
    $order_stmt->execute([
        'user_id' => $user_id,
        'meal_id' => $item['meal_id'],
        'restaurant_id' => $restaurant_id,
        'quantity' => $item['quantity'],
        'total_price' => ($item['price'] * $item['quantity']) - ($discount / count($cart_items)),
        'note' => $item['note'],
        'coupon_code' => $coupon_code,
        'discount_applied' => $discount
    ]);
}


$delete_cart_query = 'DELETE FROM cart WHERE user_id = :user_id';
$delete_cart_stmt = $pdo->prepare($delete_cart_query);
$delete_cart_stmt->execute(['user_id' => $user_id]);


$new_balance = $balance - $total_price;
$update_balance_query = 'UPDATE users SET balance = :balance WHERE id = :user_id';
$update_balance_stmt = $pdo->prepare($update_balance_query);
$update_balance_stmt->execute(['balance' => $new_balance, 'user_id' => $user_id]);


unset($_SESSION['coupon_code']);

header('Location: order_status.php');
exit;
?>
