<?php
session_start();
require_once "veritabani.php";

$isAdmin = isset($_SESSION['admin']) && $_SESSION['admin'] === true;
$isUser = isset($_SESSION['kullanici_adi']) && !empty($_SESSION['kullanici_adi']);

if (!$isAdmin && !$isUser) {
    header("Location: giris.php");
    exit();
}

if (!$isAdmin) {
    if (isset($_SESSION["email"])) {
        $email = $_SESSION["email"];
    } else {
        header("Location: kullanici-giris.php");
        exit();
    }
}

if ($isAdmin) {
    $user = ['kullanici_adi' => 'Admin', 'email' => 'admin@domain.com'];
} else {
    $query = $baglanti->prepare("SELECT * FROM kullanicilar WHERE email = ?");
    $query->execute([$email]);
    $user = $query->fetch(PDO::FETCH_ASSOC);
}

function fiyatVeSureGetir($kategori, $arac_turu) {
    $veri = [
        'Dış Temizlik' => [
            'SUV' => ['fiyat' => 250, 'sure' => 60],
            'Sedan' => ['fiyat' => 200, 'sure' => 50],
            'Hatchback' => ['fiyat' => 180, 'sure' => 45],
            'MPV' => ['fiyat' => 270, 'sure' => 65],
            'Crossover' => ['fiyat' => 260, 'sure' => 60]
        ],
        'İç Temizlik' => [
            'SUV' => ['fiyat' => 220, 'sure' => 55],
            'Sedan' => ['fiyat' => 180, 'sure' => 45],
            'Hatchback' => ['fiyat' => 160, 'sure' => 40],
            'MPV' => ['fiyat' => 230, 'sure' => 60],
            'Crossover' => ['fiyat' => 210, 'sure' => 55]
        ],
        'Cam Filmi' => [
            'SUV' => ['fiyat' => 500, 'sure' => 90],
            'Sedan' => ['fiyat' => 450, 'sure' => 80],
            'Hatchback' => ['fiyat' => 400, 'sure' => 70],
            'MPV' => ['fiyat' => 520, 'sure' => 95],
            'Crossover' => ['fiyat' => 480, 'sure' => 85]
        ],
        'PPF' => [
            'SUV' => ['fiyat' => 1500, 'sure' => 240],
            'Sedan' => ['fiyat' => 1300, 'sure' => 210],
            'Hatchback' => ['fiyat' => 1200, 'sure' => 200],
            'MPV' => ['fiyat' => 1550, 'sure' => 250],
            'Crossover' => ['fiyat' => 1450, 'sure' => 230]
        ],
        'İç Dış Yıkama' => [
            'SUV' => ['fiyat' => 350, 'sure' => 75],
            'Sedan' => ['fiyat' => 300, 'sure' => 65],
            'Hatchback' => ['fiyat' => 280, 'sure' => 60],
            'MPV' => ['fiyat' => 370, 'sure' => 80],
            'Crossover' => ['fiyat' => 360, 'sure' => 75]
        ],
    ];

    return $veri[$kategori][$arac_turu] ?? ['fiyat' => 0, 'sure' => 0];
}

$hata = "";
$basari = "";

// Form işlemleri burada yapılıyor, yönlendirme varsa hemen exit() var.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['durum_guncelle'])) {
        $temizlik_id = $_POST['temizlik_id'] ?? '';
        $yeni_durum = $_POST['yeni_durum'] ?? '';
        if ($temizlik_id && $yeni_durum) {
            $guncelle = $baglanti->prepare("UPDATE temizlik SET durum = ? WHERE temizlik_id = ?");
            $guncelle->execute([$yeni_durum, $temizlik_id]);
            header("Location: temizlik.php");
            exit();
        } else {
            $hata = "Durum güncellenemedi. Eksik bilgi.";
        }
    } elseif (isset($_POST['sil'])) {
        $temizlik_id = $_POST['temizlik_id'] ?? '';
        if ($temizlik_id) {
            $sil = $baglanti->prepare("DELETE FROM temizlik WHERE temizlik_id = ?");
            $sil->execute([$temizlik_id]);
            header("Location: temizlik.php");
            exit();
        } else {
            $hata = "Silme işlemi başarısız.";
        }
    } elseif (isset($_POST["plaka"])) {
        $plaka = trim($_POST["plaka"]);
        $islem_adi = trim($_POST["islem_adi"]);
        $kategori = $_POST["kategori"] ?? '';
        $arac_turu = $_POST["arac_turu"] ?? '';

        if (empty($plaka) || empty($islem_adi) || empty($kategori) || empty($arac_turu)) {
            $hata = "Lütfen tüm alanları doldurun.";
        } else {
            $fiyatSure = fiyatVeSureGetir($kategori, $arac_turu);
            $fiyat = $fiyatSure['fiyat'];
            $sure = $fiyatSure['sure'];

            $insert = $baglanti->prepare("INSERT INTO temizlik (islem_adi, arac_turu, fiyat, plaka, sure, kategori, durum) VALUES (?, ?, ?, ?, ?, ?, 'Beklemede')");
            $sonuc = $insert->execute([$islem_adi, $arac_turu, $fiyat, $plaka, $sure, $kategori]);

            if ($sonuc) {
                header("Location: temizlik.php");
                exit();
            } else {
                $hata = "Bir hata oluştu. Lütfen tekrar deneyin.";
            }
        }
    }
}

