<?php
require_once "baglanti.php";

//id bilgisine göre müşteri bilgilerini inputlara çekme
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $arananID = isset($_POST['arananID']) ? htmlspecialchars($_POST['arananID']) : null;

    if ($arananID) {
        $query = $db->prepare('SELECT * FROM customers WHERE customersID = ?');
        $query->execute([$arananID]);
        $musteri = $query->fetch(PDO::FETCH_ASSOC);

        if ($musteri) {
            echo 'Müşteri ID: ' . $musteri['customersID'] . '<br>';
            echo 'Ad: ' . $musteri['firstName'] . '<br>';
            echo 'Soyad: ' . $musteri['lastName'] . '<br>';
            echo 'Mail: ' . $musteri['email'] . '<br>';
            echo 'Bakiye: ' . $musteri['balance'] . '<br>';
        } else {
            echo 'Müşteri bulunamadı.';
        }
    } else {
        echo 'Aranan Sayı Girilmedi.';
    }

    //müşteri bilgilerini id ye göre güncelleme
    if (isset($_POST['updateBtn'])) {
        $id = isset($_POST['id']) ? htmlspecialchars($_POST['id']) : null;
        $firstName = isset($_POST['firstName']) ? htmlspecialchars($_POST['firstName']) : null;
        $lastName = isset($_POST['lastName']) ? htmlspecialchars($_POST['lastName']) : null;
        $email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : null;
        $balance = isset($_POST['balance']) ? htmlspecialchars($_POST['balance']) : null;

        if ($id && $firstName && $lastName && $email && $balance) {
            $update = $db->prepare('UPDATE customers SET 
                firstName = ?,
                lastName = ?,
                email = ?,
                balance = ? WHERE customersID = ?');

            $result = $update->execute([$firstName, $lastName, $email, $balance, $id]);

            if ($result) {
                echo "Güncelleme Tamam";
            } else {
                echo "Güncelleme Başarısız";
            }
        } else {
            echo 'Tüm alanları doldurunuz.';
        }
    }

    //müşteri id'sine göre silme işlemi
    if (isset($_POST['sil_btn'])) {
        $id = isset($_POST['sil']) ? htmlspecialchars($_POST['sil']) : null;

        if ($id) {
            $delete = $db->prepare('DELETE FROM customers WHERE customersID = ?');
            $sil = $delete->execute([$id]);

            if ($sil) {
                echo "Silme Tamam";
            } else {
                echo "Silme Başarısız";
            }
        } else {
            echo 'Silinecek müşteri ID giriniz.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <style>
        table {
            border-spacing: 0;
            width: 100%;
            border: 1px solid #ddd;
        }

        th,
        td {
            text-align: left;
            padding: 16px;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2
        }
    </style>
</head>

<body>

    <table id="myTable">
        <tr>
            <th>ID</th>
            <th>Ad</th>
            <th>Soyad</th>
            <th>Mail</th>
            <th>Bakiye</th>
        </tr>

        <?php
        $sql = "SELECT * FROM customers";
        $result = $db->query($sql);

        if ($result->rowCount() > 0) { //müşteri bilgilerini table a doldurma işlemi
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>" . $row["customersID"] . "</td>";
                echo "<td>" . $row["firstName"] . "</td>";
                echo "<td>" . $row["lastName"] . "</td>";
                echo "<td>" . $row["email"] . "</td>";
                echo "<td>" . $row["balance"] . "</td>";
                echo "</tr>";
            }
        } else {
            echo "Tabloda veri bulunamadı.";
        }
        $db = null;
        ?>

    </table>

    <button id="myBtn">Güncelle</button>

    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <form method="POST">
                ID:
                <input type="text" name="id" value="<?php echo isset($_POST['id']) ? $_POST['id'] : null ?>"><br><br>

                Ad:
                <input type="text" name="firstName" value="<?php echo isset($_POST['firstName']) ? $_POST['firstName'] : null ?>"><br><br>

                Soyad:
                <input type="text" name="lastName" value="<?php echo isset($_POST['lastName']) ? $_POST['lastName'] : null ?>"><br><br>

                Email:
                <input type="email" name="email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : null ?>"><br><br>

                Bakiye:
                <input type="text" name="balance" value="<?php echo isset($_POST['balance']) ? $_POST['balance'] : null ?>"><br><br>

                <button type="submit" name="updateBtn">Güncelle</button>
            </form>
        </div>
    </div><br><br>

    <form  action="musteri_bilgileri.php" method="POST">
        Ara: <input type="text" name="arananID">
        <button type="submit" id="ara_btn">Ara</button><br><br>
        Sil: <input type ="text" name="sil" value="<?php echo isset($_POST['sil']) ? $_POST['sil'] : null ?>">
        <button type="submit" name="sil_btn">Sil</button><br><br>
        
    </form>

    <script>
        //modal'ın açılabilmesi için script kodları
        var modal = document.getElementById("myModal");

        var btn = document.getElementById("myBtn");
        var span = document.getElementsByClassName("close")[0];

        btn.onclick = function () {
            modal.style.display = "block";
        }

        span.onclick = function () {
            modal.style.display = "none";
        }

        window.onclick = function (event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
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
