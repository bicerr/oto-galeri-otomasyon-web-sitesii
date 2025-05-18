<?php
session_start();
include 'veritabani.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  file_put_contents('log.txt', print_r($_POST, true));
}

$isAdmin = isset($_SESSION['admin']) && $_SESSION['admin'] === true;
$isUser = isset($_SESSION['kullanici_adi']) && !empty($_SESSION['kullanici_adi']);

if (!$isAdmin && !$isUser) {
    header("Location: giris.php");
    exit();
}

// SILME ISLEMI
if ($_GET['action'] === 'delete' && isset($_GET['id'])) {
  $id = intval($_GET['id']);
  try {
      $stmt = $baglanti->prepare("DELETE FROM arac_ilanlari WHERE id = ?");
      $stmt->execute([$id]);
      echo "<script>alert('Başarıyla silindi!');window.location.href='araba-listesi.php';</script>";
  } catch (Exception $e) {
      echo "<script>alert('Hata: {$e->getMessage()}');</script>";
  }
  exit();
}

// GUNCELLEME ISLEMI
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && !isset($_POST['sil_id'])) {
    $id = $_POST['id'];
    $marka = $_POST['marka'];
    $model = $_POST['model'];
    $yil = $_POST['yil'];
    $vites = $_POST['vites'];
    $yakit = $_POST['yakit'];
    $fiyat = $_POST['fiyat'];
    $km = $_POST['km'];
    $kasa_tipi = $_POST['kasa_tipi'];
    $motor_gucu = $_POST['motor_gucu'];
    $motor_hacmi = $_POST['motor_hacmi'];
    $renk = $_POST['renk'];

    $updateSql = "UPDATE arac_ilanlari SET 
        marka = :marka,
        model = :model,
        yil = :yil,
        vites = :vites,
        yakit = :yakit,
        fiyat = :fiyat,
        km = :km,
        kasa_tipi = :kasa_tipi,
        motor_gucu = :motor_gucu,
        motor_hacmi = :motor_hacmi,
        renk = :renk
        WHERE id = :id";

    $stmtUpdate = $baglanti->prepare($updateSql);
    $result = $stmtUpdate->execute([
        ':marka' => $marka,
        ':model' => $model,
        ':yil' => $yil,
        ':vites' => $vites,
        ':yakit' => $yakit,
        ':fiyat' => $fiyat,
        ':km' => $km,
        ':kasa_tipi' => $kasa_tipi,
        ':motor_gucu' => $motor_gucu,
        ':motor_hacmi' => $motor_hacmi,
        ':renk' => $renk,
        ':id' => $id
    ]);

    echo json_encode(['success' => $result]);
    exit();
}

// FILTRELEME
$conditions = [];
$params = [];

foreach (['kasa_tipi','renk','motor_gucu','motor_hacmi','marka','model','yil','vites','yakit'] as $field) {
    if (!empty($_GET[$field])) {
        $conditions[] = "$field = :$field";
        $params[":$field"] = $_GET[$field];
    }
}
foreach (['fiyat','km'] as $numField) {
    if (!empty($_GET[$numField . '_min'])) {
        $conditions[] = "$numField >= :{$numField}_min";
        $params[":{$numField}_min"] = $_GET[$numField . '_min'];
    }
    if (!empty($_GET[$numField . '_max'])) {
        $conditions[] = "$numField <= :{$numField}_max";
        $params[":{$numField}_max"] = $_GET[$numField . '_max'];
    }
}