$randevular = $baglanti->query("SELECT * FROM temizlik ORDER BY temizlik_id DESC")->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <title>Temizlik Randevu</title>
    <style>
        /* K Teması Arka Plan Animasyonu */
        

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 1000px;
            margin: 40px auto;
            background-color: #001e3c; /* Koyu lacivert */
            animation: bgAnim 15s ease infinite;
            padding: 20px;
            border-radius: 15px;
            box-shadow:
                0 8px 32px 0 rgba(31, 38, 135, 0.37),
                0 0 0 1px rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            color: #f0f4f8;
        }
        h1 {
            text-align: center;
            color: #e1eaff;
            font-weight: 700;
            text-shadow: 1px 1px 5px #1b2e5a;
            margin-bottom: 25px;
        }
        form {
            background: rgba(255 255 255 / 0.12);
            padding: 25px;
            border-radius: 15px;
            box-shadow:
                0 4px 30px rgba(31, 38, 135, 0.1);
            border: 1px solid rgba(255 255 255 / 0.3);
        }
        label {
            display: block;
            margin-top: 15px;
            font-weight: 600;
            color: #dbe9ff;
            text-shadow: 0 0 2px #204a94;
        }
        input[type=text], select {
            width: 100%;
            padding: 10px 12px;
            margin-top: 6px;
            border-radius: 8px;
            border: none;
            font-size: 16px;
            background: rgba(255 255 255 / 0.3);
            color: #ffffff;
            box-shadow: inset 0 0 5px rgba(0,0,0,0.2);
            transition: background-color 0.3s ease;
        }
        input[type=text]:focus, select:focus {
            background: rgba(255 255 255 / 0.5);
            outline: none;
            color: #000;
        }
        button {
            margin-top: 20px;
            background: #2c67f2;
            color: white;
            border: none;
            padding: 14px 26px;
            font-size: 16px;
            border-radius: 12px;
            cursor: pointer;
            transition: background 0.3s ease, box-shadow 0.3s ease;
            font-weight: 700;
            box-shadow: 0 4px 15px rgba(44,103,242,0.6);
        }
        button:hover {
            background: #1753d1;
            box-shadow: 0 6px 20px rgba(23,83,209,0.8);
        }
        .geri-don-btn {
            background: #1b4cca;
            margin-left: 10px;
            box-shadow: 0 3px 10px rgba(27,76,202,0.7);
        }
        .geri-don-btn:hover {
            background: #153aa3;
            box-shadow: 0 6px 18px rgba(21,58,163,0.9);
        }
        .message {
            margin-top: 15px;
            padding: 12px 18px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 15px;
            box-shadow: 0 0 8px rgba(0,0,0,0.15);
            user-select: none;
        }
        .success {
            background-color: rgba(20, 120, 20, 0.7);
            color: #d4f5d4;
            text-shadow: 0 0 4px #0f650f;
        }
        .error {
            background-color: rgba(180, 30, 30, 0.7);
            color: #f6d4d4;
            text-shadow: 0 0 5px #7a1919;
        }
        table {
            width: 100%;
            margin-top: 40px;
            border-collapse: separate;
            border-spacing: 0 10px;
            font-size: 14px;
            color: #e0e7ff;
            text-shadow: 0 0 4px #1a2f6e;
        }
        th, td {
            padding: 12px 15px;
            background: rgba(255 255 255 / 0.12);
            border-radius: 12px;
            text-align: center;
            vertical-align: middle;
            box-shadow: inset 0 0 6px rgba(0,0,0,0.2);
        }
        th {
            background: #2c67f2;
            font-weight: 700;
            box-shadow: 0 4px 10px rgba(44,103,242,0.7);
            color: #e5f0ff;
        }
        /* Modal düzenleme */
        .modal {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background-color: rgba(20, 30, 60, 0.75);
            z-index: 1000;
            display: flex;
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(6px);
            -webkit-backdrop-filter: blur(6px);
        }
        .modal-content {
            background: rgba(30, 40, 70, 0.85);
            border-radius: 20px;
            padding: 25px 30px;
            width: 320px;
            box-shadow:
                0 8px 32px 0 rgba(31, 38, 135, 0.37),
                0 0 0 1px rgba(255, 255, 255, 0.18);
            color: #cbd6f7;
            text-align: center;
            position: relative;
        }
        .modal-content h3 {
            margin: 0 0 15px;
            font-weight: 700;
            color: #e8ecff;
            text-shadow: 0 0 5px #1f3a8a;
        }
        .modal-content label {
            color: #aeb6ff;
            font-weight: 600;
            display: block;
            margin-bottom: 8px;
            text-align: left;
        }
        .modal-content select {
            width: 100%;
            padding: 10px;
            border-radius: 10px;
            border: none;
            background: rgba(255 255 255 / 0.25);
            color: #e0e7ff;
            box-shadow: inset 0 0 6px rgba(0,0,0,0.25);
            margin-bottom: 15px;
            font-size: 15px;
            font-weight: 600;
        }
        .modal-content button {
            width: 100%;
            padding: 12px 0;
            font-size: 16px;
            border-radius: 12px;
            border: none;
            cursor: pointer;
            font-weight: 700;
            margin-top: 10px;
            transition: background-color 0.3s ease;
            box-shadow: 0 4px 15px rgba(44,103,242,0.6);
        }
        .modal-content button[name="durum_guncelle"] {
            background-color: #2c67f2;
            color: #e5f0ff;
        }
        .modal-content button[name="durum_guncelle"]:hover {
            background-color: #1a45c7;
        }
        .modal-content button[name="sil"] {
            background-color: #c0392b;
            color: #ffd6d6;
            margin-top: 5px;
            box-shadow: 0 4px 12px rgba(192,57,43,0.8);
        }
        .modal-content button[name="sil"]:hover {
            background-color: #8e261e;
        }
        .modal-content button[onclick] {
            background: #555;
            margin-top: 15px;
            box-shadow: none;
            color: #ddd;
        }
        .modal-content button[onclick]:hover {
            background: #444;
        }
    </style>
