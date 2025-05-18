<?php
require_once "veritabani.php";

if (!isset($_GET['id'])) {
    echo "Geçersiz istek.";
    exit();
}

$id = intval($_GET['id']);

$sorgu = $baglanti->prepare("SELECT * FROM arac_ilanlari WHERE id = ?");
$sorgu->execute([$id]);
$arac = $sorgu->fetch(PDO::FETCH_ASSOC);

if (!$arac) {
    echo "Araç bulunamadı.";
    exit();
}

$fotosorgu = $baglanti->prepare("SELECT foto FROM arac_ilanlari_foto WHERE ilan_no = ?");
$fotosorgu->execute([$arac['ilan_no']]);
$fotolar = $fotosorgu->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <title>Araç Detayı</title>
    <style>
        /* Genel gövde arka plan koyu lacivert */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #001e3c; /* Koyu lacivert */
            margin: 0;
            padding: 40px 0;
            color: #ffffff; /* Tüm metinler beyaz */
        }

        /* Ana kapsayıcı kutu */
        .container {
            background: #003366; /* Orta koyulukta mavi */
            padding: 30px 40px;
            max-width: 900px;
            margin: auto;
            border-radius: 15px;
            box-shadow: 0 0 20px #001e3caa;
            border: 1.5px solid #004080;
        }

        h2 {
            text-align: center;
            color: #99ccff; /* Açık mavi başlık */
            margin-bottom: 30px;
            font-size: 30px;
            font-weight: 700;
            text-shadow: 1px 1px 4px #0008;
        }

        /* Resim galeri bölümü */
        .gallery {
            display: flex;
            overflow-x: auto;
            gap: 15px;
            padding: 15px 0 25px;
            border-bottom: 2px solid #004080aa;
            margin-bottom: 30px;
        }

        .gallery img {
            height: 220px;
            border-radius: 12px;
            border: 2.5px solid #0059b3;
            object-fit: cover;
            filter: drop-shadow(0 0 6px #003366);
            cursor: pointer;
            transition: transform 0.3s ease;
        }
        .gallery img:hover {
            transform: scale(1.05);
        }

        /* Detay satırları */
        .detail {
            margin-bottom: 16px;
            font-size: 18px;
            padding: 12px 18px;
            background-color: #004080cc; /* Transparan koyu mavi kutu */
            border-radius: 10px;
            box-shadow: inset 0 0 10px #001f3f;
        }

        /* Etiketler (Marka, Model vs) */
        .label {
            font-weight: 700;
            color: #a3d2ff; /* Açık mavi */
        }

        /* Geri butonu */
        a {
            display: block;
            margin-top: 35px;
            text-align: center;
            background-color: #0066cc;
            color: #e0f0ff;
            padding: 15px 0;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 700;
            font-size: 18px;
            box-shadow: 0 0 12px #0050a0aa;
            transition: background-color 0.3s ease;
        }
        a:hover {
            background-color: #004080;
            box-shadow: 0 0 20px #002f5fbb;
        }

        /* Scrollbar mavi tonları */
        ::-webkit-scrollbar {
            height: 9px;
        }
        ::-webkit-scrollbar-thumb {
            background-color: #0059b3cc;
            border-radius: 5px;
        }
        ::-webkit-scrollbar-track {
            background-color: #001e3c;
        }
    </style>
</head>
<body>

<div class="container">
    <h2><?= htmlspecialchars($arac['arac_baslik']) ?></h2>

    <?php if (count($fotolar) > 0): ?>
        <div class="gallery">
            <?php foreach ($fotolar as $foto): ?>
                <img src="data:image/jpeg;base64,<?= base64_encode($foto['foto']) ?>" alt="Araç Fotoğrafı" />
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p style="text-align:center; color: #99bbffaa;">Fotoğraf bulunamadı.</p>
    <?php endif; ?>

    <div class="detail"><span class="label">Marka:</span> <?= htmlspecialchars($arac['marka']) ?></div>
    <div class="detail"><span class="label">Model:</span> <?= htmlspecialchars($arac['model']) ?></div>
    <div class="detail"><span class="label">Yıl:</span> <?= htmlspecialchars($arac['yil']) ?></div>
    <div class="detail"><span class="label">Vites Türü:</span> <?= htmlspecialchars($arac['vites']) ?></div>
    <div class="detail"><span class="label">Yakıt Türü:</span> <?= htmlspecialchars($arac['yakit']) ?></div>
    <div class="detail"><span class="label">Fiyat:</span> <?= htmlspecialchars($arac['fiyat']) ?> ₺</div>
    <div class="detail"><span class="label">Motor Gücü:</span> <?= htmlspecialchars($arac['motor_gucu']) ?></div>
    <div class="detail"><span class="label">Motor Hacmi:</span> <?= htmlspecialchars($arac['motor_hacmi']) ?> cc</div>
    <div class="detail"><span class="label">Kilometre:</span> <?= htmlspecialchars($arac['km']) ?> km</div>
    <div class="detail"><span class="label">Kasa Tipi:</span> <?= htmlspecialchars($arac['kasa_tipi']) ?></div>
    <div class="detail"><span class="label">Renk:</span> <?= htmlspecialchars($arac['renk']) ?></div>

    <a href="javascript:history.back()">◀️ Geri Dön</a>
</div>
<!-- Modal yapısı -->
<div id="fotoModal" class="modal">
  <span class="close">&times;</span>
  <img class="modal-content" id="modalImg" alt="Büyük Araç Fotoğrafı">
</div>

<style>
/* Modal arkaplan ve yerleşim */
.modal {
  display: none; 
  position: fixed; 
  z-index: 9999; 
  padding-top: 60px; 
  left: 0;
  top: 0;
  width: 100%; 
  height: 100%; 
  overflow: auto; 
  background-color: rgba(0, 0, 0, 0.85);
}

/* Modal içindeki büyük resim */
.modal-content {
  margin: auto;
  display: block;
  max-width: 90%;
  max-height: 80vh;
  border-radius: 12px;
  box-shadow: 0 0 25px #00aaffbb;
  animation: zoomIn 0.3s ease forwards;
}

@keyframes zoomIn {
  from {transform: scale(0.7);}
  to {transform: scale(1);}
}

/* Kapatma butonu (X) */
.close {
  position: fixed;
  top: 25px;
  right: 35px;
  color: #ffffffcc;
  font-size: 40px;
  font-weight: bold;
  cursor: pointer;
  transition: color 0.3s ease;
  user-select: none;
}
.close:hover {
  color: #00aaff;
}
</style>

<script>
// Modal açma kapama işlemleri
document.addEventListener('DOMContentLoaded', function () {
  const modal = document.getElementById('fotoModal');
  const modalImg = document.getElementById('modalImg');
  const closeBtn = document.querySelector('.close');

  // Galeri içindeki tüm img öğelerine tıklama eventi ekle
  document.querySelectorAll('.gallery img').forEach(img => {
    img.addEventListener('click', () => {
      modal.style.display = 'block';
      modalImg.src = img.src;
      modalImg.alt = img.alt || 'Araç Fotoğrafı Büyük';
      // Body scroll kapatma
      document.body.style.overflow = 'hidden';
    });
  });

  // Kapatma butonuna tıklayınca modalı kapat
  closeBtn.addEventListener('click', () => {
    modal.style.display = 'none';
    document.body.style.overflow = '';
  });

  // Modal dışına tıklayınca kapat
  modal.addEventListener('click', (e) => {
    if (e.target === modal) {
      modal.style.display = 'none';
      document.body.style.overflow = '';
    }
  });

  // ESC tuşu ile kapatma
  document.addEventListener('keydown', (e) => {
    if (e.key === "Escape" && modal.style.display === 'block') {
      modal.style.display = 'none';
      document.body.style.overflow = '';
    }
  });
});
</script>



</body>
</html>
