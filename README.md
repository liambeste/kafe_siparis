# kafe_siparis

## Kurulum 
Ana sayfa index.php. index.php yi açtıktan sonra çıkan buttonlar ile yapılacak işlem seçilir.

### Veri Tabanını kurma

CREATE TABLE customers (
    customersID INT PRIMARY KEY AUTO_INCREMENT,
    firstName VARCHAR(255) NOT NULL,
    lastName VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    balance INT NOT NULL
);

CREATE TABLE products(
    productsID INT PRIMARY KEY AUTO_INCREMENT,
    productsName VARCHAR(255) NOT NULL,
    price INT NOT NULL
);

CREATE TABLE orders (
    orderID INT PRIMARY KEY AUTO_INCREMENT,
    customerID INT NOT NULL,
    productsID INT NOT NULL,
    orderDate 
)


