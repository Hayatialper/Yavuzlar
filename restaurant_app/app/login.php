<?php
session_start();

require 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
        $query = 'SELECT * FROM users WHERE username = :username';
        $stmt = $pdo->prepare($query);
        $stmt->execute(['username' => $username]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {

            $_SESSION['user_id'] = $user['id']; 
            $_SESSION['username'] = $user['username']; 
            $_SESSION['role'] = $user['role']; 
            $_SESSION['balance'] = $user['balance'];
            if ($user['role'] === 'restaurant_user') {
                $restaurant_query = 'SELECT restaurant_id FROM users WHERE id = :user_id';
                $restaurant_stmt = $pdo->prepare($restaurant_query);
                $restaurant_stmt->execute(['user_id' => $user['id']]);
                $restaurant = $restaurant_stmt->fetch(PDO::FETCH_ASSOC);

                if ($restaurant) {
                    $_SESSION['restaurant_id'] = $restaurant['restaurant_id'];
                }
            }
            if ($user['role'] === 'admin') {
                header('Location: index.php');
            } elseif ($user['role'] === 'company') {
                header('Location: index.php');
            } elseif ($user['role'] === 'restaurant_user') {
                header('Location: restaurant_orders.php');
            } else {
                header('Location: index.php');
            }
            exit;
        } else {
            $error = 'Geçersiz kullanıcı adı veya şifre!';
        }
    } catch (Exception $e) {
        $error = 'Bir hata oluştu: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Sayfası</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .form-container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
        }

        .form-container input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
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

        .navbar {
            display: flex;
            justify-content: space-between;
            background-color: #333;
            padding: 10px 20px;
            color: white;
            position: absolute;
            top: 0;
            width: 100%;
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


<div class="form-container">
    <h2>Oturum Aç</h2>

    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form action="login.php" method="post">
        <label for="username">Kullanıcı Adı:</label>
        <input type="text" id="username" name="username" required>

        <label for="password">Şifre:</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Giriş Yap</button>
    </form>
</div>

</body>
</html>
