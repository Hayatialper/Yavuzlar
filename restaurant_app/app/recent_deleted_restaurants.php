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


$query = 'SELECT * FROM restaurants WHERE deleted_at IS NOT NULL';
$stmt = $pdo->query($query);
$deleted_restaurants = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_GET['restore_restaurant'])) {
    $restore_restaurant_id = $_GET['restore_restaurant'];


    $restore_query = 'UPDATE restaurants SET deleted_at = NULL WHERE id = :id';
    $stmt = $pdo->prepare($restore_query);
    $stmt->execute(['id' => $restore_restaurant_id]);

    header('Location: recent_deleted_restaurants.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Silinen Restoranlar</title>
</head>
<body>
    <h1>Silinen Restoranlar</h1>

    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Restoran Adı</th>
                <th>Açıklama</th>
                <th>Şirket ID</th>
                <th>Geri Al</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($deleted_restaurants as $restaurant): ?>
                <tr>
                    <td><?php echo htmlspecialchars($restaurant['id']); ?></td>
                    <td><?php echo htmlspecialchars($restaurant['name']); ?></td>
                    <td><?php echo htmlspecialchars($restaurant['description']); ?></td>
                    <td><?php echo htmlspecialchars($restaurant['company_id']); ?></td>
                    <td>
                        <a href="recent_deleted_restaurants.php?restore_restaurant=<?php echo $restaurant['id']; ?>" onclick="return confirm('Bu restoranı geri almak istediğinize emin misiniz?');">Geri Al</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
