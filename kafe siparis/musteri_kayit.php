<?php
require_once 'baglanti.php';

$ad = "";
$soyad = "";
$mail = "";
$bakiye = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") { //müşteri kayıt için inputlara yazılan bilgilere post işlemi yapıp bir değişkene atama
    $ad = isset($_POST['ad']) ? $_POST['ad'] : null;
    $soyad = isset($_POST['soyad']) ? $_POST['soyad'] : null;
    $mail = isset($_POST['mail']) ? $_POST['mail'] : null;
    $bakiye = isset($_POST['bakiye']) ? $_POST['bakiye'] : null;
}

if (!$ad && !$soyad && !$mail && !$bakiye) {//inputların dolu mu boş mu olduğunu kontrol edip veri tabanına insert işlemi
} else {
    $query = $db->prepare('INSERT INTO customers SET 
        firstName = :ad,
        lastName = :soyad,
        email = :mail,
        balance = :bakiye');

    $insert = $query->execute([
        ':ad' => $ad,
        ':soyad' => $soyad,
        ':mail' => $mail,
        ':bakiye' => $bakiye
    ]);

    if ($insert) {
        header('Location: index.php');
    } else {
        $error = $query->errorInfo();
        echo $error[2];
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<script src="script.js" defer></script>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form</title>
</head>

<body>
    <form action="" method="POST">
        <h3>Müşteri Kayıt </h3>
        Ad: <input required type="text" name="ad" value="<?php echo isset($_POST['ad']) ? $_POST['ad'] : null ?>"><br><br>
        Soyad: <input required type="text" name="soyad" value="<?php echo isset($_POST['soyad']) ? $_POST['soyad'] : null ?>"><br><br>
        E-mail: <input required type="email" name="mail" value="<?php echo isset($_POST['mail']) ? $_POST['mail'] : null ?>"><br><br>
        Bakiye: <input required type="text" name="bakiye" value="<?php echo isset($_POST['bakiye']) ? $_POST['bakiye'] : null ?>"><br><br>
        <button type="submit" name="submit">Müşteri Kayıt</button>
    </form>
    
</body>

</html>
