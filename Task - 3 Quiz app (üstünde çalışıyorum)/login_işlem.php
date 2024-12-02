<?php
session_start();
include('funcions.php');


if ($_SERVER['REQUEST_METHOD'] == 'POST'){
    $username = $_POST['username'];
    $password = $_POST['password'];


    $user = login($username, $password);

    if($user) {
        $_SESSION['$username'] = $user['username'];
        $_SESSION['$role'] = $user['role'];
        checkuserrole($user);

    }
    else {
        echo "Yanlış şifre yada kullanıcı adı...";
    }
}

?>