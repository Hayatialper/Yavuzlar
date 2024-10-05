<?php
ob_start();
session_start();



if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'company' && $_SESSION['role'] !== 'restaurant_user')) {
    echo "Bu sayfaya erişim izniniz yok.";
    exit;
}

require 'db_connect.php';


if (isset($_GET['id'])) {
    $meal_id = $_GET['id'];


    $query = 'SELECT * FROM meals WHERE id = :meal_id';
    $stmt = $pdo->prepare($query);
    $stmt->execute(['meal_id' => $meal_id]);
    $meal = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$meal) {
        echo "Yemek bulunamadı!";
        exit;
    }


    if ($_SESSION['role'] === 'company' || $_SESSION['role'] === 'restaurant_user') {
        $restaurant_query = 'SELECT * FROM restaurants WHERE id = :restaurant_id AND (company_id = :user_id OR :restaurant_id IN (SELECT restaurant_id FROM users WHERE id = :user_id))';
        $restaurant_stmt = $pdo->prepare($restaurant_query);
        $restaurant_stmt->execute([
            'restaurant_id' => $meal['restaurant_id'],
            'user_id' => $_SESSION['user_id']
        ]);

        $restaurant = $restaurant_stmt->fetch(PDO::FETCH_ASSOC);
        if (!$restaurant) {
            echo "Bu yemeği düzenleme izniniz yok.";
            exit;
        }
    }


    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = $_POST['name'];
        $description = $_POST['description'];
        $price = $_POST['price'];


        if (!empty($_FILES["image"]["name"])) {
            $target_dir = "uploads/";
            $image_path = $target_dir . basename($_FILES["image"]["name"]);
            move_uploaded_file($_FILES["image"]["tmp_name"], $image_path);

            $query = 'UPDATE meals SET name = :name, description = :description, price = :price, image_path = :image_path WHERE id = :meal_id';
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                'name' => $name,
                'description' => $description,
                'price' => $price,
                'image_path' => $image_path,
                'meal_id' => $meal_id
            ]);
        } else {
            $query = 'UPDATE meals SET name = :name, description = :description, price = :price WHERE id = :meal_id';
            $stmt = $pdo->prepare($query);
            $stmt->execute([
                'name' => $name,
                'description' => $description,
                'price' => $price,
                'meal_id' => $meal_id
            ]);
        }

        echo "Yemek başarıyla güncellendi!";
        header('Location: restaurant_panel.php');
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
    <title>Yemek Düzenle</title>
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

        .navbar .nav-right {
            display: flex;
        }

        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }

        .form-container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            width: 60%;
            text-align: center;
        }

        .form-container input, .form-container textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .form-container button {
            padding: 10px 20px;
            background-color: red;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .form-container button:hover {
            background-color: darkred;
        }

        .back-btn {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: lightgray;
            color: black;
            border: none;
            border-radius: 5px;
            text-align: center;
            cursor: pointer;
            text-decoration: none;
        }

        .back-btn:hover {
            background-color: gray;
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






<div class="container">
    <div class="form-container">
        <h1>Yemek Düzenle</h1>
        <form action="edit_meal.php?id=<?php echo $meal['id']; ?>" method="post" enctype="multipart/form-data">
            <label for="name">Yemek Adı:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($meal['name']); ?>" required>

            <label for="description">Açıklama:</label>
            <textarea id="description" name="description" required><?php echo htmlspecialchars($meal['description']); ?></textarea>

            <label for="price">Fiyat:</label>
            <input type="number" id="price" name="price" value="<?php echo htmlspecialchars($meal['price']); ?>" step="0.01" required>

            <label for="image">Resim Yükle (Değiştirmek istemiyorsanız boş bırakın):</label>
            <input type="file" id="image" name="image" accept="image/*">

            <button type="submit">Güncelle</button>
        </form>
        <br>
        <br>
        <a href="index.php" class="back-btn">Geri Dön</a>
    </div>
</div>

</body>
</html>
