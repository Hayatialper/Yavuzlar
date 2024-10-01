<?php
session_start();

require 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'restaurant_user') {
    echo 'Bu sayfaya erişim izniniz yok.';
    exit;
}

$restaurant_query = 'SELECT * FROM restaurants WHERE id = :restaurant_id';
$stmt = $pdo->prepare($restaurant_query);
$stmt->execute(['restaurant_id' => $_SESSION['restaurant_id']]);
$restaurant = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$restaurant) {
    echo 'Restoran bulunamadı.';
    exit;
}
$meal_query = 'SELECT * FROM meals WHERE restaurant_id = :restaurant_id';
$meal_stmt = $pdo->prepare($meal_query);
$meal_stmt->execute(['restaurant_id' => $_SESSION['restaurant_id']]);
$meals = $meal_stmt->fetchAll(PDO::FETCH_ASSOC);
?>







<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restoran Paneli</title>
</head>
<body>
    <h1><?php echo htmlspecialchars($restaurant['name']); ?> - Restoran Yönetimi</h1>

    <h2>Yemekler</h2>
    <ul>
        <?php foreach ($meals as $meal): ?>
            <li>
                <?php echo htmlspecialchars($meal['name']); ?>
                <a href="edit_meal.php?id=<?php echo $meal['id']; ?>">Düzenle</a>
                <a href="delete_meal.php?id=<?php echo $meal['id']; ?>" onclick="return confirm('Bu yemeği silmek istediğinize emin misiniz?');">Sil</a>
            </li>
        <?php endforeach; ?>
    </ul>

    <h2>Yeni Yemek Ekle</h2>
    <form action="add_meal.php" method="post" enctype="multipart/form-data">
        <label for="name">Yemek Adı:</label><br>
        <input type="text" id="name" name="name" required><br><br>

        <label for="description">Açıklama:</label><br>
        <textarea id="description" name="description" required></textarea><br><br>

        <label for="price">Fiyat:</label><br>
        <input type="number" id="price" name="price" step="0.01" required><br><br>

        <label for="image">Yemek Resmi:</label><br>
        <input type="file" id="image" name="image" accept="image/*"><br><br>

        <button type="submit">Yemek Ekle</button>
    </form>
</body>
</html>
