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


$query = 'SELECT * FROM users WHERE role = "company" AND deleted_at IS NULL';
$stmt = $pdo->query($query);
$companies = $stmt->fetchAll(PDO::FETCH_ASSOC);


if (isset($_GET['delete_company'])) {
    $delete_company_id = $_GET['delete_company'];


    $delete_query = 'UPDATE users SET deleted_at = NOW() WHERE id = :id';
    $stmt = $pdo->prepare($delete_query);
    $stmt->execute(['id' => $delete_company_id]);

    header('Location: companies.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Şirket Listesi</title>
</head>
<body>
    <h1>Şirketler</h1>

    <a href="recent_deleted_companies.php" style="color: red; text-decoration: none; font-weight: bold;">🗑 Trash</a>

    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Şirket Adı</th>
                <th>Email</th>
                <th>Rol</th>
                <th>Sil</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($companies as $company): ?>
                <tr>
                    <td><?php echo htmlspecialchars($company['id']); ?></td>
                    <td><?php echo htmlspecialchars($company['username']); ?></td>
                    <td><?php echo htmlspecialchars($company['email']); ?></td>
                    <td><?php echo htmlspecialchars($company['role']); ?></td>
                    <td>
                        <a href="companies.php?delete_company=<?php echo $company['id']; ?>" onclick="return confirm('Bu şirketi silmek istediğinizden emin misiniz?');">Sil</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
