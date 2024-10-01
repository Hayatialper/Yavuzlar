<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
if ($_SESSION['role'] !== 'admin') {
    echo "Bu sayfaya erişim izniniz yok.";
    exit;
}
require 'db_connect.php';
$query = 'SELECT * FROM restaurants WHERE deleted_at IS NULL';
$stmt = $pdo->query($query);
$restaurants = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (isset($_GET['delete_restaurant'])) {
    $delete_restaurant_id = $_GET['delete_restaurant'];
    $delete_query = 'UPDATE restaurants SET deleted_at = NOW() WHERE id = :id';
    $stmt = $pdo->prepare($delete_query);
    $stmt->execute(['id' => $delete_restaurant_id]);
    header('Location: restaurants.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restoran Listesi</title>
</head>
<body>
    <h1>Restoranlar</h1>

    <a href="recent_deleted_restaurants.php" style="color: red; text-decoration: none; font-weight: bold;">🗑 Çöp Kutusu</a>

    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Restoran Adı</th>
                <th>Açıklama</th>
                <th>Şirket ID</th>
                <th>Sil</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($restaurants as $restaurant): ?>
                <tr>
                    <td><?php echo htmlspecialchars($restaurant['id']); ?></td>
                    <td><?php echo htmlspecialchars($restaurant['name']); ?></td>
                    <td><?php echo htmlspecialchars($restaurant['description']); ?></td>
                    <td><?php echo htmlspecialchars($restaurant['company_id']); ?></td>
                    <td>
                        <a href="restaurants.php?delete_restaurant=<?php echo $restaurant['id']; ?>" onclick="return confirm('Bu restoranı silmek istediğinizden emin misiniz?');">Sil</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
