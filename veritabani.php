<?php
$host = "127.0.0.1";
$dbname = "bemaotoveritabani";
$kullanici = "root";
$sifre = "1234";

try {
    $baglanti = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $kullanici, $sifre);
    $baglanti->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}
?>
