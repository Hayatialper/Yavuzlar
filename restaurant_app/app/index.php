<?php
session_start();

require 'db_connect.php';


$query = 'SELECT * FROM restaurants WHERE deleted_at IS NULL';
$stmt = $pdo->query($query);
$restaurants = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yavuzlar Yemek</title>


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
            margin-left: 10px;
        }

        .navbar a:hover {
            background-color: #575757;
        }

        .navbar .nav-right {
            display: flex;
        }


        .restaurant-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }

        .restaurant {
            background-color: white;
            margin: 10px 0;
            padding: 20px;
            width: 60%;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .restaurant img {
            width: 100px; 
            height: 100px;
            margin-bottom: 10px;
        }

        .restaurant h3 {
            margin: 10px 0;
        }

        .restaurant a {
            text-decoration: none;
            color: black;
        }

        .restaurant a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>







<div class="navbar">
    
<div class="logo"><img src="header-logo.png" alt="Logo" width="50" height="50px">  Yavuzlar yemek</div>
    <div class="nav-right">
        <?php if (isset($_SESSION['user_id'])): ?>
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="users.php">Admin Kullanıcı Paneli</a>
                <a href="companies.php">Admin Şirket Paneli</a>
                <a href="restaurants.php">Admin Restoran Paneli</a>
                <a href="company_panel.php">Admin Restoran Yönetim paneli</a>
                <a href="coupons.php">Kupon paneli</a>
            <?php elseif ($_SESSION['role'] === 'company'): ?>
                <a href="company_panel.php">Şirket Paneli</a>
                <a href="coupons.php">Kupon paneli</a>
            <?php elseif ($_SESSION['role'] === 'restaurant_user'): ?>
                <a href="restaurant_orders.php">Gelen Siparişler</a>
                <a href="restaurant_panel.php">Restoran Paneli</a>
                <a href="coupons.php">Kupon paneli</a>
            <?php else: ?>
                <a href="profile.php">Profil</a>
                <a href="order_status.php">Siparişler</a>
                
            <?php endif; ?>
            <a href="logout.php">Çıkış Yap</a>
        <?php else: ?>
            <a href="login.php">Giriş Yap</a>
            <a href="kayitol.php">Kayıt Ol</a>
        <?php endif; ?>
    </div>
</div>

<div class="restaurant-container">
    <?php foreach ($restaurants as $restaurant): ?>
        <div class="restaurant">
            <a href="restaurant.php?id=<?php echo $restaurant['id']; ?>">
                <img src="<?php echo $restaurant['image_path']; ?>" alt="Restoran Resmi">
                <h3><?php echo htmlspecialchars($restaurant['name']); ?></h3>
            </a>
            <p><?php echo htmlspecialchars($restaurant['description']); ?></p>
        </div>
    <?php endforeach; ?>
</div>

</body>
</html>






<?php

if (isset($_SESSION['user_id'])) {
    $query = 'SELECT COUNT(*) FROM orders WHERE user_id = :user_id';
    $stmt = $pdo->prepare($query);
    $stmt->execute(['user_id' => $_SESSION['user_id']]);
    $orderCount = $stmt->fetchColumn();
}
?>
