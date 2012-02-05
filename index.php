<?php
include_once "shop.php";
$dsn = "sqlite:products.sqlite";
$pdo = new PDO($dsn, null, null);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$obj = ShopProduct::getInstance(2, $pdo);
var_dump($obj);
?>
