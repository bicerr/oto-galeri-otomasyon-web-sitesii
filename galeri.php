<?php
// Veritabanı bağlantısı
$pdo = new PDO('mysql:host=localhost;dbname=araclar;charset=utf8', 'kullaniciadi', 'sifre');

// Araçları veritabanından çek
$sorgu = $pdo->query("SELECT * FROM arabalar");
$araclar = $sorgu->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Araç Galerisi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            display: flex;
        }

        /* Sol filtre menüsü */
        #filtreMenu {
            width: 250px;
            background: #f3f3f3;
            padding: 20px;
            position: fixed;
            top: 0;
            left: -270px;
            height: 100%;
            transition: left 0.3s ease;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
            z-index: 10;
        }

        #filtreMenu.active {
            left: 0;
        }

        #icerik {
            margin-left: 20px;
            padding: 20px;
            flex: 1;
        }

        .arac {
            display: flex;
            border-bottom: 1px solid #ddd;
            padding: 10px 0;
            align-items: center;
        }

        .arac img {
            width: 120px;
            height: 80px;
            object-fit: cover;
            margin-right: 20px;
        }

        .filtre-btn {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 11;
            background: #007bff;
            color: white;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
        }

        .filtre-baslik {
            font-size: 18px;
            margin-bottom: 10px;
        }

        .filtre-alani label {
            display: block;
            margin-top: 10px;
        }

    </style>
</head>
<body>

<!-- Filtre menüsü -->
<div id="filtreMenu">
    <div class="filtre-baslik">Filtreler</div>
    <div class="filtre-alani">
        <label>Marka</label>
        <input type="text" name="marka">
        <label>Model</label>
        <input type="text" name="model">
        <label>Fiyat Max</label>
        <input type="number" name="fiyat">
        <!-- Daha fazla filtre eklenebilir -->
    </div>
</div>

<!-- Filtreler butonu -->
<button class="filtre-btn" onclick="toggleFiltre()">Filtreler</button>

<!-- İçerik -->
<div id="icerik">
    <h2>Araç Listesi</h2>
    <?php foreach($araclar as $arac): ?>
        <div class="arac">
            <img src="<?= $arac['resim_url'] ?? 'placeholder.jpg' ?>" alt="araba">
            <div>
                <strong><?= $arac['marka'] ?> <?= $arac['model'] ?></strong><br>
                <?= $arac['yil'] ?> | <?= number_format($arac['km'], 0, ',', '.') ?> KM<br>
                Fiyat: <?= number_format($arac['fiyat'], 0, ',', '.') ?> TL<br>
                İl/İlçe: <?= $arac['il'] ?> / <?= $arac['ilce'] ?><br>
                <small><?= date("d M Y", strtotime($arac['ilan_tarihi'])) ?></small>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script>
    function toggleFiltre() {
        document.getElementById('filtreMenu').classList.toggle('active');
    }
</script>

</body>
</html>
