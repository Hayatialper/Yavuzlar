<?php
$db_host = "localhost";
$db_user = "root"; // Kullanıcı adı
$db_passwd = "123";   // Şifre
$db_name = "Quiz"; // Veritabanı adı


// Burdan itibaren bağlamaya başlıycaz.
$mysqli = new mysqli($db_host, $db_user, $db_passwd, $db_name);
//DİKKAT! YUKARIDAKİ SIRALAMA ÇOK ÖNEMLİ. YOKSA 10+ DK BOYUNCA 
//QUIZ DİYE Bİ KULLANIYICA BAĞLANIR VEYA ŞİFREYİ QUİZ GİRERSİN.



//Hata kodları için
if($mysqli->connect_error) {
    printf("Bağlantı hatası lütfen tekrar dene kanka :%s\n, $mysqli->connection_error");
    exit();
}

?>
