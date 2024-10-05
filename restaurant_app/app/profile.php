<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require 'db_connect.php';
$query = 'SELECT * FROM users WHERE id = :id';
$stmt = $pdo->prepare($query);
$stmt->execute(['id' => $_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil</title>
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


        .profile-container {
            background-color: white;
            padding: 20px;
            margin: 50px auto;
            width: 60%;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .profile-container h1 {
            margin-bottom: 20px;
        }

        .profile-container p {
            font-size: 18px;
            margin-bottom: 10px;
        }

        .profile-container a {
            text-decoration: none;
            background-color: red;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            display: inline-block;
            margin-top: 20px;
        }

        .profile-container a:hover {
            background-color: darkred;
        }

        .back-btn {
            background-color: gray;
            color: white;
            padding: 10px 20px;
            margin-top: 20px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
        }

        .back-btn:hover {
            background-color: darkgray;
        }
    </style>
</head>
<body>

<div class="navbar">
    <div class="logo"><img src="header-logo.png" alt="Logo" width="50" height="50px">  Yavuzlar yemek</div>
    <div class="nav-right">
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="profile.php">Profil</a>
            <a href="logout.php">Çıkış Yap</a>
        <?php else: ?>
            <a href="login.php">Giriş Yap</a>
            <a href="kayitol.php">Kayıt Ol</a>
        <?php endif; ?>
    </div>
</div>
<div class="profile-container">
    <h1>Profil</h1>
    <p>Kullanıcı Adı: <?php echo htmlspecialchars($user['username']); ?></p>
    <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
    <p>Bakiye: <?php echo htmlspecialchars($user['balance']); ?> TL</p>
    <a href="payment.php">Bakiye Yükle</a>

    <a href="index.php" class="back-btn">Geri Dön</a>
</div>

</body>
</html>
