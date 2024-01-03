<?php

//pdo bağlantı kodları 
try{
    $db = new PDO('mysql:host=localhost;dbname=kafesiparis', 'root', '');
}
catch(PDOException $e){
     
    echo $e->getMessage();
    $db= null;

}


?>
