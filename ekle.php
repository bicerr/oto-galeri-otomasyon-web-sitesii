<?php
include 'veritabani.php';  // PDO bağlantısını içeren dosya

$successMessage = '';
$errorMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $marka = $_POST['marka'];
    $model = $_POST['model'];
    $yil = $_POST['yil'];
    $vites = $_POST['vites'];
    $yakit = $_POST['yakit'];
    $fiyat = $_POST['fiyat'];
    $km = $_POST['km'] ?? 0;
    $motor_gucu = $_POST['motor_gucu'] ?? 0;
    $motor_hacmi = $_POST['motor_hacmi'] ?? 0;
    $renk = $_POST['renk'] ?? '';
    $kasa_tipi = $_POST['kasa_tipi'] ?? '';

    $ilan_no = mt_rand(100000, 999999);
    $ilan_tarihi = date('Y-m-d');

    try {
        $stmt = $baglanti->prepare("INSERT INTO arac_ilanlari 
        (ilan_no, ilan_tarihi, marka, model, yil, yakit, vites, km, kasa_tipi, motor_gucu, motor_hacmi, renk, fiyat)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->execute([
            $ilan_no, $ilan_tarihi, $marka, $model, $yil, $yakit, $vites, $km,
            $kasa_tipi, $motor_gucu, $motor_hacmi, $renk, $fiyat
        ]);

        // FOTOĞRAF YÜKLEME
        if (!empty($_FILES['foto']['name'][0])) {
            foreach ($_FILES['foto']['tmp_name'] as $key => $tmp_name) {
                $foto = file_get_contents($tmp_name);
                $fotoStmt = $baglanti->prepare("INSERT INTO arac_ilanlari_foto (ilan_no, foto) VALUES (?, ?)");
                $fotoStmt->execute([$ilan_no, $foto]);
            }
        }

        $successMessage = '🚗 Araba başarıyla eklendi.';
    } catch (PDOException $e) {
        $errorMessage = 'Hata: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <title>Yeni Araba Ekle</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet" />
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #283E51, #486A8F);
            margin: 0;
            padding: 20px;
            color: #e0e7ff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .message {
            margin: 20px auto;
            padding: 15px;
            border-radius: 10px;
            font-weight: 600;
            text-align: center;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.3);
        }

        .success {
            background-color: #4caf50;
            color: white;
        }

        .error {
            background-color: #e53e3e;
            color: white;
        }

        .form-wrapper {
            max-width: 700px;
            width: 90vw;
            padding: 40px 45px 50px;
            background: rgba(20, 35, 60, 0.85);
            border-radius: 16px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(8px);
            opacity: 1;
            transform: translateY(0);
            color: #dbe9ff;
        }

        .form-wrapper h2 {
            text-align: center;
            margin-bottom: 30px;
            font-weight: 700;
            color: #a6c8ff;
            letter-spacing: 1.2px;
            text-shadow: 0 0 10px #7dafff99;
        }

        .input-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 18px 20px;
        }

        .input-grid input {
            width: 100%;
            padding: 14px 16px;
            border: 1.5px solid #5a7dbd;
            border-radius: 12px;
            font-size: 15px;
            background: rgba(40, 60, 100, 0.7);
            color: #dbe9ff;
            transition: border-color 0.3s ease, background-color 0.3s ease;
            box-shadow: inset 0 1px 4px rgba(255,255,255,0.1);
        }

        .input-grid input::placeholder {
            color: #aabde9cc;
        }

        .input-grid input:focus {
            border-color: #86a8ff;
            outline: none;
            background: rgba(70, 90, 150, 0.9);
            box-shadow: 0 0 12px #7dafffcc;
            color: #eef6ff;
        }

        .btn-submit {
            width: 100%;
            padding: 16px 0;
            background-color: rgba(255, 255, 255, 0.85);
            border: none;
            color: #283E51;
            font-weight: 700;
            font-size: 18px;
            border-radius: 14px;
            cursor: pointer;
            margin-top: 25px;
            box-shadow: 0 6px 15px rgba(40, 62, 90, 0.3);
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
            letter-spacing: 0.8px;
        }

        .btn-submit:hover {
            background-color: rgba(255, 255, 255, 0.95);
            box-shadow: 0 8px 22px rgba(40, 62, 90, 0.5);
            color: #1b2a3a;
        }

        .back-button {
            display: block;
            text-align: center;
            margin-top: 22px;
            padding: 14px 0;
            background-color: #5e6fa3;
            color: #dbe9ff;
            text-decoration: none;
            border-radius: 14px;
            box-shadow: 0 4px 14px #4861a9cc;
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
            font-weight: 600;
            letter-spacing: 0.6px;
        }

        .back-button:hover {
            background-color: #3e4d7a;
            box-shadow: 0 6px 18px #2e3a58cc;
            color: #f0f7ff;
        }

        @media (max-width: 600px) {
            .input-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<?php if ($successMessage): ?>
    <div class="message success"><?= $successMessage ?></div>
<?php endif; ?>

<?php if ($errorMessage): ?>
    <div class="message error"><?= $errorMessage ?></div>
<?php endif; ?>

<div class="form-wrapper">
    <h2>🚘 Yeni Araba Ekle</h2>
    <form method="post" action="" enctype="multipart/form-data">
        <div class="input-grid">
            <input type="text" name="marka" placeholder="Marka" required />
            <input type="text" name="model" placeholder="Model" required />
            <input type="number" name="yil" placeholder="Yıl" required />
            <input type="text" name="vites" placeholder="Vites Türü" required />
            <input type="text" name="yakit" placeholder="Yakıt Türü" required />
            <input type="number" name="km" placeholder="KM" required />
            <input type="number" name="motor_gucu" placeholder="Motor Gücü (HP)" required />
            <input type="number" name="motor_hacmi" placeholder="Motor Hacmi (cc)" required />
            <input type="text" name="renk" placeholder="Renk" required />
            <input type="text" name="kasa_tipi" placeholder="Kasa Tipi" required />
            <input type="number" step="0.01" name="fiyat" placeholder="Fiyat (₺)" required />
        </div>
        <br>
        <label for="foto" style="display:block; margin:15px 0 5px; font-weight:600;">Fotoğraf Yükle (birden fazla seçebilirsiniz):</label>
        <input type="file" name="foto[]" multiple accept="image/*" style="margin-bottom: 20px;" />
        <input type="submit" value="Araba Ekle" class="btn-submit" />
    </form>
    <a href="admin-panel.php" class="back-button">◀️ Geri Dön</a>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.querySelector('.form-wrapper');
        form.style.opacity = 0;
        form.style.transform = 'translateY(20px)';
        setTimeout(() => {
            form.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            form.style.opacity = 1;
            form.style.transform = 'translateY(0)';
        }, 100);
    });
</script>

</body>
</html>
