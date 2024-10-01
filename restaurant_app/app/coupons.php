<?php
session_start();


if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'company', 'restaurant_user'])) {
    echo 'Bu sayfaya erişim izniniz yok.';
    exit;
}

require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_coupon'])) {
    $code = $_POST['code'];
    $discount_percentage = $_POST['discount_percentage'];
    $valid_until = $_POST['valid_until'];


    if ($_SESSION['role'] === 'admin') {
        $restaurant_id = $_POST['restaurant_id'];
    } elseif ($_SESSION['role'] === 'company' || $_SESSION['role'] === 'restaurant_user') {

        $restaurant_id = $_SESSION['restaurant_id'];
    }

    $query = 'INSERT INTO coupons (code, discount_percentage, valid_until, restaurant_id) 
              VALUES (:code, :discount_percentage, :valid_until, :restaurant_id)';
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'code' => $code,
        'discount_percentage' => $discount_percentage,
        'valid_until' => $valid_until,
        'restaurant_id' => $restaurant_id
    ]);
}


if (isset($_GET['delete_coupon'])) {
    $coupon_id = $_GET['delete_coupon'];
    

    $delete_query = 'DELETE FROM coupons WHERE id = :id';
    $stmt = $pdo->prepare($delete_query);
    $stmt->execute(['id' => $coupon_id]);
}


if ($_SESSION['role'] === 'admin') {
    $query = 'SELECT coupons.*, restaurants.name AS restaurant_name 
              FROM coupons 
              JOIN restaurants ON coupons.restaurant_id = restaurants.id';
    $stmt = $pdo->query($query);
    $coupons = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $query = 'SELECT coupons.*, restaurants.name AS restaurant_name 
              FROM coupons 
              JOIN restaurants ON coupons.restaurant_id = restaurants.id 
              WHERE restaurants.company_id = :company_id OR restaurants.id = :restaurant_id';
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'company_id' => $_SESSION['user_id'], 
        'restaurant_id' => $_SESSION['restaurant_id'] 
    ]);
    $coupons = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kupon Yönetimi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            background-color: #333;
            padding: 10px 20px;
            color: white;
        }

        .navbar .logo {
            font-size: 1.5em;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            padding: 8px 16px;
        }

        .navbar a:hover {
            background-color: #575757;
        }

        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: white;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }

        .form-container {
            margin-bottom: 30px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 10px;
            box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.1);
        }

        .form-container label,
        .form-container input,
        .form-container select {
            display: block;
            width: 100%;
            margin-bottom: 10px;
            padding: 10px;
        }

        .form-container button {
            width: 100%;
            padding: 10px;
            background-color: green;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .form-container button:hover {
            background-color: darkgreen;
        }

        .delete-btn {
            color: red;
            cursor: pointer;
        }
    </style>
</head>
<body>

    <div class="navbar">
        <div class="logo">Kupon Yönetimi</div>
        <div class="nav-right">
            <a href="profile.php">Profil</a>
            <a href="logout.php">Çıkış Yap</a>
        </div>
    </div>

    <div class="container">
        <h1>Kupon Ekle</h1>
        <div class="form-container">
            <form action="coupons.php" method="post">
                <label for="code">Kupon Kodu:</label>
                <input type="text" id="code" name="code" required>

                <label for="discount_percentage">İndirim Oranı (%):</label>
                <input type="number" id="discount_percentage" name="discount_percentage" min="1" max="100" required>

                <label for="valid_until">Geçerlilik Tarihi:</label>
                <input type="date" id="valid_until" name="valid_until" required>

                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <label for="restaurant_id">Restoran Seç:</label>
                    <select id="restaurant_id" name="restaurant_id" required>
                        <?php
                        $restaurants_query = 'SELECT * FROM restaurants';
                        $restaurants_stmt = $pdo->query($restaurants_query);
                        $restaurants = $restaurants_stmt->fetchAll(PDO::FETCH_ASSOC);

                        foreach ($restaurants as $restaurant) {
                            echo '<option value="' . $restaurant['id'] . '">' . htmlspecialchars($restaurant['name']) . '</option>';
                        }
                        ?>
                    </select>
                <?php endif; ?>

                <button type="submit" name="add_coupon">Kupon Ekle</button>
            </form>
        </div>


        <h1>Mevcut Kuponlar</h1>
        <table>
            <thead>
                <tr>
                    <th>Kupon Kodu</th>
                    <th>İndirim Oranı</th>
                    <th>Geçerlilik Tarihi</th>
                    <th>Restoran</th>
                    <th>Sil</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($coupons as $coupon): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($coupon['code']); ?></td>
                        <td><?php echo htmlspecialchars($coupon['discount_percentage']); ?>%</td>
                        <td><?php echo htmlspecialchars($coupon['valid_until']); ?></td>
                        <td><?php echo htmlspecialchars($coupon['restaurant_name']); ?></td>
                        <td><a href="coupons.php?delete_coupon=<?php echo $coupon['id']; ?>" class="delete-btn">Sil</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
