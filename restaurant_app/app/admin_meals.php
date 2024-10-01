<?php
session_start();


error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'db_connect.php';


if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'company')) {
    echo "Bu sayfaya erişim izniniz yok.";
    exit;
}

$restaurant_id = $_GET['restaurant_id'];


if ($_SESSION['role'] === 'company') {
    $company_id = $_SESSION['user_id'];


    $check_query = 'SELECT * FROM restaurants WHERE id = :restaurant_id AND company_id = :company_id';
    $check_stmt = $pdo->prepare($check_query);
    $check_stmt->execute(['restaurant_id' => $restaurant_id, 'company_id' => $company_id]);

    $restaurant = $check_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$restaurant) {
        echo "Bu restorana erişim izniniz yok.";
        exit;
    }
}


$meal_query = 'SELECT * FROM meals WHERE restaurant_id = :restaurant_id';
$meal_stmt = $pdo->prepare($meal_query);
$meal_stmt->execute(['restaurant_id' => $restaurant_id]);
$meals = $meal_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yemekleri Yönet</title>
    <style>

        .meal-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .meal-item {
            border: 1px solid #ddd;
            padding: 10px;
            width: 200px;
            text-align: center;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        .meal-item img {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }

        .meal-item a {
            margin-top: 10px;
            display: inline-block;
            color: blue;
            text-decoration: none;
        }

        .meal-item a:hover {
            text-decoration: underline;
        }

        .meal-price {
            font-weight: bold;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <h1>Yemekleri Yönet</h1>

    <a href="add_meal.php?restaurant_id=<?php echo $restaurant_id; ?>">Yeni Yemek Ekle</a>

    <div class="meal-container">
        <?php foreach ($meals as $meal): ?>
            <div class="meal-item">
                <?php if (!empty($meal['image_path'])): ?>
                    <img src="<?php echo $meal['image_path']; ?>" alt="<?php echo htmlspecialchars($meal['name']); ?>">
                <?php endif; ?>
                <h3><?php echo htmlspecialchars($meal['name']); ?></h3>
                <p><?php echo htmlspecialchars($meal['description']); ?></p>
                <p class="meal-price">Fiyat: <?php echo htmlspecialchars($meal['price']); ?> TL</p>
                <a href="edit_meal.php?id=<?php echo $meal['id']; ?>">Düzenle</a>
                <a href="delete_meal.php?id=<?php echo $meal['id']; ?>" onclick="return confirm('Bu yemeği silmek istediğinize emin misiniz?');">Sil</a>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
