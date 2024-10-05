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


$query = 'SELECT * FROM users WHERE role = "company" AND deleted_at IS NOT NULL';
$stmt = $pdo->query($query);
$deleted_companies = $stmt->fetchAll(PDO::FETCH_ASSOC);


if (isset($_GET['restore_company'])) {
    $restore_company_id = $_GET['restore_company'];


    $restore_query = 'UPDATE users SET deleted_at = NULL WHERE id = :id';
    $stmt = $pdo->prepare($restore_query);
    $stmt->execute(['id' => $restore_company_id]);

    header('Location: recent_deleted_companies.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Silinen Şirketler</title>
</head>
<body>
    <h1>Silinen Şirketler</h1>

    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Şirket Adı</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Geri Al</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($deleted_companies as $company): ?>
                <tr>
                    <td><?php echo htmlspecialchars($company['id']); ?></td>
                    <td><?php echo htmlspecialchars($company['username']); ?></td>
                    <td><?php echo htmlspecialchars($company['email']); ?></td>
                    <td><?php echo htmlspecialchars($company['role']); ?></td>
                    <td>
                        <a href="recent_deleted_companies.php?restore_company=<?php echo $company['id']; ?>" onclick="return confirm('Bu şirketi geri almak istediğinize emin misiniz?');">Geri Al</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
