<?php
session_start();
require_once "veritabani.php";

$isAdmin = isset($_SESSION['admin']) && $_SESSION['admin'] === true;
if (!$isAdmin) {
    header("Location: giris.php");
    exit();
}

$hata = '';
$basarili = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $marka = trim($_POST['kiralik_marka'] ?? '');
    $model = trim($_POST['kiralik_model'] ?? '');
    $aciklama = trim($_POST['aciklama'] ?? '');
    $vites = trim($_POST['vites'] ?? '');
    $yil = trim($_POST['yil'] ?? '');
    $gunluk_fiyat = trim($_POST['gunluk_fiyat'] ?? '');
    $aylik_fiyat = trim($_POST['aylik_fiyat'] ?? '');

    if (!$marka || !$model || !$vites || !$yil || !$gunluk_fiyat || !$aylik_fiyat) {
        $hata = "Lütfen tüm zorunlu alanları doldurun.";
    } elseif (!is_numeric($yil) || !is_numeric($gunluk_fiyat) || !is_numeric($aylik_fiyat)) {
        $hata = "Yıl ve fiyat alanları sayısal olmalıdır.";
    } else {
        $stmt = $baglanti->prepare("INSERT INTO kiralama 
            (kiralik_marka, kiralik_model, aciklama, vites, yil, gunluk_fiyat, aylik_fiyat) 
            VALUES (?, ?, ?, ?, ?, ?, ?)");

        $basarili_insert = $stmt->execute([
            $marka, $model, $aciklama, $vites, $yil, $gunluk_fiyat, $aylik_fiyat
        ]);

        if ($basarili_insert) {
            $kiralama_id = $baglanti->lastInsertId();

            // Fotoğraflar yüklendi mi kontrolü
            if (!empty($_FILES['foto']['name'][0])) {
                foreach ($_FILES['foto']['tmp_name'] as $key => $tmp_name) {
                    $fotoData = file_get_contents($tmp_name);
                    $fotoStmt = $baglanti->prepare("INSERT INTO kiralama_foto (kiralama_id, foto_data) VALUES (?, ?)");
                    $fotoStmt->execute([$kiralama_id, $fotoData]);
                }
            }

            $basarili = "Araç ve fotoğraflar başarıyla eklendi.";
            $_POST = [];
        } else {
            $hata = "Araç eklenirken hata oluştu.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <title>Araç Ekle</title>
    <style>
        body {
            background: linear-gradient(135deg, #001f3f, #003366);
            color: #e0e7ff;
            font-family: Arial, sans-serif;
            padding: 30px;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background-color: #00264d;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 20px #3399ff;
        }
        h1 {
            margin-bottom: 20px;
            border-bottom: 2px solid #3399ff;
            padding-bottom: 10px;
            text-align: center;
        }
        label {
            display: block;
            margin: 15px 0 6px;
            font-weight: bold;
            color: #80bfff;
        }
        input[type="text"],
        textarea {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
        }
        textarea {
            resize: vertical;
            min-height: 80px;
        }
        button {
            margin-top: 20px;
            background-color: #3399ff;
            border: none;
            color: white;
            padding: 12px 25px;
            border-radius: 7px;
            cursor: pointer;
            font-weight: bold;
            width: 100%;
            transition: background 0.3s ease;
        }
        button:hover {
            background-color: #1a75ff;
        }
        .message {
            margin-top: 15px;
            padding: 10px;
            border-radius: 6px;
            font-weight: bold;
        }
        .error {
            background-color: #cc4444;
            color: #fff;
        }
        .success {
            background-color: #44cc44;
            color: #fff;
        }
        .btn-back {
            display: inline-block;
            margin-bottom: 20px;
            padding: 10px 25px;
            background: #3366cc;
            color: #fff;
            border-radius: 30px;
            text-decoration: none;
            font-weight: bold;
            transition: background 0.3s ease;
        }
        .btn-back:hover {
            background: #254e8c;
        }
    </style>
</head>
<body>
    <a href="admin-panel.php" class="btn-back">← Admin Paneline Dön</a>

    <div class="container">
        <h1>Yeni Araç Ekle</h1>

        <?php if ($hata): ?>
            <div class="message error"><?= htmlspecialchars($hata) ?></div>
        <?php endif; ?>

        <?php if ($basarili): ?>
            <div class="message success"><?= htmlspecialchars($basarili) ?></div>
        <?php endif; ?>

        <form action="" method="post" enctype="multipart/form-data">
            <label for="kiralik_marka">Marka *</label>
            <input type="text" id="kiralik_marka" name="kiralik_marka" value="<?= htmlspecialchars($_POST['kiralik_marka'] ?? '') ?>" required>

            <label for="kiralik_model">Model *</label>
            <input type="text" id="kiralik_model" name="kiralik_model" value="<?= htmlspecialchars($_POST['kiralik_model'] ?? '') ?>" required>

            
            <label for="aciklama">Açıklama</label>
            <textarea id="aciklama" name="aciklama"><?= htmlspecialchars($_POST['aciklama'] ?? '') ?></textarea>

            <label for="vites">Vites *</label>
            <input type="text" id="vites" name="vites" value="<?= htmlspecialchars($_POST['vites'] ?? '') ?>" required>

            <label for="yil">Yıl *</label>
            <input type="text" id="yil" name="yil" value="<?= htmlspecialchars($_POST['yil'] ?? '') ?>" required>

            <label for="gunluk_fiyat">Günlük Fiyat (₺) *</label>
            <input type="text" id="gunluk_fiyat" name="gunluk_fiyat" value="<?= htmlspecialchars($_POST['gunluk_fiyat'] ?? '') ?>" required>

            <label for="aylik_fiyat">Aylık Fiyat (₺) *</label>
            <input type="text" id="aylik_fiyat" name="aylik_fiyat" value="<?= htmlspecialchars($_POST['aylik_fiyat'] ?? '') ?>" required>

            <label for="foto">Araç Fotoğrafları (birden fazla seçebilirsiniz)</label>
            <input type="file" id="foto" name="foto[]" multiple accept="image/*" style="margin-bottom: 15px;">

            <button type="submit">Araç Ekle</button>
        </form>
    </div>
</body>
</html>
