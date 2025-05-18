<?php
session_start();
require_once "veritabani.php";

if (!isset($_SESSION["admin"])) {
    header("Location: index.php");
    exit();
}

// Silme işlemi AJAX ile buradan yönetilecek
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["sil_id"])) {
    $sil_id = intval($_POST["sil_id"]);
    $sil = $baglanti->prepare("DELETE FROM kiralanan_araclar WHERE id = ?");
    $sil->execute([$sil_id]);
    echo "ok";
    exit();
}

// Listeleme
$sorgu = $baglanti->query("
    SELECT ka.id, ka.kullanici_id, ka.kiralama_id, ka.kiralama_tarihi, ka.teslim_tarihi, ka.toplam_fiyat,
           CONCAT(k.ad, ' ', k.soyad) AS kullanici_adi
    FROM kiralanan_araclar ka
    LEFT JOIN kullanicilar k ON ka.kullanici_id = k.kullanici_id
    ORDER BY ka.kiralama_tarihi DESC
");
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kiralama Yönetimi</title>
    <style>
        body {
            background-color: #0a0f2c;
            color: #f0f0f0;
            font-family: Arial, sans-serif;
            padding: 30px;
        }

        h1 {
            color: #00adb5;
            text-align: center;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #1f1f1f;
        }

        th, td {
            padding: 12px 15px;
            border: 1px solid #333;
            text-align: center;
        }

        th {
            background-color: #00adb5;
            color: #fff;
        }

        tr:hover {
            background-color: #2c2c2c;
        }

        .btn {
            padding: 6px 12px;
            background-color: #ff4d4d;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn:hover {
            background-color: #ff1a1a;
        }

        /* Geri Dön Butonu Stili */
        .geri-don-btn {
            padding: 10px 20px;
            background-color: #00adb5;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
        }

        .geri-don-btn:hover {
            background-color: #008f94;
        }
    </style>

    <script>
        function kiralamayiIptalEt(id) {
            if (confirm("Bu kiralamayı iptal etmek istediğine emin misin?")) {
                const formData = new FormData();
                formData.append("sil_id", id);

                fetch("rent-yonet.php", {
                    method: "POST",
                    body: formData
                })
                .then(response => response.text())
                .then(cevap => {
                    if (cevap.trim() === "ok") {
                        alert("Kiralama başarıyla iptal edildi.");
                        document.getElementById("satir-" + id).remove();
                    } else {
                        alert("Silme işlemi başarısız.");
                    }
                });
            }
        }
    </script>
</head>
<body>

    <h1>📋 Kiralanan Araçlar</h1>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Kullanıcı</th>
                <th>Kullanıcı ID</th>
                <th>Kiralama ID</th>
                <th>Kiralama Tarihi</th>
                <th>Teslim Tarihi</th>
                <th>Toplam Fiyat</th>
                <th>İşlem</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($satir = $sorgu->fetch(PDO::FETCH_ASSOC)): ?>
                <tr id="satir-<?= htmlspecialchars($satir["id"]) ?>">
                    <td><?= htmlspecialchars($satir["id"]) ?></td>
                    <td><?= htmlspecialchars($satir["kullanici_adi"] ?? "Bilinmiyor") ?></td>
                    <td><?= htmlspecialchars($satir["kullanici_id"]) ?></td>
                    <td><?= htmlspecialchars($satir["kiralama_id"]) ?></td>
                    <td><?= htmlspecialchars($satir["kiralama_tarihi"]) ?></td>
                    <td><?= htmlspecialchars($satir["teslim_tarihi"]) ?></td>
                    <td><?= number_format($satir["toplam_fiyat"], 2) ?> ₺</td>
                    <td>
                        <button class="btn" onclick="kiralamayiIptalEt(<?= htmlspecialchars($satir['id']) ?>)">İptal Et</button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div style="text-align: center;">
        <button class="geri-don-btn" onclick="window.location.href='admin-panel.php'">Geri Dön</button>
    </div>

</body>
</html>
