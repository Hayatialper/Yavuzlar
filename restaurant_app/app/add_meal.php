<?php
session_start();
if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'company' && $_SESSION['role'] !== 'restaurant_user')) {
    echo "Bu sayfaya erişim izniniz yok.";
    exit;
}

require 'db_connect.php'; 


$restaurant_id = $_GET['restaurant_id'];


if ($_SESSION['role'] === 'company') {
    $company_id = $_SESSION['user_id'];


    $check_query = 'SELECT * FROM restaurants WHERE id = :restaurant_id AND company_id = :company_id';
    $check_asd = $pdo->prepare($check_query);
    $check_asd->execute(['restaurant_id' => $restaurant_id, 'company_id' => $company_id]);

    $restaurant = $check_asd->fetch(PDO::FETCH_ASSOC);

    if (!$restaurant) {
        echo "Bu restorana erişim izniniz yok.";
        exit;
    }
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];


    $target_dir = "uploads/";
    $image_path = $target_dir . basename($_FILES["image"]["name"]);

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $image_path)) {

        $query = 'INSERT INTO meals (restaurant_id, name, description, price, image_path) VALUES (:restaurant_id, :name, :description, :price, :image_path)';
        $asd = $pdo->prepare($query);
        $asd->execute([
            'restaurant_id' => $restaurant_id,
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'image_path' => $image_path,
        ]);

        echo "Yemek başarıyla eklendi!";
        header('Location: add_meal.php?restaurant_id=' . $restaurant_id);
        exit;
    } else {
        echo "Dosya yükleme başarısız!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yemek Ekle</title>
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
        <h1>Yeni Yemek Ekle</h1>
        <form action="add_meal.php?restaurant_id=<?php echo $restaurant_id; ?>" method="post" enctype="multipart/form-data">
            <label for="name">Yemek Adı:</label>
            <input type="text" id="name" name="name" required>

            <label for="description">Açıklama:</label>
            <textarea id="description" name="description" required></textarea>

            <label for="price">Fiyat:</label>
            <input type="number" id="price" name="price" step="0.01" required>

            <label for="image">Resim Yükle:</label>
            <input type="file" id="image" name="image" accept="image/*" required>
            
            <button type="submit">Yemek Ekle</button>
        </form>
        <br>
        <br>
        <a href="restaurant_panel.php" class="back-btn">Geri Dön</a>
    </div>
</div>

</body>
</html>
