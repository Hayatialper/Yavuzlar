<?php
session_start();
require 'db_connect.php';


if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'company' && $_SESSION['role'] !== 'restaurant_user')) {
    echo "Bu sayfaya erişim izniniz yok.";
    exit;
}


if ($_SESSION['role'] === 'company') {
    $company_id = $_SESSION['user_id'];
    $restaurant_query = 'SELECT id FROM restaurants WHERE company_id = :company_id';
    $restaurant_stmt = $pdo->prepare($restaurant_query);
    $restaurant_stmt->execute(['company_id' => $company_id]);
    $restaurant = $restaurant_stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($restaurant) {
        $restaurant_id = $restaurant['id'];
    } else {
        echo "Restoran bulunamadı.";
        exit;
    }
} else {
    if (!isset($_SESSION['restaurant_id'])) {
        echo "Restoran ID bulunamadı.";
        exit;
    }
    $restaurant_id = $_SESSION['restaurant_id'];
}


$order_query = 'SELECT orders.id, users.username, meals.name, orders.quantity, orders.total_price, 
                orders.note, orders.status, orders.coupon_code, orders.discount_applied 
                FROM orders
                JOIN meals ON orders.meal_id = meals.id
                JOIN users ON orders.user_id = users.id
                WHERE meals.restaurant_id = :restaurant_id';
$order_stmt = $pdo->prepare($order_query);
$order_stmt->execute(['restaurant_id' => $restaurant_id]);
$orders = $order_stmt->fetchAll(PDO::FETCH_ASSOC);
$current_orders = array_filter($orders, function($order) {
    return $order['status'] !== 'Teslim Edildi';
});

$past_orders = array_filter($orders, function($order) {
    return $order['status'] === 'Teslim Edildi';
});
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restoran Siparişleri</title>
    <style>
        /* Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        h1, h2 {
            text-align: center;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            background-color: white;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }

        tr:hover {
            background-color: #f9f9f9;
        }

        select, button {
            padding: 8px;
            margin-top: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
            cursor: pointer;
        }

        button {
            background-color: #4CAF50;
            color: white;
        }

        button:hover {
            background-color: #45a049;
        }

        .no-orders {
            text-align: center;
            padding: 20px;
            background-color: #ffdddd;
            color: #d8000c;
            border: 1px solid #d8000c;
            border-radius: 5px;
        }

        .section-title {
            margin-top: 50px;
            font-size: 1.5em;
        }
    </style>
</head>
<body>
    <h1>Restoran Siparişleri</h1>
    <h2 class="section-title">Güncel Siparişler</h2>
    <table>
        <thead>
            <tr>
                <th>Sipariş ID</th>
                <th>Kullanıcı Adı</th>
                <th>Yemek Adı</th>
                <th>Adet</th>
                <th>Toplam Fiyat</th>
                <th>Not</th>
                <th>Durum</th>
                <th>Durumu Güncelle</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($current_orders)): ?>
                <?php foreach ($current_orders as $order): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order['id']); ?></td>
                        <td><?php echo htmlspecialchars($order['username']); ?></td>
                        <td><?php echo htmlspecialchars($order['name']); ?></td>
                        <td><?php echo htmlspecialchars($order['quantity']); ?></td>
                        <td><?php echo htmlspecialchars($order['total_price']); ?> TL</td>
                        <td><?php echo htmlspecialchars($order['note']); ?></td>
                        <td><?php echo htmlspecialchars($order['status']); ?></td>
                        <td>
                            <form action="update_order_status.php" method="post">
                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                <select name="new_status">
                                    <option value="Hazırlanıyor" <?php echo $order['status'] === 'Hazırlanıyor' ? 'selected' : ''; ?>>Hazırlanıyor</option>
                                    <option value="Yolda" <?php echo $order['status'] === 'Yolda' ? 'selected' : ''; ?>>Yolda</option>
                                    <option value="Teslim Edildi" <?php echo $order['status'] === 'Teslim Edildi' ? 'selected' : ''; ?>>Teslim Edildi</option>
                                </select>
                                <button type="submit">Güncelle</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8">
                        <div class="no-orders">Henüz güncel sipariş bulunmuyor.</div>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <h2 class="section-title">Geçmiş Siparişler</h2>
    <table>
        <thead>
            <tr>
                <th>Sipariş ID</th>
                <th>Kullanıcı Adı</th>
                <th>Yemek Adı</th>
                <th>Adet</th>
                <th>Toplam Fiyat</th>
                <th>Not</th>
                <th>Durum</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($past_orders)): ?>
                <?php foreach ($past_orders as $order): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($order['id']); ?></td>
                        <td><?php echo htmlspecialchars($order['username']); ?></td>
                        <td><?php echo htmlspecialchars($order['name']); ?></td>
                        <td><?php echo htmlspecialchars($order['quantity']); ?></td>
                        <td><?php echo htmlspecialchars($order['total_price']); ?> TL</td>
                        <td><?php echo htmlspecialchars($order['note']); ?></td>
                        <td><?php echo htmlspecialchars($order['status']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7">
                        <div class="no-orders">Henüz geçmiş sipariş bulunmuyor.</div>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
