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
$query = 'SELECT * FROM users WHERE role = "user" AND deleted_at IS NULL';
$stmt = $pdo->query($query);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (isset($_GET['delete_user'])) {
    $delete_user_id = $_GET['delete_user'];
    $delete_query = 'UPDATE users SET deleted_at = NOW() WHERE id = :id';
    $stmt = $pdo->prepare($delete_query);
    $stmt->execute(['id' => $delete_user_id]);
    header('Location: users.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kullanıcı Listesi</title>
</head>
<body>
    <h1>Kullanıcılar</h1>

    <a href="recent_deleted_users.php" style="color: red; text-decoration: none; font-weight: bold;">🗑 Trash</a>

    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Kullanıcı Adı</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Sil</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['id']); ?></td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars($user['role']); ?></td>
                    <td>
                        <?php if ($user['role'] !== 'admin'): ?>
                            <a href="users.php?delete_user=<?php echo $user['id']; ?>" onclick="return confirm('Bu kullanıcıyı silmek istediğinizden emin misiniz?');">Sil</a>
                        <?php else: ?>
                            Admin silinemez
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
