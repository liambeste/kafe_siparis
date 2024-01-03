# kafe_siparis

## Kurulum 
Bu sql sorgularını oluşturulan veritabanı içerisinde direkt olarak yazabilirsiniz
Ana sayfa index.php. index.php yi açtıktan sonra çıkan buttonlar ile yapılacak işlem seçilir.

### Veri Tabanını kurma


CREATE TABLE `customers` (
  `customersID` int(11) NOT NULL,
  `firstName` varchar(50) NOT NULL,
  `lastName` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `balance` int(11) NOT NULL
);

CREATE TABLE `orders` (
  `orderID` int(11) NOT NULL,
  `customerID` int(11) NOT NULL,
  `productsID` varchar(250) NOT NULL,
  `orderDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `totalAmount` int(50) NOT NULL,
  `tableNumber` int(5) NOT NULL
);

CREATE TABLE `products` (
  `productsID` int(11) NOT NULL,
  `productsName` varchar(50) NOT NULL,
  `price` int(11) NOT NULL
);

CREATE TABLE `tables` (
  `tableNumber` int(11) NOT NULL,
  `customerID` int(11) NOT NULL,
  `ordersID` int(11) NOT NULL
) ;


