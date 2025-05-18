<?php
session_start();
require_once "veritabani.php";

$isAdmin = isset($_SESSION['admin']) && $_SESSION['admin'] === true;
$isUser = isset($_SESSION['kullanici_id']) && !empty($_SESSION['kullanici_id']);

if (!$isAdmin && !$isUser) {
    header("Location: giris.php");
    exit();
}


// Burada rent.php içeriği (normal kullanıcı için)




if (isset($_GET['action']) && $isAdmin) {
    header('Content-Type: application/json');

    if ($_GET['action'] === 'get' && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $stmt = $baglanti->prepare("SELECT * FROM kiralama WHERE kiralama_id = ?");
        $stmt->execute([$id]);
        $arac = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($arac) {
            echo json_encode(['success' => true, 'data' => $arac]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Araç bulunamadı']);
        }
        exit();
    }

    if ($_GET['action'] === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $kiralama_id = $_POST['kiralama_id'] ?? null;
        $kiralik_marka = $_POST['kiralik_marka'] ?? null;
        $kiralik_model = $_POST['kiralik_model'] ?? null;
        $aciklama = $_POST['aciklama'] ?? null;
        $vites = $_POST['vites'] ?? null;
        $yil = $_POST['yil'] ?? null;
        $gunluk_fiyat = $_POST['gunluk_fiyat'] ?? null;
        $aylik_fiyat = $_POST['aylik_fiyat'] ?? null;

        if (!$kiralama_id || !$kiralik_marka || !$kiralik_model || !$aciklama || !$vites || !$yil || !$gunluk_fiyat || !$aylik_fiyat) {
            echo json_encode(['success' => false, 'message' => 'Tüm alanları doldurun.']);
            exit();
        }

        try {
            $stmt = $baglanti->prepare("UPDATE kiralama SET kiralik_marka=?, kiralik_model=?, aciklama=?, vites=?, yil=?, gunluk_fiyat=?, aylik_fiyat=? WHERE kiralama_id=?");
            $stmt->execute([$kiralik_marka, $kiralik_model, $aciklama, $vites, $yil, $gunluk_fiyat, $aylik_fiyat, $kiralama_id]);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Veritabanı hatası: ' . $e->getMessage()]);
        }
        exit();
    }

    if ($_GET['action'] === 'delete' && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        try {
            // Önce bağlı kayıtları sil
            $stmt = $baglanti->prepare("DELETE FROM kiralanan_araclar WHERE kiralama_id = ?");
            $stmt->execute([$id]);
    
            // Sonra ana kaydı sil
            $stmt = $baglanti->prepare("DELETE FROM kiralama WHERE kiralama_id = ?");
            $stmt->execute([$id]);
    
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Silme işlemi başarısız: ' . $e->getMessage()]);
        }
        exit();
    }
    

    echo json_encode(['success' => false, 'message' => 'Geçersiz istek']);
    exit();
}

$stmt = $baglanti->query("SELECT * FROM kiralama");
$kiralik_araclar = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <title>Kiralık Araçlar</title>
    <style>
        body {
            background-color: #001f3f;
            color: #e0e7ff;
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            background-color: #00264d;
            color: #e0e7ff;
            box-shadow: 0 0 15px #3399ff;
        }
        th, td {
            border: 1px solid #3399ff;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #004080;
            font-weight: bold;
        }
        button {
            background-color: #3399ff;
            border: none;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: background 0.3s ease;
            margin: 0 2px;
        }
        button:hover {
            background-color: #1a75ff;
        }
        .modal {
          display: none;
          position: fixed;
          z-index: 1000;
          left: 0; top: 0;
          width: 100%; height: 100%;
          overflow: auto;
          background-color: rgba(0,0,0,0.6);
        }
        .modal-content {
          background-color: #00264d;
          margin: 10% auto;
          padding: 20px;
          border-radius: 10px;
          width: 400px;
          color: #e0e7ff;
          box-shadow: 0 0 10px #3399ff;
        }
        .close-btn {
          color: #aaa;
          float: right;
          font-size: 28px;
          font-weight: bold;
          cursor: pointer;
        }
        .close-btn:hover {
          color: #fff;
        }
        .modal input, .modal textarea, .modal select {
          width: 100%;
          padding: 8px;
          margin: 6px 0 12px;
          border: 1px solid #3399ff;
          border-radius: 5px;
          background-color: #001f3f;
          color: #e0e7ff;
        }
        .modal button {
          background-color: #3399ff;
          color: white;
          border: none;
          padding: 10px 15px;
          border-radius: 5px;
          cursor: pointer;
          font-weight: bold;
          transition: background 0.3s ease;
        }
        .modal button:hover {
          background-color: #1a75ff;
        }
    </style>
</head>
<body>

<button type="button" onclick="window.history.back();">Geri Dön</button>

<h1 style="text-align:center; margin-bottom: 30px;">Kiralık Araçlar</h1>

<div class="card-container">
    <?php if ($kiralik_araclar): ?>
        <?php foreach ($kiralik_araclar as $arac): ?>
            <div class="card">
                <h2><?= htmlspecialchars($arac['kiralik_marka']) ?> <?= htmlspecialchars($arac['kiralik_model']) ?></h2>
                <p><strong>Açıklama:</strong> <?= htmlspecialchars($arac['aciklama']) ?></p>
                <p><strong>Vites:</strong> <?= htmlspecialchars($arac['vites']) ?></p>
                <p><strong>Yıl:</strong> <?= htmlspecialchars($arac['yil']) ?></p>
                <p><strong>Günlük Fiyat:</strong> <?= number_format($arac['gunluk_fiyat'], 2, ',', '.') ?> ₺</p>
                <p><strong>Aylık Fiyat:</strong> <?= number_format($arac['aylik_fiyat'], 2, ',', '.') ?> ₺</p>
                <div class="buttons">
                    <form action="rent-detay.php" method="get" style="display:inline;">
                        <input type="hidden" name="id" value="<?= $arac['kiralama_id'] ?>">
                        <button type="submit">Detay</button>
                    </form>
                    <?php if($isAdmin): ?>
                        <button onclick="openModal(<?= $arac['kiralama_id'] ?>)">Düzenle</button>
                        <button onclick="silArac(<?= $arac['kiralama_id'] ?>)">Sil</button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>Kiralık araç bulunmamaktadır.</p>
    <?php endif; ?>
</div>

<style>
.card-container {
    display: flex;
    flex-direction: column;
    gap: 20px;
    max-width: 700px;
    margin: 0 auto;
}

.card {
    background-color: #00264d;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 0 15px #3399ff;
    color: #e0e7ff;
}

.card h2 {
    margin-top: 0;
    margin-bottom: 10px;
}

.card p {
    margin: 6px 0;
}

.buttons {
    margin-top: 15px;
}

.buttons button, .buttons form button {
    background-color: #3399ff;
    border: none;
    color: white;
    padding: 8px 15px;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
    transition: background 0.3s ease;
    margin-right: 10px;
}

.buttons button:hover, .buttons form button:hover {
    background-color: #1a75ff;
}
</style>


<style>
    h1 {
        text-align: center;
        margin-bottom: 30px;
    }
    table tr.arac-row {
        border-bottom: 2px solid #3399ff;
    }
    table tr.arac-row:last-child {
        border-bottom: none;
    }
    table tbody tr {
        transition: background-color 0.3s ease;
    }
    table tbody tr:hover {
        background-color: #003366;
    }
</style>


<div id="editModal" class="modal">
  <div class="modal-content">
    <span class="close-btn" onclick="closeModal()">&times;</span>
    <h2>Araç Düzenle</h2>
    <form id="editForm">
      <input type="hidden" name="kiralama_id" id="kiralama_id" />
      <label>Marka</label>
      <input type="text" name="kiralik_marka" id="kiralik_marka" required />
      
      <label>Model</label>
      <input type="text" name="kiralik_model" id="kiralik_model" required />
      
      
      
      <label>Açıklama</label>
      <textarea name="aciklama" id="aciklama" rows="3" required></textarea>
      
      <label>Vites</label>
      <select name="vites" id="vites" required>
        <option value="Otomatik">Otomatik</option>
        <option value="Manuel">Manuel</option>
        </select>
        <label>Yıl</label>
  <input type="number" name="yil" id="yil" min="1900" max="<?= date("Y") ?>" required />
  
  <label>Günlük Fiyat</label>
  <input type="number" name="gunluk_fiyat" id="gunluk_fiyat" step="0.01" min="0" required />
  
  <label>Aylık Fiyat</label>
  <input type="number" name="aylik_fiyat" id="aylik_fiyat" step="0.01" min="0" required />
  
  <button type="submit">Kaydet</button>
</form>
</div>
 </div> 
 <script> const modal = document.getElementById('editModal'); const editForm = document.getElementById('editForm'); function openModal(id) { fetch('?action=get&id=' + id) .then(res => res.json()) .then(data => { if (data.success) { const arac = data.data; document.getElementById('kiralama_id').value = arac.kiralama_id; document.getElementById('kiralik_marka').value = arac.kiralik_marka; document.getElementById('kiralik_model').value = arac.kiralik_model; 
 document.getElementById('aciklama').value = arac.aciklama; document.getElementById('vites').value = arac.vites; document.getElementById('yil').value = arac.yil; document.getElementById('gunluk_fiyat').value = arac.gunluk_fiyat; document.getElementById('aylik_fiyat').value = arac.aylik_fiyat; modal.style.display = 'block'; } else { alert(data.message || 'Araç bilgileri getirilemedi.'); } }) .catch(() => alert('Veri getirilirken hata oluştu.')); } function closeModal() { modal.style.display = 'none'; } editForm.addEventListener('submit', function(e) { e.preventDefault(); const formData = new FormData(editForm); fetch('?action=update', { method: 'POST', body: formData }) .then(res => res.json()) .then(data => { if (data.success) { alert('Araç bilgileri başarıyla güncellendi.'); closeModal(); location.reload(); } else { alert(data.message || 'Güncelleme başarısız.'); } }) .catch(() => alert('Güncelleme sırasında hata oluştu.')); }); window.onclick = function(event) { if (event.target === modal) { closeModal(); } }
  function silArac(id) {
     if (confirm('Silmek istediğinizden emin misiniz?'))
      { fetch('?action=delete&id=' + id) .then(res => res.json()) .then(data => { if (data.success) { alert('Araç başarıyla silindi.'); 
        location.reload(); } 
        else { alert(data.message || 'Silme işlemi başarısız oldu.'); } })
         .catch(() => alert('Silme sırasında hata oluştu.')); } } 
  </script> 
  </body> 
  </html>
