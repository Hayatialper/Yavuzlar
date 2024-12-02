<?php include "db_connect.php"; ?>
<?php include "functions.php"; 
$question = sorucek();
$choices = cevapcek();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script defer src="scripts/script.js"></script>
    <link defer rel="stylesheet" href="css/sorusayfası.css">
</head>
<body>
    <div class="quizcontainer">
        <div class="soru"><h2>
            <?php echo $question['text']; //not: text ile "text" aynı değilmiş. ?>
        </h2></div>
        <div class="cevaplar">
            <?php while($row = $choices->fetch_assoc()):?>  
                <button><?php echo $row['id']; ?></button>
            <?php endwhile; ?>
        <br><br><br><br>
        </div>
        <div id="controlButtons">
            <button class="nextButton" id="nextButton">Sonraki</button>
            <button class="homeButton" id="homeButton">Anasayfa</button>
        </div>
    </div>   
</body>
</html>



