<?php

require_once 'baglanti.php';

$urun_adi = '';
$urun_fiyat = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $urun_adi = isset($_POST['urun_adi']) ? $_POST['urun_adi'] : null;
    $urun_fiyat = isset($_POST['urun_fiyat']) ? $_POST['urun_fiyat'] : null;
}

if (!$urun_adi || !$urun_fiyat) {
} else { //ürün kayıt
    $query = $db->prepare('INSERT INTO products SET 
        productsName = :urun_adi,
        price = :urun_fiyat');

    $insert = $query->execute([
        ':urun_adi' => $urun_adi,
        ':urun_fiyat' => $urun_fiyat
    ]);

    if ($insert) {
        header('Location: urun_kayit.php');
    } else {
        $error = $query->errorInfo();
        echo $error[2];
    }
}

if (isset($_POST['sil_buton'])) { //ürün sil
    $id = isset($_POST['urun_id']) ? htmlspecialchars($_POST['urun_id']) : null;

    if ($id) {
        $delete = $db->prepare('DELETE FROM products WHERE productsID = ?');
        $sil = $delete->execute([$id]);

        if ($sil) {
            echo "Silme Tamam";
            // Silme işlemi başarılıysa sayfayı yeniden yükle
            echo '<meta http-equiv="refresh" content="0">';
        } else {
            echo "Silme Başarısız";
        }
    } else {
        echo 'Silinecek ürün ID giriniz.';
    }
}

if (isset($_POST['guncelle_btn'])) { //ürün güncelle
    $productID = isset($_POST['guncelle_id']) ? htmlspecialchars($_POST['guncelle_id']) : null;
    $productName = isset($_POST['guncelle_ad']) ? htmlspecialchars($_POST['guncelle_ad']) : null;
    $price = isset($_POST['guncelle_fiyat']) ? htmlspecialchars($_POST['guncelle_fiyat']) : null;

    if ($productID && $productName && $price) {
        $update = $db->prepare('UPDATE products SET 
            productsName = ?,
            price = ? WHERE productsID = ?');

        $result = $update->execute([$productName, $price, $productID]);

        if ($result) {
            echo "Güncelleme Tamam";
        } else {
            echo "Güncelleme Başarısız";
        }
    } else {
        echo 'Tüm alanları doldurunuz.';
    }
}

?>

<style>
table {
  font-family: arial, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

td, th {
  border: 1px solid #dddddd;
  text-align: left;
  padding: 8px;
}

tr:nth-child(even) {
  background-color: #dddddd;
}
</style>
</head>
<body>

<h3>Kayıtlı Ürünler</h3>

<table>
  <tr>
    <th>ID</th>
    <th>Ürün Adı</th>
    <th>Fiyat</th>
    <th> İşlem </th>
  </tr>
  <tr>
  <?php
    $sql = "SELECT * FROM products";
    $sorgu = $db->query($sql);

    if ($sorgu->rowCount() > 0) { //tabloya ürünlerin bilgilerini çek sil ve güncelle işlemleri ekle
        while ($row = $sorgu->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>" . $row["productsID"] . "</td>";
            echo "<td>" . $row["productsName"] . "</td>";
            echo "<td>" . $row["price"] . "</td>";
            echo "<td><button onclick=\"openModal('myModal{$row['productsID']}')\">Güncelle</button>";
            echo "<td><form method='post'><input type='hidden' name='urun_id' value='" . $row['productsID'] . "'><button type='submit' name='sil_buton'>Sil</button></form></td>";
            echo "</tr>";

            echo "<div id='myModal{$row['productsID']}' class='modal'>";
            echo "<div class='modal-content'>";
            echo "<span class='close' onclick=\"closeModal('myModal{$row['productsID']}')\">&times;</span>";
            echo "<form method='POST'>";
            echo "ID: <input type='text' name='guncelle_id' value='{$row['productsID']}' readonly> <br><br>";
            echo "Ürün Adı: <input type='text' name='guncelle_ad' value='{$row['productsName']}'><br><br>";
            echo "Fiyat: <input type='text' name='guncelle_fiyat' value='{$row['price']}'><br><br>";
            echo "<button type='submit' name='guncelle_btn'>Güncelle</button>";
            echo "</form>";
            echo "</div>";
            echo "</div>";
        }
    } else {
        echo "Tabloda veri bulunamadı.";
    }
    $db = null;
    ?>
  </tr>
</table>

<div>
    <form action="urun_kayit.php" method="post">

        <h3> Ürün Kayıt </h3>
        Ürün Adı: <input type="text" name="urun_adi" value="<?php echo $urun_adi; ?>"><br><br>
        Ürün Fiyatı: <input type="text" name="urun_fiyat" value="<?php echo $urun_fiyat; ?>"><br><br>

        <button type="submit" name="submit">Ürün Kaydet</button><br><br>

        <!--<h3>Ürün Sil</h3>
        Ürün ID: <input type ="text" name="urun_id" value="<?php echo isset($_POST['urun_id']) ? $_POST['urun_id'] : null ?>"><br><br>
        <button type = "submit" name = "sil_buton"> Ürün Sil </button><br><br>-->
    </form>
</div>

    <script>
         function openModal(modalId) { //modalı aç
        var modal = document.getElementById(modalId);
        modal.style.display = "block";
    }

    function closeModal(modalId) { //modalı kapat
        var modal = document.getElementById(modalId);
        modal.style.display = "none";
    }
    </script>

<style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0, 0, 0);
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>

    </body>
</html>
