<?php
session_start();
require 'db_connect.php';


if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'company' && $_SESSION['role'] !== 'restaurant_user')) {
    echo "Bu sayfaya erişim izniniz yok.";
    exit;
}


if ($_SESSION['role'] === 'company') {
    $company_id = $_SESSION['user_id'];
    $query = 'SELECT * FROM restaurants WHERE company_id = :company_id';
    $stmt = $pdo->prepare($query);
    $stmt->execute(['company_id' => $company_id]);
    $restaurant = $stmt->fetch(PDO::FETCH_ASSOC);
} else {

    if (!isset($_SESSION['restaurant_id'])) {
        echo "Restoran ID bulunamadı.";
        exit;
    }
    $restaurant_id = $_SESSION['restaurant_id'];
    $query = 'SELECT * FROM restaurants WHERE id = :restaurant_id';
    $stmt = $pdo->prepare($query);
    $stmt->execute(['restaurant_id' => $restaurant_id]);
    $restaurant = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (!$restaurant) {
    echo "Restoran bulunamadı!";
    exit;
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_restaurant'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];

    if (!empty($_FILES["image"]["name"])) {
        $target_dir = "uploads/";
        $image_path = $target_dir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $image_path);

        $query = 'UPDATE restaurants SET name = :name, description = :description, image_path = :image_path WHERE id = :id';
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            'name' => $name,
            'description' => $description,
            'image_path' => $image_path,
            'id' => $restaurant['id'],
        ]);
    } else {
        $query = 'UPDATE restaurants SET name = :name, description = :description WHERE id = :id';
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            'name' => $name,
            'description' => $description,
            'id' => $restaurant['id'],
        ]);
    }

    echo "Restoran başarıyla güncellendi!";
}





$meal_query = 'SELECT * FROM meals WHERE restaurant_id = :restaurant_id';
$meal_stmt = $pdo->prepare($meal_query);
$meal_stmt->execute(['restaurant_id' => $restaurant['id']]);
$meals = $meal_stmt->fetchAll(PDO::FETCH_ASSOC);







if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_meal'])) {
    $meal_id = $_POST['meal_id'];
    $delete_query = 'DELETE FROM meals WHERE id = :meal_id';
    $delete_stmt = $pdo->prepare($delete_query);
    $delete_stmt->execute(['meal_id' => $meal_id]);

    echo "Yemek başarıyla silindi!";
    header('Location: restaurant_panel.php');
    exit;
}
?>








<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restoran Paneli</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
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
            margin-top: 20px;
            text-align: center;
        }

        .restaurant-form {
            margin-bottom: 30px;
            padding: 20px;
            background-color: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .restaurant-form input, .restaurant-form textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .restaurant-form button {
            padding: 10px;
            background-color: red;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .restaurant-form button:hover {
            background-color: darkred;
        }

        .meals-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }

        .meal-item {
            background-color: white;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 250px;
            text-align: center;
        }

        .meal-item img {
            width: 100px;
            height: 100px;
            object-fit: cover;
        }

        .meal-item button {
            margin-top: 10px;
            padding: 8px;
            background-color: red;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .meal-item button:hover {
            background-color: darkred;
        }

        .meal-item .edit-button {
            background-color: red;
            margin-top: 5px;
        }

        .meal-item .edit-button:hover {
            background-color: white;
        }
    </style>
</head>
<body>




<div class="navbar">
    <div class="logo">Restoran Paneli</div>
    <a href="logout.php">Çıkış Yap</a>
</div>




<div class="container">
    <div class="restaurant-form">
        <h2>Restoran Bilgilerini Güncelle</h2>
        <form action="restaurant_panel.php" method="post" enctype="multipart/form-data">
            <input type="text" name="name" value="<?php echo htmlspecialchars($restaurant['name']); ?>" placeholder="Restoran Adı" required>
            <textarea name="description" placeholder="Restoran Açıklaması" required><?php echo htmlspecialchars($restaurant['description']); ?></textarea>
            <input type="file" name="image" accept="image/*">
            <button type="submit" name="update_restaurant">Güncelle</button>
        </form>
    </div>
    <h2>Yemekler</h2>
    <div class="meals-container">
        <?php foreach ($meals as $meal): ?>
            <div class="meal-item">
                <img src="<?php echo $meal['image_path']; ?>" alt="Yemek Resmi">
                <h3><?php echo htmlspecialchars($meal['name']); ?></h3>
                <p><?php echo htmlspecialchars($meal['description']); ?></p>
                <p><?php echo htmlspecialchars($meal['price']); ?> TL</p>
                <form action="restaurant_panel.php" method="post">
                    <input type="hidden" name="meal_id" value="<?php echo $meal['id']; ?>">
                    <button type="submit" name="delete_meal">Sil</button>
                    <a href="edit_meal.php?id=<?php echo $meal['id']; ?>" class="edit-button">Düzenle</a>
                </form>
            </div>
        <?php endforeach; ?>

        <div class="meal-item">
            <a href="add_meal.php?restaurant_id=<?php echo $restaurant['id']; ?>" style="text-decoration: none;">
                <button>Yemek Ekle</button>
            </a>
        </div>
    </div>
</div>

</body>
</html>
