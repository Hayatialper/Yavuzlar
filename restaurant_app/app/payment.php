<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $amount = $_POST['amount'];

    $query = 'UPDATE users SET balance = balance + :amount WHERE id = :id';
    $stmt = $pdo->prepare($query);
    $stmt->execute([
        'amount' => $amount,
        'id' => $_SESSION['user_id']
    ]);

    $new_balance_query = 'SELECT balance FROM users WHERE id = :id';
    $new_balance_stmt = $pdo->prepare($new_balance_query);
    $new_balance_stmt->execute(['id' => $_SESSION['user_id']]);
    $new_balance = $new_balance_stmt->fetchColumn();

    $_SESSION['balance'] = $new_balance;

    header('Location: profile.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bakiye Yükle</title>
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
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
            margin: 50px auto;
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

        .form-container .back-btn {
            background-color: #333;
            margin-top: 10px;
        }

        .form-container .back-btn:hover {
            background-color: #575757;
        }
    </style>
</head>
<body>

<div class="navbar">
    <div class="logo">Site İsmi</div>
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
    <h1>Bakiye Yükle</h1>
    <form action="payment.php" method="post">
        <label for="amount">Yüklenecek Miktar (TL):</label>
        <input type="number" id="amount" name="amount" required>
        <button type="submit">Yükle</button>
    </form>
    <a href="profile.php">
        <button class="back-btn">GeriDön</button>
    </a>
</div>

</body>
</html>