</head>
<body>

<h1>Temizlik Randevu Oluştur</h1>

<?php if ($hata): ?>
    <div class="message error"><?php echo htmlspecialchars($hata); ?></div>
<?php endif; ?>

<?php if ($basari): ?>
    <div class="message success"><?php echo htmlspecialchars($basari); ?></div>
<?php endif; ?>

<form method="post" action="">
    <label for="plaka">Araç Plakası:</label>
    <input type="text" id="plaka" name="plaka" required placeholder="Plakayı giriniz" />

    <label for="islem_adi">İşlem Adı:</label>
    <input type="text" id="islem_adi" name="islem_adi" required placeholder="İşlem adını giriniz" />

    <label for="kategori">Kategori:</label>
    <select name="kategori" id="kategori" required>
        <option value="" disabled selected>Seçiniz</option>
        <option value="Dış Temizlik">Dış Temizlik</option>
        <option value="İç Temizlik">İç Temizlik</option>
        <option value="Cam Filmi">Cam Filmi</option>
        <option value="PPF">PPF</option>
        <option value="İç Dış Yıkama">İç Dış Yıkama</option>
    </select>

    <label for="arac_turu">Araç Türü:</label>
    <select name="arac_turu" id="arac_turu" required>
        <option value="" disabled selected>Seçiniz</option>
        <option value="SUV">SUV</option>
        <option value="Sedan">Sedan</option>
        <option value="Hatchback">Hatchback</option>
        <option value="MPV">MPV</option>
        <option value="Crossover">Crossover</option>
    </select>

    <button type="submit">Randevu Oluştur</button>

    <?php

