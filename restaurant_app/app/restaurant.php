<?php
session_start();
require 'db_connect.php';

$restaurant_id = $_GET['id'];
$query = 'SELECT * FROM restaurants WHERE id = :id';
$stmt = $pdo->prepare($query);
$stmt->execute(['id' => $restaurant_id]);
$restaurant = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$restaurant) { 
    echo "Restoran bulunamadı!";
    exit;
}

$meal_query = 'SELECT * FROM meals WHERE restaurant_id = :restaurant_id';
$meal_stmt = $pdo->prepare($meal_query);
$meal_stmt->execute(['restaurant_id' => $restaurant_id]);
$meals = $meal_stmt->fetchAll(PDO::FETCH_ASSOC);

$cart_items = [];
$total_price = 0;
$discount = 0;
$discounted_total = 0;
$coupon_applied = false; 

if (isset($_SESSION['user_id'])) {
    $cart_query = 'SELECT cart.id AS cart_id, meals.name, meals.price, cart.quantity 
                   FROM cart 
                   JOIN meals ON cart.meal_id = meals.id 
                   WHERE cart.user_id = :user_id';
    $cart_stmt = $pdo->prepare($cart_query);
    $cart_stmt->execute(['user_id' => $_SESSION['user_id']]);
    $cart_items = $cart_stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($cart_items as $item) {
        $total_price += $item['price'] * $item['quantity'];
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['apply_coupon'])) {
    $coupon_code = $_POST['coupon_code'];
    $coupon_query = 'SELECT * FROM coupons WHERE code = :code AND valid_until >= CURDATE()';
    $coupon_stmt = $pdo->prepare($coupon_query);
    $coupon_stmt->execute(['code' => $coupon_code]);
    $coupon = $coupon_stmt->fetch(PDO::FETCH_ASSOC);

    if ($coupon) {
        $discount = ($total_price * $coupon['discount_percentage']) / 100;
        $discounted_total = $total_price - $discount;
        $coupon_applied = true;
    } else {
        echo "Geçersiz veya süresi dolmuş kupon kodu!";
    }
}

