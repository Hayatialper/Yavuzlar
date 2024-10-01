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


$query = 'SELECT * FROM users WHERE role = "user" AND deleted_at IS NOT NULL';
$stmt = $pdo->query($query);
$deleted_users = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (isset($_GET['restore_user'])) {
    $restore_user_id = $_GET['restore_user'];

    $restore_query = 'UPDATE users SET deleted_at = NULL WHERE id = :id';
    $stmt = $pdo->prepare($restore_query);
    $stmt->execute(['id' => $restore_user_id]);


    header('Location: recent_deleted_users.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Silinen Kullanıcılar</title>
</head>
<body>
    <h1>Silinen Kullanıcılar</h1>

    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Kullanıcı Adı</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Geri Al</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($deleted_users as $user): ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['id']); ?></td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo htmlspecialchars($user['role']); ?></td>
                    <td>
                        <a href="recent_deleted_users.php?restore_user=<?php echo $user['id']; ?>" onclick="return confirm('Bu kullanıcıyı geri almak istediğinize emin misiniz?');">Geri Al</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
