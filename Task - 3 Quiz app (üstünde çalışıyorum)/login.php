<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <h1>Login ekranı placeholder</h1>
    <!-- action olarak php dosyasına submit olcak. -->
    
    
    <form action="login_işlem.php" method="POST">
        <label for="username">Kullanıcı adı:</label>
        <input type="text" id="username" name="username" required>
        <br>
        <label for="password">Şifre:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <button type="submit">Giriş Yap!</button>



    </form>
</body>
</html>