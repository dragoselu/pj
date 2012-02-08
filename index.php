<?php
include "shop.php";
$dsn = "sqlite:products.db";

$pdo = new PDO($dsn, null, null);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$obj = ShopProduct::getInstance(1, $pdo);

$writer = new TextProductWriter();
$writer->addProduct($obj);
$writer->write();
?>
