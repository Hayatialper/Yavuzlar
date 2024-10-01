<?php
session_start();


if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'company'])) {

    header('Location: index.php');
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


        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 80vh;
        }

        .box {
            width: 200px;
            height: 200px;
            background-color: white;
            margin: 0 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            border: 2px solid #ddd;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .box:hover {
            transform: scale(1.05);
            box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.2);
        }

        .box a {
            text-decoration: none;
            color: black;
            font-size: 1.2em;
            font-weight: bold;
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
    <div class="box">
        <a href="add_restaurant.php">Restoran Ekle!</a>
    </div>
    <div class="box">
        <a href="list_restaurants.php">Restoranları Düzenle!</a>
    </div>
</div>


</body>
</html>
