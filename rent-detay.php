<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once "veritabani.php";

$isAdmin = isset($_SESSION['admin']) && $_SESSION['admin'] === true;
$isUser = isset($_SESSION['kullanici_adi']) && !empty($_SESSION['kullanici_adi']);
$kullanici_id = isset($_SESSION['kullanici_id']) ? $_SESSION['kullanici_id'] : null;

// Eğer ne admin ne de kullanıcı ise giriş sayfasına gönder
if (!$isAdmin && !$isUser) {
    header("Location: giris.php");
    exit();
}

// Eğer kullanıcı oturumu açıksa, kullanici_id'nin boş olmadığını kesinleştir
if ($isUser && !$kullanici_id) {
    die("Kullanıcı bilgisi eksik, lütfen tekrar giriş yapınız.");
}

// Admin ise kullanici_id null olabilir, ama kullanıcıysa kesin bir ID olmalı
if (!$kullanici_id) {
    die("Kullanıcı ID bulunamadı, işlem iptal edildi.");
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "Geçersiz veya eksik ID.";
    exit();
}

$id = intval($_GET['id']);

$stmt = $baglanti->prepare("SELECT * FROM kiralama WHERE kiralama_id = ?");
$stmt->execute([$id]);
$arac = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$arac) {
    echo "Araç bulunamadı.";
    exit();
}

// Kiralama fotoğraflarını çek
$fotolar = [];
$fotoSorgu = $baglanti->prepare("SELECT * FROM kiralama_foto WHERE kiralama_id = ?");
$fotoSorgu->execute([$id]);
$fotolar = $fotoSorgu->fetchAll(PDO::FETCH_ASSOC);

