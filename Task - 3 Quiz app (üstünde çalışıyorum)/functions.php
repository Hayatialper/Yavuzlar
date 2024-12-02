<?php 
require('db_connect.php');
function sorucek() {
    global $mysqli;
    //soru numarası
    $number = (int) $_GET['n'];


    // Soruyu çek
    $query = "SELECT text FROM questions
                WHERE question_number =$number"; 
                
    //sonucu çek
    $result = $mysqli->query($query) or die($mysqli->error.__LINE__);

    return $result->fetch_assoc(); //sonucu döndür diyor.
}

function cevapcek() {
    global $mysqli;
    //soru numarası tekrardan
    $number = (int) $_GET['n'];


    $query = "SELECT text FROM choices 
                WHERE question_number = $number";

    //sonuçları çek (yani cevapları)
    $choices = $mysqli->query($query) or die($mysqli->error.__LINE__);

    return $choices->fetch_assoc();
}


?>