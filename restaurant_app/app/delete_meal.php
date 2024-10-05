<?php
session_start();

require 'db_connect.php';


if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'company')) {
    echo "Bu sayfaya erişim izniniz yok.";
    exit;
}


if (isset($_GET['id'])) {
    $meal_id = $_GET['id'];

    $restaurant_query = 'SELECT restaurant_id FROM meals WHERE id = :meal_id';
    $restaurant_stmt = $pdo->prepare($restaurant_query);
    $restaurant_stmt->execute(['meal_id' => $meal_id]);
    $restaurant = $restaurant_stmt->fetch(PDO::FETCH_ASSOC);

    if ($restaurant) {
        $restaurant_id = $restaurant['restaurant_id'];
        $query = 'DELETE FROM meals WHERE id = :meal_id';
        $stmt = $pdo->prepare($query);
        $stmt->execute(['meal_id' => $meal_id]);

        echo "Yemek başarıyla silindi!";
    } else {
        echo "Yemek bulunamadı!";
        exit;
    }
} else {
    echo "Yemek ID'si belirtilmedi!";
    exit;
}
?>











<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yemek Silindi</title>
</head>
<body>
    <h2>Yemek başarıyla silindi!</h2>
    <a href="restaurant.php?id=<?php echo $restaurant_id; ?>">Restorana Dön</a>
</body>
</html>