// Form gönderildiğinde kiralama işlemini kaydet
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['kirala'])) {
    $kiralama_tarihi = $_POST['kiralama_tarihi'];
    $teslim_tarihi = $_POST['teslim_tarihi'];

    // Tarihlerin geçerli olup olmadığını kontrol et
    if (strtotime($teslim_tarihi) < strtotime($kiralama_tarihi)) {
        echo "<script>alert('Teslim tarihi, kiralama tarihinden küçük olamaz!');</script>";
    } else {
        // Aynı araç için tarih aralığında başka bir kiralama var mı kontrolü
        $kontrol = $baglanti->prepare("
            SELECT COUNT(*) FROM kiralanan_araclar
            WHERE kiralama_id = ?
            AND NOT (
                teslim_tarihi < ? OR kiralama_tarihi > ?
            )
        ");
        $kontrol->execute([$id, $kiralama_tarihi, $teslim_tarihi]);
        $varMi = $kontrol->fetchColumn();

        if ($varMi > 0) {
            echo "<script>alert('Seçilen tarihler arasında araç zaten kiralanmış. Lütfen farklı tarih seçiniz.');</script>";
        } else {
            $toplam_fiyat = isset($_POST['toplam_fiyat']) ? floatval(str_replace(',', '.', $_POST['toplam_fiyat'])) : 0;

            $insert = $baglanti->prepare("INSERT INTO kiralanan_araclar (kullanici_id, kiralama_id, kiralama_tarihi, teslim_tarihi, toplam_fiyat) VALUES (?, ?, ?, ?, ?)");
            $insert->execute([$kullanici_id, $id, $kiralama_tarihi, $teslim_tarihi, $toplam_fiyat]);

            echo "<script>alert('Araç başarıyla kiralandı!'); window.location.href='rent.php';</script>";
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <title><?= htmlspecialchars($arac['baslik']) ?> - Araç Detayları</title>
    <style>
        body {
            background-color: #001f3f;
            color: #e0e7ff;
            font-family: Arial, sans-serif;
            padding: 30px;
        }
        .container {
            max-width: 700px;
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
        }
        .detail-item {
            margin: 12px 0;
            font-size: 18px;
        }
        .label {
            font-weight: bold;
            color: #80bfff;
        }
        button {
            margin-top: 20px;
            background-color: #3399ff;
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 7px;
            cursor: pointer;
            font-weight: bold;
            transition: background 0.3s ease;
        }
        button:hover {
            background-color: #1a75ff;
        }

        /* Modal stilleri */
        .modal {
            display: none;
            position: fixed;
            z-index: 10;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.6);
        }
        .modal-content {
            background-color: #003366;
            margin: 10% auto;
            padding: 20px;
            border-radius: 10px;
            width: 400px;
            color: white;
            box-shadow: 0 0 10px #3399ff;
        }
        .modal input[type="date"] {
            width: 100%;
            padding: 8px;
            margin: 10px 0;
            border-radius: 5px;
            border: none;
        }
        .close {
            float: right;
            font-size: 28px;
            font-weight: bold;
            color: white;
            cursor: pointer;
        }

        /* Fotoğraf slider stilleri */
        .slider-container {
            margin: 20px 0;
            overflow-x: auto;
            white-space: nowrap;
            border-radius: 8px;
            padding-bottom: 10px;
            background-color: #003366;
        }

        .slider-img {
            height: 120px;
            margin-right: 10px;
            cursor: pointer;
            border-radius: 6px;
            transition: transform 0.3s ease;
        }
        .slider-img:hover {
            transform: scale(1.1);
        }

        /* Modal büyük fotoğraf için */
        .photo-modal {
            display: none;
            position: fixed;
            z-index: 9999;
            padding-top: 60px;
            left: 0; top: 0;
            width: 100%; height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.9);
        }
        .photo-modal .modal-content {
            margin: auto;
            display: block;
            max-width: 90%;
            max-height: 80vh;
            border-radius: 10px;
            box-shadow: 0 0 20px #3399ff;
        }
        .photo-modal .close {
            position: absolute;
            top: 30px;
            right: 40px;
            color: white;
            font-size: 40px;
            font-weight: bold;
            cursor: pointer;
            user-select: none;
        }
        .photo-modal .close:hover {
            color: #3399ff;
        }
    </style>
</head>
<body>
    <div class="container">

        <?php if (count($fotolar) > 0): ?>
            <div class="slider-container">
                <div class="slider">
                <?php foreach ($fotolar as $index => $foto): ?>
    <?php 
        // Fotoğraf binary verisini base64 encode et
        $base64 = base64_encode($foto['foto_data']); 
    ?>
    <img src="data:image/jpeg;base64,<?= $base64 ?>" 
         alt="Araç Fotoğrafı <?= $index + 1 ?>" 
         class="slider-img" 
         onclick="openModal(<?= $index ?>)" />
<?php endforeach; ?>


                </div>
            </div>
        <?php endif; ?>

        <div class="detail-item"><span class="label">Marka:</span> <?= htmlspecialchars($arac['kiralik_marka']) ?></div>
        <div class="detail-item"><span class="label">Model:</span> <?= htmlspecialchars($arac['kiralik_model']) ?></div>
        <div class="detail-item"><span class="label">Açıklama:</span> <?= nl2br(htmlspecialchars($arac['aciklama'])) ?></div>
        <div class="detail-item"><span class="label">Vites:</span> <?= htmlspecialchars($arac['vites']) ?></div>
        <div class="detail-item"><span class="label">Yıl:</span> <?= htmlspecialchars($arac['yil']) ?></div>
        <div class="detail-item"><span class="label">Günlük Fiyat:</span> <?= number_format($arac['gunluk_fiyat'], 2, ',', '.') ?> ₺</div>
        <div class="detail-item"><span class="label">Aylık Fiyat:</span> <?= number_format($arac['aylik_fiyat'], 2, ',', '.') ?> ₺</div>

        <button onclick="window.history.back();">Geri Dön</button>

        <?php if ($isUser): ?>
            <button onclick="document.getElementById('kiralaModal').style.display='block'">Kirala</button>
        <?php endif; ?>
    </div>

    <!-- Kirala Modal -->
    <div id="kiralaModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="document.getElementById('kiralaModal').style.display='none'">&times;</span>
            <form method="post" onsubmit="return hesaplaToplamFiyat()">
            <input type="date" id="kiralama_tarihi" name="kiralama_tarihi" required onchange="hesaplaToplamFiyat()" />               
                <label for="teslim_tarihi">Teslim Tarihi:</label><br />
                <input type="date" id="teslim_tarihi" name="teslim_tarihi" required onchange="hesaplaToplamFiyat()" />
                <label>Toplam Fiyat (₺):</label><br />
                <span id="toplam_fiyat_span"></span>
                <input type="hidden" id="toplam_fiyat" name="toplam_fiyat" />

                <button type="submit" name="kirala">Kirala</button>
            </form>
        </div>
    </div>

    <!-- Büyük fotoğraf modalı -->
    <div id="photoModal" class="photo-modal">
        <span class="close" onclick="closeModal()">&times;</span>
        <img class="modal-content" id="modalImage" />
    </div>

    <script>
        // Kirala modal kapatma
        window.onclick = function(event) {
            let modal = document.getElementById('kiralaModal');
            if (event.target == modal) {
                modal.style.display = "none";
            }
            let photoModal = document.getElementById('photoModal');
            if(event.target == photoModal){
                closeModal();
            }
        };

        // Toplam fiyat hesaplama fonksiyonu
        function hesaplaToplamFiyat() {
            const gunlukFiyat = <?= floatval($arac['gunluk_fiyat']) ?>;
            const kiralamaTarihi = new Date(document.getElementById('kiralama_tarihi').value);
            const teslimTarihi = new Date(document.getElementById('teslim_tarihi').value);

            if (teslimTarihi < kiralamaTarihi) {
            alert("Teslim tarihi, kiralama tarihinden küçük olamaz!");
            return false;
        }

        const fark = Math.ceil((teslimTarihi - kiralamaTarihi) / (1000 * 60 * 60 * 24)) + 1; // gün sayısı dahil
        const toplam = fark * gunlukFiyat;

        document.getElementById('toplam_fiyat_span').innerText = toplam.toFixed(2).replace('.', ',') + ' ₺';
        document.getElementById('toplam_fiyat').value = toplam.toFixed(2);
        return true;
    }

    // Fotoğraf modal açma/kapatma
    const modal = document.getElementById('photoModal');
    const modalImg = document.getElementById('modalImage');

    function openModal(index) {
        const images = document.querySelectorAll('.slider-img');
        modal.style.display = "block";
        modalImg.src = images[index].src;
    }

    function closeModal() {
        modal.style.display = "none";
    }
    
</script>
</body>
</html>

