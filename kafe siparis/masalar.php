<?php
//baglanti.php sayfaya dahil etme
require_once 'baglanti.php';
//id ye göre müşteri bilgileri çekme
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $arananID = isset($_POST['arananID']) ? htmlspecialchars($_POST['arananID']) : null;

    if ($arananID) {
        $query = $db->prepare('SELECT firstName FROM tables,customers,orders,products WHERE tableNumber = ?');
        $query->execute([$arananID]);
        $musteri = $query->fetch(PDO::FETCH_ASSOC);

        if ($musteri) {
            echo 'Müşteri ID: ' . $musteri['tableNumber'] . '<br>';
            echo 'Ad: ' . $musteri['customers.customerId'] . '<br>';
            echo 'Soyad: ' . $musteri['ordersId'] . '<br>';
        } else {
            echo 'Müşteri bulunamadı.';
        }
    } else {
        echo 'Aranan Sayı Girilmedi.';
    }
}

?>

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
            <th>Masa</th>
            <th>Ad</th>
            <th>Mail</th>
            <th>Ürünler</th>
            <th>Ürün Fiyat</th>
        </tr>

        <?php
        //birden fazla tablodan join işlemi bilgileri çekildiği sql
        $sql = "SELECT orders.tableNumber, customers.firstName, customers.email, orders.productsID, orders.totalAmount
        FROM customers
        JOIN orders ON customers.customersID = orders.customerID
        JOIN products ON FIND_IN_SET(products.productsID, orders.productsId);
        '";
        $result = $db->query($sql);

        if ($result->rowCount() > 0) {
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>";
                echo "<td>" . $row["tableNumber"] . "</td>";
                echo "<td>" . $row["firstName"] . "</td>";
                echo "<td>" . $row["email"] . "</td>";
                echo "<td>" . $row["productsID"] . "</td>";
                echo "<td>" . $row["totalAmount"] . "</td>";
                echo "</tr>";
            }
        } else {
            echo "Tabloda veri bulunamadı.";
        }
        $db = null;
        ?>

    </table>