<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/sorusayfası.css">
</head>
<body>
    <div class="quizcontainer">
        <div class="addquestion">
            <h2>Soru ekle!</h2>
            <form method="post" action="addquestion.php">
                <p>
                    <label>Soru numarası</label>
                    <input type="number" name="Soru_numarası">
                     
                </p>
                <p>
                    <label>Soru metni</label>
                    <input type="text" name="Soru metni">
                     
                </p>
                <p>
                    <label>Seçenek 1</label>
                    <input type="text" name="secenek1">
                     
                </p>
                <p>
                    <label>Seçenek 2</label>
                    <input type="text" name="secenek2">
                     
                </p>
                <p>
                    <label>Seçenek 3</label>
                    <input type="text" name="secenek3">
                     
                </p>
                <p>
                    <label>Seçenek 4</label>
                    <input type="text" name="secenek4">
                     
                </p>
                <p>
                    <label>Seçenek 5</label>
                    <input type="text" name="secenek5">
                     
                </p>
                <p>
                    <label>Doğru olan cevap</label>
                    <input type="number" name="dogru_cevap">
                     
                </p>
                <p>
                    <input type="submit" name="submit" value="Gönder">
                     
                </p>
            </form>
        </div>
        </div>
</body>
</html>