<?php
// Ortam değişkenlerini al
$host = getenv('DB_HOST') ?: 'localhost'; // Eğer ortam değişkeni yoksa localhost olarak kullan
$db = getenv('DB_NAME') ?: 'deneme'; // Varsayılan veritabanı adı
$username = getenv('DB_USER') ?: 'root'; // Varsayılan kullanıcı adı
$password = getenv('DB_PASS') ?: ''; // Varsayılan şifre

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
];

$dsn = "mysql:host=$host;dbname=$db";

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    echo 'Bağlantı hatası: ' . $e->getMessage();
    exit;
}
?>
