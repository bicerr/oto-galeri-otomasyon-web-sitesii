<?php
session_start();
include 'veritabani.php';

// Sadece admin erişebilir
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: index.php");
    exit();
}

// ID kontrolü
if (!isset($_GET['id'])) {
    echo "Geçersiz ID.";
    exit();
}

$id = $_GET['id'];

// Güncelleme işlemi
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $marka = $_POST['marka'];
    $model = $_POST['model'];
    $yil = $_POST['yil'];
    $vites = $_POST['vites_turu'];
    $yakit = $_POST['yakit_turu'];
    $fiyat = $_POST['fiyat'];

    $sorgu = $baglanti->prepare("UPDATE arabalar SET marka=?, model=?, yil=?, vites_turu=?, yakit_turu=?, fiyat=? WHERE id=?");
    $sorgu->execute([$marka, $model, $yil, $vites, $yakit, $fiyat, $id]);

    header("Location: araba-listesi.php");
    exit();
}

// Mevcut veriyi getir
$sorgu = $baglanti->prepare("SELECT * FROM arabalar WHERE id = ?");
$sorgu->execute([$id]);
$araba = $sorgu->fetch(PDO::FETCH_ASSOC);

if (!$araba) {
    echo "Araba bulunamadı.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Araba Güncelle</title>
</head>
<body>
    <h1>Satılık Araba Güncelle</h1>
    <form method="post">
        Marka: <input type="text" name="marka" value="<?= htmlspecialchars($araba['marka']) ?>"><br><br>
        Model: <input type="text" name="model" value="<?= htmlspecialchars($araba['model']) ?>"><br><br>
        Yıl: <input type="number" name="yil" value="<?= htmlspecialchars($araba['yil']) ?>"><br><br>
        Vites Türü: <input type="text" name="vites_turu" value="<?= htmlspecialchars($araba['vites_turu']) ?>"><br><br>
        Yakıt Türü: <input type="text" name="yakit_turu" value="<?= htmlspecialchars($araba['yakit_turu']) ?>"><br><br>
        Fiyat: <input type="number" name="fiyat" value="<?= htmlspecialchars($araba['fiyat']) ?>"><br><br>

        <button type="submit">Güncelle</button>
    </form>
    <br>
    <a href="araba-listesi.php">◀️ Listeye Geri Dön</a>
</body>
</html>
