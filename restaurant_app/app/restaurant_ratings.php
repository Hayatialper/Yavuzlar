<?php
session_start();
require 'db_connect.php';
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "Restoran ID'si bulunamadı!";
    exit;
}

$restaurant_id = $_GET['id'];
$restaurant_query = 'SELECT * FROM restaurants WHERE id = :restaurant_id';
$restaurant_stmt = $pdo->prepare($restaurant_query);
$restaurant_stmt->execute(['restaurant_id' => $restaurant_id]);
$restaurant = $restaurant_stmt->fetch(PDO::FETCH_ASSOC);

if (!$restaurant) {
    echo 'Restoran bulunamadı!';
    exit;
}



$rating_avg_query = 'SELECT AVG(rating) AS average_rating FROM ratings WHERE order_id IN 
                     (SELECT id FROM orders WHERE restaurant_id = :restaurant_id)';
$rating_avg_stmt = $pdo->prepare($rating_avg_query);
$rating_avg_stmt->execute(['restaurant_id' => $restaurant_id]);
$average_rating = $rating_avg_stmt->fetchColumn();
$sort_option = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
switch ($sort_option) {
    case 'positive':
        $order_by = 'rating DESC';
        break;
    case 'negative':
        $order_by = 'rating ASC';
        break;
    default:
        $order_by = 'created_at DESC';
}

$ratings_query = "SELECT ratings.*, users.username FROM ratings 
                  JOIN orders ON ratings.order_id = orders.id 
                  JOIN users ON ratings.user_id = users.id 
                  WHERE orders.restaurant_id = :restaurant_id ORDER BY $order_by";
$ratings_stmt = $pdo->prepare($ratings_query);
$ratings_stmt->execute(['restaurant_id' => $restaurant_id]);
$ratings = $ratings_stmt->fetchAll(PDO::FETCH_ASSOC);


if ($_SESSION['role'] === 'admin' && isset($_POST['delete_rating'])) {
    $rating_id = $_POST['rating_id'];
    $delete_query = 'DELETE FROM ratings WHERE id = :rating_id';
    $delete_stmt = $pdo->prepare($delete_query);
    $delete_stmt->execute(['rating_id' => $rating_id]);
    header("Location: restaurant_ratings.php?id=$restaurant_id");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($restaurant['name']); ?> Yorumları</title>
    <style>
        *{
            margin:0;
            padding:0;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
        }
        h1 {
            text-align: center;
            padding: 20px;
            background-color: #333;
            color: white;
        }
        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
        }
        .restaurant-info {
            text-align: center;
            margin-bottom: 30px;
        }
        .restaurant-info img {
            width: 100%;
            max-width: 600px;
            height: auto;
        }
        .average-rating {
            font-size: 1.5em;
            margin-top: 10px;
        }
        .sort-options {
            margin: 20px 0;
            text-align: center;
        }
        .sort-options a {
            margin: 0 10px;
            padding: 10px;
            background-color: #333;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .sort-options a:hover {
            background-color: #555;
        }
        .ratings-list {
            margin-top: 20px;
        }
        .rating-item {
            padding: 20px;
            background-color: white;
            border: 1px solid #ddd;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .rating-item h3 {
            margin: 0 0 10px;
        }
        .rating-item .rating {
            font-weight: bold;
            color: #ff9800;
        }
        .rating-item .username {
            color: #333;
            font-size: 0.9em;
        }
        .delete-btn {
            background-color: red;
            color: white;
            padding: 5px 10px;
            border: none;
            cursor: pointer;
            float: right;
        }
        .delete-btn:hover {
            background-color: darkred;
        }
    </style>
</head>
<body>
    <h1><?php echo htmlspecialchars($restaurant['name']); ?> Yorumları</h1>
    
    <div class="container">
        <div class="restaurant-info">
            <img src="<?php echo htmlspecialchars($restaurant['image_path']); ?>" alt="Restoran Resmi">
            <p class="average-rating">Ortalama Puan: <?php echo round($average_rating, 2); ?> / 10</p>
        </div>
        
        <div class="sort-options">
            <a href="?id=<?php echo $restaurant_id; ?>&sort=newest">En Yeni</a>
            <a href="?id=<?php echo $restaurant_id; ?>&sort=positive">Olumlu</a>
            <a href="?id=<?php echo $restaurant_id; ?>&sort=negative">Olumsuz</a>
        </div>
        
        <div class="ratings-list">
            <?php foreach ($ratings as $rating): ?>
                <div class="rating-item">
                    <h3><?php echo htmlspecialchars($rating['username']); ?> - <?php echo htmlspecialchars($rating['created_at']); ?></h3>
                    <p class="rating">Puan: <?php echo htmlspecialchars($rating['rating']); ?> / 10</p>
                    <p><?php echo htmlspecialchars($rating['comment']); ?></p>
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <form action="" method="POST">
                            <input type="hidden" name="rating_id" value="<?php echo $rating['id']; ?>">
                            <button type="submit" name="delete_rating" class="delete-btn">Yorumu Sil</button>
                            <br>
                            <br>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