if ($discounted_total == 0) {
    $discounted_total = $total_price;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['clear_cart'])) {
    $clear_cart_query = 'DELETE FROM cart WHERE user_id = :user_id';
    $clear_cart_stmt = $pdo->prepare($clear_cart_query);
    $clear_cart_stmt->execute(['user_id' => $_SESSION['user_id']]);
    header('Location: restaurant.php?id=' . $restaurant_id); 
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['meal_id'])) {
    $meal_id = $_POST['meal_id'];
    $quantity = $_POST['quantity'];
    $note = $_POST['note'];
    $meal_query = 'SELECT * FROM meals WHERE id = :meal_id';
    $meal_stmt = $pdo->prepare($meal_query);
    $meal_stmt->execute(['meal_id' => $meal_id]);
    $meal = $meal_stmt->fetch(PDO::FETCH_ASSOC);

    if ($meal) {
        $total_price = $meal['price'] * $quantity;

        $cart_query = 'INSERT INTO cart (user_id, meal_id, quantity, note) 
                       VALUES (:user_id, :meal_id, :quantity, :note)';
        $cart_stmt = $pdo->prepare($cart_query);
        $cart_stmt->execute([
            'user_id' => $_SESSION['user_id'],
            'meal_id' => $meal_id,
            'quantity' => $quantity,
            'note' => $note
        ]);

        echo "Sipariş sepete eklendi!";
        header('Location: restaurant.php?id=' . $restaurant_id);
        exit;
    } else {
        echo "Yemek bulunamadı!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($restaurant['name']); ?></title>
    <style>

        *{
            margin:0;
            padding:0;
        }
        .popup {
            display: none;
            position: fixed;
            top: -50px;
            left: 50%;
            transform: translateX(-50%);
            background-color: green;
            color: white;
            padding: 10px;
            border-radius: 5px;
            z-index: 1000;
            transition: top 0.5s ease-in-out;
        }
        .popup.show {
            top: 20px;
        }


        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
        }

        h1 {
            text-align: center;
            padding: 20px 0;
            background-color: #333;
            color: white;
        }

        .restaurant-image {
            width: 100%;
            max-height: 400px;
            object-fit: cover;
            margin-bottom: 20px;
        }

        .container {
            display: flex;
            justify-content: space-between;
            margin: 20px auto;
            max-width: 1200px;
            padding: 20px;
        }

        .meal-container {
            width: 70%;
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: flex-start;
        }

        .meal {
            border: 1px solid #ddd;
            padding: 30px;
            width: 250px;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .meal:hover {
            transform: scale(1.10);
        }

        .meal img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            margin-bottom: 15px;
        }

        .meal-price {
            font-weight: bold;
            margin-top: 5px;
        }

        .cart {
            width: 25%;
            padding: 20px;
            border: 1px solid #ddd;
            background-color: white;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .cart table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .cart th, .cart td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }

        .cart th {
            background-color: #f2f2f2;
        }

        .balance-info {
            margin-top: 20px;
            font-weight: bold;
        }

        .balance-warning {
            color: red;
        }

        button {
            background-color: #28a745;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #218838;
        }

        textarea {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
        }

        label {
            font-weight: bold;
        }

        /* Kupon Alanı */
        .coupon-container {
            margin-top: 20px;
            text-align: center;
        }

        .coupon-container input {
            padding: 10px;
            width: 70%;
            margin-bottom: 10px;
        }
        .reviews-button {
            margin-top: 20px;
            text-align: center;
        }

    </style>
</head>

<body>

    <div class="popup" id="coupon-popup">Kupon başarıyla uygulandı!</div>
    <h1><?php echo htmlspecialchars($restaurant['name']); ?></h1>
    <img src="<?php echo htmlspecialchars($restaurant['image_path']); ?>" class="restaurant-image" alt="Restoran Resmi">
    <p style="text-align: center;"><?php echo htmlspecialchars($restaurant['description']); ?></p>

    <div class="container">
        <div class="meal-container">
            <?php if (count($meals) > 0): ?>
                <?php foreach ($meals as $meal): ?>
                    <div class="meal">
                        <img src="<?php echo $meal['image_path']; ?>" alt="Yemek Resmi">
                        <h3><?php echo htmlspecialchars($meal['name']); ?></h3>
                        <p><?php echo htmlspecialchars($meal['description']); ?></p>
                        <p class="meal-price"><?php echo htmlspecialchars($meal['price']); ?> TL</p>
                        <form action="restaurant.php?id=<?php echo $restaurant_id; ?>" method="post">
                            <input type="hidden" name="meal_id" value="<?php echo $meal['id']; ?>">
                            <label for="quantity">Adet:</label>
                            <input type="number" id="quantity" name="quantity" min="1" value="1" required><br><br>
                            <label for="note">Not (isteğe bağlı):</label><br>
                            <textarea id="note" name="note"></textarea><br><br>
                            <button type="submit">Sepete Ekle</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Bu restoranda yemek bulunmamaktadır.</p>
            <?php endif; ?>
        </div>


        <div class="cart">
            <h2>Sepetiniz</h2>
            <?php if (!empty($cart_items)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Yemek</th>
                            <th>Fiyat</th>
                            <th>Adet</th>
                            <th>Toplam</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cart_items as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['name']); ?></td>
                                <td><?php echo htmlspecialchars($item['price']); ?> TL</td>
                                <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                                <td><?php echo $item['price'] * $item['quantity']; ?> TL</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="3">Toplam Fiyat</th>
                            <th><?php echo $total_price; ?> TL</th>
                        </tr>
                        <tr>
                            <th colspan="3">İndirimli Fiyat</th>
                            <th><?php echo $discounted_total; ?> TL</th>
                        </tr>
                    </tfoot>
                </table>

                <form action="restaurant.php?id=<?php echo $restaurant_id; ?>" method="post">
                    <input type="hidden" name="clear_cart" value="1">
                    <button type="submit">Sepeti Temizle</button>
                </form>

                <div class="balance-info">
                    <p>Mevcut Bakiye: <?php echo isset($_SESSION['balance']) ? $_SESSION['balance'] . ' TL' : 'Bakiye bilgisi alınamadı'; ?></p>
                    <?php if (isset($_SESSION['balance']) && $_SESSION['balance'] >= $discounted_total): ?>
                        <form action="checkout.php" method="post">
                            <button type="submit">Sepeti Onayla</button>
                        </form>
                    <?php else: ?>
                        <p class="balance-warning">Bakiye yetersiz. Lütfen bakiye yükleyin.</p>
                    <?php endif; ?>
                </div>
                <div class="coupon-container">
                    <h3>Kupon Kodu Uygula</h3>
                    <form action="restaurant.php?id=<?php echo $restaurant_id; ?>" method="post">
                        <input type="text" name="coupon_code" placeholder="Kupon kodunu girin" required>
                        <button type="submit" name="apply_coupon">Kupon Uygula</button>
                    </form>
                </div>
                <div class="reviews-button">
                    <a href="restaurant_ratings.php?id=<?php echo $restaurant_id; ?>"><button>Yorumları Görüntüle</button></a>
                </div>

            <?php else: ?>
                <p>Sepetiniz boş.</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
        <?php if ($coupon_applied): ?>
        const popup = document.getElementById('coupon-popup');
        popup.classList.add('show');
        setTimeout(() => {
            popup.classList.remove('show');
        }, 3000);
        <?php endif; ?>
    </script>

</body>
</html>