if (isset($_SESSION['admin']) && $_SESSION['admin'] === true) {
    $geriDonUrl = 'admin-panel.php';
} else {
    $geriDonUrl = 'kullanici-panel.php';
}
?>
<button type="button" onclick="window.location.href='<?php echo $geriDonUrl; ?>'">Geri Dön</button>
</form>

<?php if ($randevular): ?>
    <h2>Sıradaki İşlemler</h2>
    <table>
        <thead>
            <tr>
                <th>Plaka</th>
                <th>İşlem Adı</th>
                <th>Kategori</th>
                <th>Araç Türü</th>
                <th>Fiyat (₺)</th>
                <th>Süre (dk)</th>
                <th>Durum</th>
                <?php if ($isAdmin): ?>
    <th>İşlemler</th>
<?php endif; ?>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($randevular as $r): ?>
            <tr>
                <td><?php echo htmlspecialchars($r['plaka']); ?></td>
                <td><?php echo htmlspecialchars($r['islem_adi']); ?></td>
                <td><?php echo htmlspecialchars($r['kategori']); ?></td>
                <td><?php echo htmlspecialchars($r['arac_turu']); ?></td>
                <td><?php echo number_format($r['fiyat'], 2, ',', '.'); ?></td>
                <td><?php echo (int)$r['sure']; ?></td>
                <td><?php echo htmlspecialchars($r['durum']); ?></td>
                <?php if ($isAdmin): ?>
    <td>
    <?php if ($isAdmin): ?>
    <button onclick="openModal(<?php echo $r['temizlik_id']; ?>)">Düzenle</button>
<?php endif; ?>
    </td>
    

<?php endif; ?>

            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
<?php foreach ($randevular as $r): ?>
<div id="modal-<?php echo $r['temizlik_id']; ?>" class="modal" style="display:none;">
    <div class="modal-content">
        <h3>İşlemi Düzenle - <?php echo htmlspecialchars($r['plaka']); ?></h3>
        <form method="POST" action="">
            <input type="hidden" name="temizlik_id" value="<?php echo $r['temizlik_id']; ?>">
            <label>Durum:</label>
            <select name="yeni_durum" required>
                <option value="Beklemede" <?php if ($r['durum'] == 'Beklemede') echo 'selected'; ?>>Beklemede</option>
                <option value="İşlemde" <?php if ($r['durum'] == 'İşlemde') echo 'selected'; ?>>İşlemde</option>
                <option value="Tamamlandı" <?php if ($r['durum'] == 'Tamamlandı') echo 'selected'; ?>>Tamamlandı</option>
            </select>
            <button type="submit" name="durum_guncelle">Güncelle</button>
        </form>
        <form method="POST" action="" onsubmit="return confirm('Silmek istediğinizden emin misiniz?');">
            <input type="hidden" name="temizlik_id" value="<?php echo $r['temizlik_id']; ?>">
            <button type="submit" name="sil" style="background-color: #c0392b;">Sil</button>
        </form>
        <button onclick="closeModal(<?php echo $r['temizlik_id']; ?>)">Kapat</button>
    </div>
</div>
<?php endforeach; ?>

<script>
function openModal(id) {
    document.getElementById('modal-' + id).style.display = 'block';
}
function closeModal(id) {
    document.getElementById('modal-' + id).style.display = 'none';
}
</script>

<style>
.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.4);
    z-index: 999;
}
.modal-content {
    background: white;
    margin: 10% auto;
    padding: 20px;
    border-radius: 10px;
    width: 300px;
    position: relative;
}
.modal-content h3 {
    margin-top: 0;
}
.modal-content button {
    margin-top: 10px;
    width: 100%;
}
</style>

</body>

<?php
// Durum güncelleme
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["durum_guncelle"])) {
    $id = $_POST["temizlik_id"];
    $yeni_durum = $_POST["yeni_durum"];
    $guncelle = $baglanti->prepare("UPDATE temizlik SET durum = ? WHERE temizlik_id = ?");
    $guncelle->execute([$yeni_durum, $id]);
    header("Location: temizlik.php");
    exit();
}

// Silme işlemi
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["sil"])) {
    $id = $_POST["temizlik_id"];
    $sil = $baglanti->prepare("DELETE FROM temizlik WHERE temizlik_id = ?");
    $sil->execute([$id]);
    header("Location: temizlik.php");
    exit();
}
?>

</html>