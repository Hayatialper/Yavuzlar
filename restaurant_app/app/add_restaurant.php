<?php
session_start();

require 'db_connect.php';
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'company' && $_SESSION['role'] !== 'admin') {
    echo 'Bu sayfaya erişim izniniz yok.';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); 
    $target_dir = "uploads/";
    $image_path = $target_dir . basename($_FILES["image"]["name"]);
    move_uploaded_file($_FILES["image"]["tmp_name"], $image_path);


    $query = 'INSERT INTO restaurants (company_id, name, description, image_path) VALUES (:company_id, :name, :description, :image_path)';
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'company_id' => $_SESSION['user_id'],
        'name' => $name,
        'description' => $description,
        'image_path' => $image_path,
    ]);


    $restaurant_user_query = 'INSERT INTO users (username, email, password, role, restaurant_id) VALUES (:username, :email, :password, "restaurant_user", :restaurant_id)';
    $restaurant_user_stmt = $pdo->prepare($restaurant_user_query);
    $restaurant_user_stmt->execute([
        'username' => $username,
        'email' => $email,
        'password' => $password,
        'restaurant_id' => $pdo->lastInsertId()
    ]);
    header('Location: company_panel.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Yeni Restoran Ekle</title>
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


        .form-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }

        .form-container label,
        .form-container input,
        .form-container textarea {
            display: block;
            width: 100%;
            margin-bottom: 10px;
        }

        .form-container input,
        .form-container textarea {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .form-container button {
            width: 100%;
            padding: 10px;
            background-color: red;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .form-container button:hover {
            background-color: darkred;
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


<div class="form-container">
    <h1>Yeni Restoran Ekle</h1>
    <form action="add_restaurant.php" method="post" enctype="multipart/form-data">
        <label for="name">Restoran Adı:</label>
        <input type="text" id="name" name="name" required>

        <label for="description">Açıklama:</label>
        <textarea id="description" name="description" required></textarea>

        <label for="image">Restoran Resmi:</label>
        <input type="file" id="image" name="image" accept="image/*">

        <h2>Restoran Kullanıcısı Oluştur</h2>
        <label for="username">Kullanıcı Adı:</label>
        <input type="text" id="username" name="username" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required> 

        <label for="password">Şifre:</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Restoran Ekle</button>
    </form>


    <div class="back-button">
        <a href="company_panel.php">Geri Dön</a>
    </div
