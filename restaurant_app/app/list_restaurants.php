<?php
session_start();

require 'db_connect.php';

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'company' && $_SESSION['role'] !== 'admin')) { //burda hangi roller erişiyo onu kontrol ediyoruz.
    echo 'Bu sayfaya erişim izniniz yok.';
    exit;
}


if ($_SESSION['role'] === 'admin') {
    $query = 'SELECT * FROM restaurants WHERE deleted_at IS NULL';
    $stmt = $pdo->query($query);
    $restaurants = $stmt->fetchAll(PDO::FETCH_ASSOC);
} elseif ($_SESSION['role'] === 'company') {
    $company_id = $_SESSION['user_id'];
    $query = 'SELECT * FROM restaurants WHERE company_id = :company_id AND deleted_at IS NULL';
    $stmt = $pdo->prepare($query);
    $stmt->execute(['company_id' => $company_id]);
    $restaurants = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restoranlarınızı Düzenleyin</title>
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
            flex-wrap: wrap;
            justify-content: center;
            padding: 20px;
        }

        .restaurant-card {
            background-color: white;
            margin: 10px;
            padding: 20px;
            width: 300px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            cursor: pointer;
            transition: transform 0.3s ease;
        }

        .restaurant-card:hover {
            transform: scale(1.05);
        }

        .restaurant-card img {
            width: 100px;
            height: 100px;
            margin-bottom: 10px;
        }

        .restaurant-card h3 {
            margin: 10px 0;
        }

        .restaurant-card p {
            color: #555;
        }

        .back-button {
            margin-top: 20px;
            text-align: center;
        }

        .back-button a {
            text-decoration: none;
            color: #333;
            padding: 10px 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .back-button a:hover {
            background-color: #f0f0f0;
        }
    </style>
</head>
<body>
<div class="navbar">
    <div class="logo"><img src="header-logo.png" alt="Logo" width="50" height="50px">  Yavuzlar yemek</div>
    <div class="nav-right">
        <a href="profile.php">Profil</a>
        <a href="logout.php">Çıkış Yap</a>
    </div>
</div>
<div class="restaurant-container">
    <?php if (count($restaurants) > 0): ?>
        <?php foreach ($restaurants as $restaurant): ?>
            <div class="restaurant-card" onclick="window.location.href='edit_restaurant.php?id=<?php echo $restaurant['id']; ?>'">
                <img src="<?php echo $restaurant['image_path']; ?>" alt="Restoran Resmi">
                <h3><?php echo htmlspecialchars($restaurant['name']); ?></h3>
                <p><?php echo htmlspecialchars($restaurant['description']); ?></p>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Henüz eklenmiş restoran bulunmuyor.</p>
    <?php endif; ?>
</div>


<div class="back-button">
    <a href="company_panel.php">Geri Dön</a>
</div>

</body>
</html>