$sql = "SELECT * FROM arac_ilanlari";
if ($conditions) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}
$stmt = $baglanti->prepare($sql);
$stmt->execute($params);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Araba Listesi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>


    /* Tablo gövdesi */
    .custom-table {
      border-collapse: separate !important;
      border-spacing: 0 12px;
      background: transparent;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      font-weight: 600;
      font-size: 16px;
      color: #1a1a1a;
    }

    .custom-title {
      font-weight: 900;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      color: rgba(255, 255, 255, 0.85); /* Hafif koyu beyaz */
      text-shadow: 1px 1px 3px #4364F7;
    }

    /* Başlık satırı */
    

    .custom-table thead th {
      border: none !important;
      padding: 12px 20px;
      text-align: center;
      text-transform: uppercase;
      letter-spacing: 0.1em;
      user-select: none;
    }

    /* Gövde satırları */
    .custom-table tbody tr {
      background-color: #001e3c; /* Koyu lacivert */
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
      transition: all 0.3s ease;
      border-radius: 10px;
      cursor: pointer;
    }

    /* Hover efekti */
    .custom-table tbody tr:hover {
      background:rgb(0, 40, 219);
      color: #fff;
      box-shadow: 0 8px 20px rgba(67, 100, 247, 0.6);
      transform: translateY(-4px);
    }

    /* Hücreler */
    .custom-table tbody td {
      vertical-align: middle !important;
      padding: 14px 15px;
      border: none !important;
      text-align: center;
      transition: color 0.3s ease;
      user-select: text;
    }

    /* ID hücresi admin için */
    .custom-table tbody td:first-child {
      font-weight: 700;
      color: #0052D4;
    }

    /* Butonlar */
    .custom-table tbody td .btn {
      font-weight: 600;
      padding: 6px 12px;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(67, 100, 247, 0.3);
      transition: background-color 0.3s ease;
    }

    .custom-table tbody td .btn-info {
      background-color: #4364F7;
      border: none;
    }

    .custom-table tbody td .btn-info:hover {
      background-color: #2948c3;
    }

    .custom-table tbody td .btn-warning {
      background-color: #f5a623;
      border: none;
      color: #222;
    }

    .custom-table tbody td .btn-warning:hover {
      background-color: #cc8400;
      color: #fff;
    }

    .custom-table tbody td .btn-danger {
      background-color: #e84141;
      border: none;
    }

    .custom-table tbody td .btn-danger:hover {
      background-color: #b33333;
    }

    /* Responsive küçük ekranlarda yazı küçült */
    @media (max-width: 768px) {
      .custom-table tbody td {
        font-size: 13px;
        padding: 10px 8px;
      }
      .custom-table thead th {
        font-size: 12px;
      }
    }

    /* Container ve başlık stilleri */
    .container {
  max-width: 100%;
  overflow-x: auto;
}

    .btn-back {
    background: rgba(255, 255, 255, 0.15); /* Hafif koyu beyaz, yarı şeffaf */
    color: #f0f0f0;
    font-weight: 700;
    padding: 12px 30px;
    border-radius: 12px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    transition: background-color 0.3s ease, color 0.3s ease, box-shadow 0.3s ease;
    text-decoration: none;
    display: inline-block;
    user-select: none;
}

.btn-back:hover, 
.btn-back:focus {
    background: rgba(255, 255, 255, 0.35);
    color: #1a1a1a;
    box-shadow: 0 6px 20px rgba(67, 100, 247, 0.6);
    text-decoration: none;
}


   h2.text-primary {
   font-weight: 900;
   font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
   color: rgba(255, 255, 255, 0.85); /* Hafif koyu beyaz */
   text-shadow: 1px 1px 3px #4364F7;
    }

    

    </style>
</head>
<body style="
  background-color: #001e3c; /* Koyu lacivert */
  color: #f0f0f0;
  padding: 20px;
  animation: bgPulse 4s ease-in-out infinite;
">
<div class="container">
    <div class="mb-3 text-end">
        <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#filtreModal">Filtreleme</button>
        <?php if (!empty($_GET)) : ?>
            <a href="araba-listesi.php" class="btn btn-danger">Filtrelemeyi Kaldır</a>
        <?php endif; ?>
    </div>

<h2 class="text-center custom-title mb-4">Satılık Arabalar</h2>

    <table class="custom-table table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <?php if ($isAdmin): ?><th>ID</th><?php endif; ?>
                <th>Marka</th>
                <th>Model</th>
                <th>Yıl</th>
                <th>Vites</th>
                <th>Yakıt</th>
                <th>Fiyat</th>
                <th>KM</th>
                <th>Kasa Tipi</th>
                <th>Motor Gücü</th>
                <th>Motor Hacmi</th>
                <th>Renk</th>
                <th>İşlem</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
        <tr id="row-<?= $row['id'] ?>">
            <?php if ($isAdmin): ?><td><?= $row['id'] ?></td><?php endif; ?>
            <td class="marka"><?= htmlspecialchars($row['marka']) ?></td>
            <td class="model"><?= htmlspecialchars($row['model']) ?></td>
            <td class="yil"><?= htmlspecialchars($row['yil']) ?></td>
            <td class="vites"><?= htmlspecialchars($row['vites']) ?></td>
            <td class="yakit"><?= htmlspecialchars($row['yakit']) ?></td>
            <td class="fiyat"><?= htmlspecialchars($row['fiyat']) ?></td>
            <td class="km"><?= htmlspecialchars($row['km']) ?></td>
            <td class="kasa_tipi"><?= htmlspecialchars($row['kasa_tipi']) ?></td>
            <td class="motor_gucu"><?= htmlspecialchars($row['motor_gucu']) ?></td>
            <td class="motor_hacmi"><?= htmlspecialchars($row['motor_hacmi']) ?></td>
            <td class="renk"><?= htmlspecialchars($row['renk']) ?></td>
            <td>
                <?php if (!$isAdmin): ?>
                    <a href="araba-detay.php?id=<?= $row['id'] ?>" class="btn btn-info btn-sm">Detay</a>
                <?php endif; ?>
                <?php if($isAdmin): ?>
                    <button class="btn btn-warning btn-sm editBtn" data-id="<?= $row['id'] ?>" data-bs-toggle="modal" data-bs-target="#editModal">Düzenle</button>
                    <a href="araba-listesi.php?action=delete&id=<?= $row['id'] ?>" class="btn btn-danger btn-sm">Sil</a>                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<div class="text-center mt-4">
    <a href="<?php echo $isAdmin ? 'admin-panel.php' : 'kullanici-panel.php'; ?>" 
       class="btn btn-back">Geri Dön</a>
