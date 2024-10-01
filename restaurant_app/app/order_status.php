<?php
session_start();
require 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    echo "Giriş yapmadınız.";
    exit;
}

$user_id = $_SESSION['user_id'];
$order_query = 'SELECT orders.*, meals.name AS meal_name FROM orders 
                JOIN meals ON orders.meal_id = meals.id 
                WHERE orders.user_id = :user_id ORDER BY orders.created_at DESC';
$order_stmt = $pdo->prepare($order_query);
$order_stmt->execute(['user_id' => $user_id]);
$orders = $order_stmt->fetchAll(PDO::FETCH_ASSOC);
$current_orders = array_filter($orders, function($order) {
    return $order['status'] === 'Yolda' || $order['status'] === 'Hazırlanıyor';
});
$past_orders = array_filter($orders, function($order) {
    return $order['status'] === 'Teslim Edildi';
});



if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['rate_order'])) {
    $order_id = $_POST['order_id'];
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];

    $rating_query = 'INSERT INTO ratings (user_id, order_id, rating, comment, created_at) 
                     VALUES (:user_id, :order_id, :rating, :comment, NOW())';
    $rating_stmt = $pdo->prepare($rating_query);
    $rating_stmt->execute([
        'user_id' => $user_id,
        'order_id' => $order_id,
        'rating' => $rating,
        'comment' => $comment
    ]);

    header('Location: order_status.php');
    exit;
}
?>






<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sipariş Durumlarınız</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
        }

        h1, h2 {
            text-align: center;
            color: #333;
        }

        .orders-section {
            width: 80%;
            margin: 20px auto;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.1);
        }

        .order-item {
            border-bottom: 1px solid #ddd;
            padding: 15px 0;
            position: relative;
        }

        .order-item:last-child {
            border-bottom: none;
        }

        .order-item h3 {
            margin-bottom: 10px;
        }

        .order-info {
            display: flex;
            justify-content: space-between;
        }

        .order-info div {
            margin-bottom: 5px;
        }

        .rating-container {
            margin-top: 15px;
            display: none;
        }

        .rating-container.show {
            display: block;
        }

        .rating-stars input[type="radio"] {
            display: none;
        }

        .rating-stars label {
            font-size: 30px;
            color: #ccc;
            cursor: pointer;
        }

        .rating-stars input[type="radio"]:checked ~ label {
            color: gold;
        }

        .rate-btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .rate-btn:hover {
            background-color: #45a049;
        }

        .comment-box {
            margin-top: 10px;
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .submit-rating {
            background-color: #007BFF;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .submit-rating:hover {
            background-color: #0056b3;
        }
    </style>
    <script>
        function toggleRating(orderId) {
            const ratingContainer = document.getElementById('rating-container-' + orderId);
            if (ratingContainer.style.display === 'block') {
                ratingContainer.style.display = 'none';
            } else {
                ratingContainer.style.display = 'block';
            }
        }
    </script>
</head>
<body>

    <h1>Sipariş Durumlarınız</h1>

    <div class="orders-section">
        <h2>Güncel Siparişler</h2>
        <?php if (!empty($current_orders)): ?>
            <?php foreach ($current_orders as $order): ?>
                <div class="order-item">
                    <h3><?php echo htmlspecialchars($order['meal_name']); ?></h3>
                    <div class="order-info">
                        <div>Adet: <?php echo htmlspecialchars($order['quantity']); ?></div>
                        <div>Toplam Fiyat: <?php echo htmlspecialchars($order['total_price']); ?> TL</div>
                        <div>Durum: <?php echo htmlspecialchars($order['status']); ?></div>
                    </div>
                    <?php if (!empty($order['coupon_code'])): ?>
                        <div>Kupon Kodu: <?php echo htmlspecialchars($order['coupon_code']); ?></div>
                        <div>İndirim: <?php echo htmlspecialchars($order['discount_applied']); ?> TL</div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Henüz güncel siparişiniz bulunmamaktadır.</p>
        <?php endif; ?>
    </div>

    <div class="orders-section">
        <h2>Geçmiş Siparişler</h2>
        <?php if (!empty($past_orders)): ?>
            <?php foreach ($past_orders as $order): ?>
                <div class="order-item">
                    <h3><?php echo htmlspecialchars($order['meal_name']); ?></h3>
                    <div class="order-info">
                        <div>Adet: <?php echo htmlspecialchars($order['quantity']); ?></div>
                        <div>Toplam Fiyat: <?php echo htmlspecialchars($order['total_price']); ?> TL</div>
                        <div>Durum: <?php echo htmlspecialchars($order['status']); ?></div>
                    </div>
                    <?php if (!empty($order['coupon_code'])): ?>
                        <div>Kupon Kodu: <?php echo htmlspecialchars($order['coupon_code']); ?></div>
                        <div>İndirim: <?php echo htmlspecialchars($order['discount_applied']); ?> TL</div>
                    <?php endif; ?>
                    <button class="rate-btn" onclick="toggleRating(<?php echo $order['id']; ?>)">Siparişi Oyla</button>
                    <div class="rating-container" id="rating-container-<?php echo $order['id']; ?>">
                        <form action="order_status.php" method="post">
                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                            <div class="rating-stars">
                                <?php for ($i = 10; $i >= 1; $i--): ?>
                                    <input type="radio" id="star<?php echo $i; ?>-<?php echo $order['id']; ?>" name="rating" value="<?php echo $i; ?>" required>
                                    <label for="star<?php echo $i; ?>-<?php echo $order['id']; ?>">&#9733;</label>
                                <?php endfor; ?>
                            </div>
                            <textarea class="comment-box" name="comment" placeholder="Yorumunuzu yazın..." required></textarea>
                            <button type="submit" class="submit-rating" name="rate_order">Gönder</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Henüz geçmiş siparişiniz bulunmamaktadır.</p>
        <?php endif; ?>
    </div>

</body>
</html>
