<?php
session_start();
require_once "veritabani.php";

if (!isset($_SESSION["admin"])) {
    header("Location: index.php");
    exit();
}

$stmt = $baglanti->prepare("SELECT * FROM calisanlar");
$stmt->execute();
$calisanlar = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    // Çalışan bilgilerini güncelleme işlemi
    $id = $_POST['calisan_id'];
    $ad = $_POST['ad'];
    $soyad = $_POST['soyad'];
    $pozisyon = $_POST['pozisyon'];
    $email = $_POST['email'];
    $telefon = $_POST['telefon'];
    $maas = $_POST['maas'];

    try {
        $stmt = $baglanti->prepare("UPDATE calisanlar SET ad = ?, soyad = ?, pozisyon = ?, email = ?, telefon = ?, maas = ? WHERE calisan_id = ?");
        $stmt->execute([$ad, $soyad, $pozisyon, $email, $telefon, $maas, $id]);
        header("Location: calisanlar-listesi.php");
        exit();
    } catch (PDOException $e) {
        echo "Hata: " . $e->getMessage();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {
    // Çalışanı silme işlemi
    $id_to_delete = $_POST['delete_id'];

    try {
        $stmt = $baglanti->prepare("DELETE FROM calisanlar WHERE calisan_id = ?");
        $stmt->execute([$id_to_delete]);
        header("Location: calisanlar-listesi.php");
        exit();
    } catch (PDOException $e) {
        echo "Hata: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Çalışanlar Listesi</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #1b263b; /* Lacivert arka plan */
            padding: 30px;
            color: #ffffff;
        }

        h1 {
            text-align: center;
            color: #f8f9fa; /* Açık renk başlık */
        }

        .table-container {
            overflow-x: auto;
            background: white;
            border-radius: 8px;
            padding: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            color: #2c3e50;
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 12px;
            text-align: center;
        }

        th {
            background-color: #0d1b2a; /* Koyu lacivert başlık */
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f1f4f9;
        }

        a.button {
            padding: 6px 12px;
            color: white;
            background-color: #1d3557; /* Lacivert buton */
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        a.button:hover {
            background-color: #0b2545;
        }

        a.button.delete {
            background-color: #e74c3c;
        }

        a.button.delete:hover {
            background-color: #c0392b;
        }

        .add-button {
            display: inline-block;
            margin-bottom: 15px;
            background-color: #2a9d8f;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 6px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .add-button:hover {
            background-color: #21867a;
        }

        .back-button {
            display: inline-block;
            margin-top: 25px;
            background-color: #457b9d;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 6px;
            transition: background-color 0.3s;
        }

        .back-button:hover {
            background-color: #1d3557;
        }

        .back-container {
            text-align: center;
            margin-top: 20px;
        }

        .edit-form {
            display: none;
            background-color: white;
            padding: 20px;
            margin-top: 20px;
            box-shadow: 0 6px 12px rgba(0,0,0,0.1);
            border-radius: 8px;
            color: #2c3e50;
        }
    </style>
</head>
<body>
    

    <div class="table-container">
        <!-- Buraya tablo gelecek -->
    </div>

    
    </div>
</body>
</html>

    </style>
</head>
<body>
    <h1>Çalışanlar Listesi</h1>

    <!-- Geri Dön Butonu -->
    <div class="back-container">
        <a href="admin-panel.php" class="back-button">← Geri Dön</a>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Ad</th>
                <th>Soyad</th>
                <th>Pozisyon</th>
                <th>Email</th>
                <th>Telefon</th>
                <th>Maaş</th>
                <th>İşlemler</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($calisanlar as $c): ?>
            <tr>
                <td><?= $c["calisan_id"] ?></td>
                <td><?= $c["ad"] ?></td>
                <td><?= $c["soyad"] ?></td>
                <td><?= $c["pozisyon"] ?></td>
                <td><?= $c["email"] ?></td>
                <td><?= $c["telefon"] ?></td>
                <td><?= number_format($c["maas"], 2) ?> ₺</td>
                <td>
                    <!-- Düzenle butonu, formu gösteren JavaScript fonksiyonu ile tetiklenecek -->
                    <button class="button edit-btn" onclick="showEditForm(<?= $c['calisan_id'] ?>, '<?= $c['ad'] ?>', '<?= $c['soyad'] ?>', '<?= $c['pozisyon'] ?>', '<?= $c['email'] ?>', '<?= $c['telefon'] ?>', '<?= $c['maas'] ?>')">Düzenle</button>

                    <!-- Silme işlemi için POST formu -->
                    <form method="post" style="display:inline;" onsubmit="return confirmDelete()">
                        <input type="hidden" name="delete_id" value="<?= $c['calisan_id'] ?>">
                        <button type="submit" class="button delete">Sil</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Düzenleme Formu -->
    <div class="edit-form" id="edit-form">
        <h2>Çalışan Bilgilerini Düzenle</h2>
        <form method="post" action="">
            <input type="hidden" name="calisan_id" id="calisan_id">
            <div class="form-group">
                <label>Ad:</label>
                <input type="text" name="ad" id="ad" required>
            </div>
            <div class="form-group">
                <label>Soyad:</label>
                <input type="text" name="soyad" id="soyad" required>
            </div>
            <div class="form-group">
                <label>Pozisyon:</label>
                <input type="text" name="pozisyon" id="pozisyon">
            </div>
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" id="email">
            </div>
            <div class="form-group">
                <label>Telefon:</label>
                <input type="text" name="telefon" id="telefon">
            </div>
            <div class="form-group">
                <label>Maaş:</label>
                <input type="number" name="maas" id="maas" step="0.01">
            </div>
            <button type="submit" name="update">Güncelle</button>
        </form>
    </div>

    <script>
        function showEditForm(id, ad, soyad, pozisyon, email, telefon, maas) {
            // Formu görünür yap
            document.getElementById('edit-form').style.display = 'block';

            // Çalışan bilgilerini forma yerleştir
            document.getElementById('calisan_id').value = id;
            document.getElementById('ad').value = ad;
            document.getElementById('soyad').value = soyad;
            document.getElementById('pozisyon').value = pozisyon;
            document.getElementById('email').value = email;
            document.getElementById('telefon').value = telefon;
            document.getElementById('maas').value = maas;
        }

        function confirmDelete() {
            return confirm("Bu çalışanı silmek istediğinizden emin misiniz?");
        }
    </script>
</body>
</html>