</div>


<!-- Filtreleme Modal -->
<div class="modal fade" id="filtreModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="get" action="">
        <div class="modal-header">
          <h5 class="modal-title">Araç Filtreleme</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <?php foreach (['marka','model','yil','vites','yakit','kasa_tipi','renk','motor_gucu','motor_hacmi'] as $field): ?>
              <div class="col-md-4"><input type="text" name="<?= $field ?>" class="form-control" placeholder="<?= ucfirst($field) ?>"></div>
            <?php endforeach; ?>
            <div class="col-md-6"><input type="number" name="fiyat_min" class="form-control" placeholder="Min Fiyat"></div>
            <div class="col-md-6"><input type="number" name="fiyat_max" class="form-control" placeholder="Max Fiyat"></div>
            <div class="col-md-6"><input type="number" name="km_min" class="form-control" placeholder="Min KM"></div>
            <div class="col-md-6"><input type="number" name="km_max" class="form-control" placeholder="Max KM"></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary w-100">Filtrele</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Düzenleme Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="editForm">
        <input type="hidden" name="id" id="edit-id">
        <div class="modal-header">
          <h5 class="modal-title">Düzenle</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body row g-2">
          <?php foreach ([
            'marka' => 'Marka',
            'model' => 'Model',
            'yil' => 'Yıl',
            'vites' => 'Vites',
            'yakit' => 'Yakıt',
            'fiyat' => 'Fiyat',
            'km' => 'KM',
            'kasa_tipi' => 'Kasa Tipi',
            'motor_gucu' => 'Motor Gücü',
            'motor_hacmi' => 'Motor Hacmi',
            'renk' => 'Renk'
          ] as $name => $label): ?>
          <div class="col-md-6">
            <input type="<?= is_numeric($label) ? 'number' : 'text' ?>" class="form-control" name="<?= $name ?>" id="edit-<?= $name ?>" placeholder="<?= $label ?>" required>
          </div>
          <?php endforeach; ?>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary w-100">Kaydet</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
    $('.editBtn').click(function() {
        var row = $(this).closest('tr');
        $('#edit-id').val($(this).data('id'));
        ['marka','model','yil','vites','yakit','fiyat','km','kasa_tipi','motor_gucu','motor_hacmi','renk'].forEach(function(field) {
            $('#edit-' + field).val(row.find('.' + field).text().trim());
        });
    });

    $('#editForm').submit(function(e) {
        e.preventDefault();
        $.post('araba-listesi.php', $(this).serialize(), function(res) {
            if (res.success) {
                alert('Başarıyla güncellendi.');
                location.reload();
            } else {
                alert('Güncelleme başarısız.');
            }
        }, 'json').fail(function() {
            alert('Sunucu hatası.');
        });
    });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.sil-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm('Bu aracı silmek istediğinizden emin misiniz?')) {
                const row = this.closest('tr');
                const id = this.getAttribute('data-id');

                fetch('araba-listesi.php?action=delete&id=' + id)
    .then(response => response.text())
    .then(data => {
        console.log(data);
        row.remove();
        alert('Araç başarıyla silindi!');
    })
    .catch(err => {
        console.error(err);
        alert('Hata oluştu.');
    });
            }
        });
    });
});
</script>

</body>
</html>
