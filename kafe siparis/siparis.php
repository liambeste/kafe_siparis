<?php
require_once 'baglanti.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $musteri_id = isset($_POST['musteri_id']) ? htmlspecialchars($_POST['musteri_id']) : null;
    $masa_numara = isset($_POST['masa_numara']) ? htmlspecialchars($_POST['masa_numara']) : null;

    // Seçili ürünleri al
    $secili_urunler = isset($_POST['ad']) ? $_POST['ad'] : array();

    // Toplam fiyat hesapla
    $toplam_fiyat = 0;
    $productsIDArray = array(); // Products ID'leri saklamak için bir dizi oluştur

    foreach ($secili_urunler as $urunAdi) {
        $sorgu = $db->prepare('SELECT productsID, price FROM products WHERE productsName = ?');
        $sorgu->execute([$urunAdi]);
        $result = $sorgu->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $productsIDArray[] = $result['productsID'];
            $toplam_fiyat += $result['price'];
        } else {
            echo 'Ürün bulunamadı: ' . $urunAdi; // Eklediğim kontrol
        }
    }

    // Müşteri bakiyesini kontrol et
    try {
        $sorgu = $db->prepare('SELECT balance FROM customers WHERE customersID = ?');
        $sorgu->execute([$musteri_id]);
        $balance = $sorgu->fetch(PDO::FETCH_ASSOC);

        if ($balance) {
            $bakiye = $balance['balance'];

            // Bakiye yetersizse işlemi yapma
            if ($bakiye < $toplam_fiyat) {
                echo 'Bakiye yetersiz!';
            } else {
                // Siparişi veritabanına ekle
                $sorgu = $db->prepare('INSERT INTO orders (customerID, tableNumber, totalAmount, productsID) VALUES (?, ?, ?, ?)');
                $sorgu->execute([$musteri_id, $masa_numara, $toplam_fiyat, implode(', ', $productsIDArray)]);

                // Eklenen siparişin orderID değerini al
                $orderID = $db->lastInsertId();

                // Bakiyeyi güncelle
                $newBalance = $bakiye - $toplam_fiyat;
                $updateSorgu = $db->prepare('UPDATE customers SET balance = ? WHERE customersID = ?');
                $updateSorgu->execute([$newBalance, $musteri_id]);

                // Masa tablosunu güncelle
                $masayaat = $db->prepare('UPDATE tables SET customerID = ?, ordersID = ? WHERE tableNumber = ?');
                $masayaat->execute([$musteri_id, $orderID, $masa_numara]);

                echo 'Sipariş başarıyla alındı!';
            }
        } else {
            echo 'Müşteri bulunamadı.';
        }
    } catch (PDOException $e) {
        echo "Bağlantı hatası: " . $e->getMessage();
    }
}
?>





<h3>Menü</h3>

<form action="siparis.php" method="POST">
    <?php //checkbox a ürün adlarını ve fiyatlarını çek 
    $query = $db->prepare('SELECT productsName, price FROM products');
    $query->execute();
    ?> 
    Ürün Adı:<br><br> 
    <?php while ($satir = $query->fetch(PDO::FETCH_ASSOC)) : ?>
        <label><?= $satir["productsName"] ?>
            <input type="checkbox"
                   class="urun-checkbox"
                   name="ad[]"
                   data-fiyat="<?= $satir["price"] ?>"
                   value="<?= $satir["productsName"] ?>"> ▴
        </label>
    <?php endwhile; ?>
    <input type="text" id="toplam_fiyat" readonly><br><br>

    <?php //müşteri bakiyesini çek
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $musteri_id = isset($_POST['musteri_id']) ? htmlspecialchars($_POST['musteri_id']) : null;

        try {
            $sorgu = $db->prepare('SELECT balance FROM customers WHERE customersID = ?');
            $sorgu->execute([$musteri_id]);
            $balance = $sorgu->fetch(PDO::FETCH_ASSOC);

            if ($balance) {
                echo 'Bakiyeniz: <input type="text" id="bakiye" value="' . $balance['balance'] . '" readonly><br><br>';
            } else {
                echo 'Müşteri bulunamadı.';
            }
        } catch (PDOException $e) {
            echo "Bağlantı hatası: " . $e->getMessage();
        }
    }
    ?>

    Müşteri ID:
    <input type="number" name="musteri_id" value="<?php echo isset($_POST['musteri_id']) ? $_POST['musteri_id'] : null ?>"><br><br>
    Masa Numarası:
    <input type="number" name="masa_numara" value="<?php echo isset($_POST['tableNumber']) ? $_POST['tableNumber'] : null?>"><br><br>

    <input type="submit" value="Sipariş Ver">
</form>

<script>
    var checkboxes = document.querySelectorAll('.urun-checkbox');
    checkboxes.forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
            updateToplamFiyat();
        });
    });

    function updateToplamFiyat() { //ürünlerin toplam fiyatını güncelle
        var toplamFiyatInput = document.getElementById('toplam_fiyat');
        var seciliUrunler = document.querySelectorAll('.urun-checkbox:checked');
        var toplamFiyat = 0;

        seciliUrunler.forEach(function (urun) {
            toplamFiyat += parseFloat(urun.getAttribute('data-fiyat'));
        });

        toplamFiyatInput.value = toplamFiyat.toFixed(2);
    }
</script>
