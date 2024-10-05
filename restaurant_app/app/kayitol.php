<?php
session_start();


require 'db_connect.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST['username']; 
    $email = $_POST['email']; 
    $password = password_hash($_POST['password'], PASSWORD_ARGON2ID); 
    $role = $_POST['role'];

    try {
        $query = 'INSERT INTO users (username, email, password, role) VALUES (:username, :email, :password, :role)'; 
        $stmt = $pdo->prepare($query); 

        $stmt->execute([
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'role' => $role,
        ]);

        $user_id = $pdo->lastInsertId();
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $role;
        $_SESSION['balance'] = 0; 


        header('Location: index.php');
        exit;
    } catch (Exception $e) {
        echo 'Bir hata oluştu: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kayıt Formu</title>
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

        .form-container input, .form-container select {
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
    <form action="kayitol.php" method="post">
        <label for="username">Kullanıcı adı:</label>
        <input type="text" id="username" name="username" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <label for="password">Şifre:</label>
        <input type="password" id="password" name="password" required>

        <label for="role">Role:</label>
        <select name="role" id="role">
            <option value="user">User</option>
            <option value="admin">Admin</option>
            <option value="company">Company</option>
        </select>

        <button type="submit">Kaydet</button>
    </form>
</div>


</body>
</html>
