<?php
include 'veritabani.php';

if (!isset($_GET['id'])) {
    echo "Geçersiz istek.";
    exit;
}

$id = $_GET['id'];

// İlk önce mevcut bilgileri çek
$sonuc = $conn->query("SELECT * FROM arabalar WHERE id = $id");
$araba = $sonuc->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $marka = $_POST['marka'];
    $model = $_POST['model'];
    $yil = $_POST['yil'];
    $vites = $_POST['vites'];
    $yakit = $_POST['yakit'];
    $fiyat = $_POST['fiyat'];
    $arac_baslik = $_POST['arac_baslik']; // yeni eklenen

    $stmt = $conn->prepare("UPDATE arabalar SET marka=?, model=?, yil=?, vites_turu=?, yakit_turu=?, fiyat=?, arac_baslik=? WHERE id=?");
    $stmt->bind_param("ssissdsi", $marka, $model, $yil, $vites, $yakit, $fiyat, $arac_baslik, $id);

    if ($stmt->execute()) {
        header("Location: index.php?list=true");
        exit;
    } else {
        echo "Güncelleme hatası.";
    }
}
?>

<h2>Araba Bilgilerini Güncelle</h2>
<form method="post">
    Başlık: <input type="text" name="arac_baslik" value="<?= htmlspecialchars($araba['arac_baslik']) ?>" required><br>
    Marka: <input type="text" name="marka" value="<?= htmlspecialchars($araba['marka']) ?>" required><br>
    Model: <input type="text" name="model" value="<?= htmlspecialchars($araba['model']) ?>" required><br>
    Yıl: <input type="number" name="yil" value="<?= htmlspecialchars($araba['yil']) ?>" required><br>
    Vites Türü: <input type="text" name="vites" value="<?= htmlspecialchars($araba['vites_turu']) ?>" required><br>
    Yakıt Türü: <input type="text" name="yakit" value="<?= htmlspecialchars($araba['yakit_turu']) ?>" required><br>
    Fiyat: <input type="number" step="0.01" name="fiyat" value="<?= htmlspecialchars($araba['fiyat']) ?>" required><br>
    <input type="submit" value="Güncelle">
</form>

<br>
<a href="index.php?list=true">← Geri Dön</a>
