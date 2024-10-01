<?php
session_start();

require 'db_connect.php';


if (!isset($_SESSION['role'])) {
    echo "Bu sayfaya erişim izniniz yok.";
    exit;
}

$id = $_GET['id']; 


if ($_SESSION['role'] === 'admin') {
    $query = 'SELECT * FROM restaurants WHERE id = :id';
    $stmt = $pdo->prepare($query);
    $stmt->execute(['id' => $id]);
} elseif ($_SESSION['role'] === 'company') {
    $query = 'SELECT * FROM restaurants WHERE id = :id AND company_id = :company_id';
    $stmt = $pdo->prepare($query);
    $stmt->execute(['id' => $id, 'company_id' => $_SESSION['user_id']]);
}

$restaurant = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$restaurant) {
    echo "Bu restorana erişim izniniz yok.";
    exit;
}




if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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
            'id' => $id,
        ]);
    } else {
        $query = 'UPDATE restaurants SET name = :name, description = :description WHERE id = :id';
        $stmt = $pdo->prepare($query);
        $stmt->execute([
            'name' => $name,
            'description' => $description,
            'id' => $id,
        ]);
    }

    if ($_SESSION['role'] === 'admin') {
        header('Location: admin_panel.php');
    } else {
        header('Location: company_panel.php');
    }
    exit;
}

$meal_query = 'SELECT * FROM meals WHERE restaurant_id = :restaurant_id';
$meal_stmt = $pdo->prepare($meal_query);
$meal_stmt->execute(['restaurant_id' => $id]);
$meals = $meal_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restoran Düzenle</title>
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
            margin-bottom: 20px;
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

        .meal-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
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
            display: block;
            margin-top: 10px;
            color: blue;
            text-decoration: none;
        }

        .meal-item a:hover {
            text-decoration: underline;
        }

        .add-meal {
            border: 1px solid #ddd;
            padding: 20px;
            text-align: center;
            width: 200px;
            background-color: lightgreen;
            cursor: pointer;
        }

        .add-meal:hover {
            background-color: green;
            color: white;
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
        <h1>Restoran Düzenle</h1>
        <form action="edit_restaurant.php?id=<?php echo $restaurant['id']; ?>" method="post" enctype="multipart/form-data">
            <label for="name">Restoran Adı:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($restaurant['name']); ?>" required>

            <label for="description">Açıklama:</label>
            <textarea id="description" name="description" required><?php echo htmlspecialchars($restaurant['description']); ?></textarea>

            <label for="image">Resim Yükle (Değiştirmek istemiyorsan boş bırakın):</label>
            <input type="file" id="image" name="image" accept="image/*">

            <button type="submit">Güncelle</button>
        </form>
    </div>


    <h2>Yemekler</h2>
    <div class="meal-container">
        <?php if (!empty($meals)): ?>
            <?php foreach ($meals as $meal): ?>
                <div class="meal-item">
                    <img src="<?php echo $meal['image_path']; ?>" alt="Yemek Resmi">
                    <h3><?php echo htmlspecialchars($meal['name']); ?></h3>
                    <p><?php echo htmlspecialchars($meal['description']); ?></p>
                    <p><?php echo htmlspecialchars($meal['price']); ?> TL</p>
                    <a href="edit_meal.php?id=<?php echo $meal['id']; ?>">Düzenle</a>
                    <a href="delete_meal.php?id=<?php echo $meal['id']; ?>" style="color: red;">Sil</a>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
        

        <div class="add-meal">
            <a href="add_meal.php?restaurant_id=<?php echo $restaurant['id']; ?>">Yemek Ekle!</a>
        </div>
    </div>


    <a href="company_panel.php" class="back-btn">Geri Dön </a>
</div>

</body>
</html>
