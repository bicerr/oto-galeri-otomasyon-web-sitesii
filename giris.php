<?php
session_start();

require_once "veritabani.php";  // Veritabanı bağlantısının doğru olduğundan emin olun

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kullanici_adi = $_POST["kullanici_adi"];
    $sifre = $_POST["sifre"];

    // Hata ayıklama: Gelen POST verilerini kontrol et
    error_log("POST verisi: Kullanıcı Adı: $kullanici_adi, Şifre: $sifre");

    // Admin için özel kontrol (admin ve şifre)
    if ($kullanici_adi == "admin@admin.com" && $sifre == "admin123") {
        // Admin giriş yaptıysa session'da admin bilgilerini tut
        $_SESSION["admin"] = true;
        header("Location: admin-panel.php");
        exit();
    } else {
        // Admin değilse, kullanıcılar tablosunu kontrol et
        $kullanicilar_sorgu = $baglanti->prepare("SELECT * FROM kullanicilar WHERE email = ? AND sifre = ?");
        $kullanicilar_sorgu->execute([$kullanici_adi, $sifre]);
        $kullanici = $kullanicilar_sorgu->fetch(PDO::FETCH_ASSOC);

        // Eğer kullanıcı bulunursa, session'a kullanıcı adı ekle ve kullanıcı paneline yönlendir
        if ($kullanicilar) {
            $_SESSION["kullanici_adi"] = $kullanicilar["email"];
            $_SESSION["kullanici_id"] = $kullanicilar["kullanici_id"];
            header("Location: kullanici-panel.php");
            exit();
        } else {
            // Kullanıcı ve admin yoksa hata mesajı göster
            $hata = "Hatalı kullanıcı adı veya şifre!";
        }
    }
    if ($kullanici) {
        $_SESSION['kullanici_id'] = $user['kullanici_id']; // veritabanından gelen gerçek id
        $_SESSION['kullanici_adi'] = $user['kullanici_adi'];
        $_SESSION['email'] = $user['email'];

        header("Location: kullanici-panel.php");
        exit();
    }
    
}

?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Giriş Yap</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background: linear-gradient(45deg, #ff6a00, #ffcc00, #33cc33, #00b3b3);
            background-size: 400% 400%;
            animation: gradientBG 10s ease infinite;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            overflow: hidden;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .login-container {
            background-color: #ffffff;
            border-radius: 15px;
            padding: 40px 30px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
            text-align: center;
            transform: translateY(-50px);
            animation: slideIn 0.7s ease-out forwards;
        }

        @keyframes slideIn {
            0% {
                transform: translateY(-50px);
                opacity: 0;
            }
            100% {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .login-container h2 {
            color: #333;
            margin-bottom: 25px;
            font-weight: 500;
            font-size: 26px;
        }

        .form-group {
            margin-bottom: 25px;
            text-align: left;
            width: 100%;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-size: 16px;
            font-weight: 500;
        }

        .form-group input[type="text"],
        .form-group input[type="password"] {
            width: 100%;
            padding: 14px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            margin-top: 8px;
            background-color: #f4f4f4;
            color: #333;
            transition: border-color 0.3s ease, background-color 0.3s ease;
        }

        .form-group input[type="text"]:focus,
        .form-group input[type="password"]:focus {
            border-color: #28a745;
            outline: none;
            background-color: #eaf9e2;
        }

        .submit-button {
            background-color: #28a745;
            color: white;
            padding: 14px;
            font-size: 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.3s ease;
            font-weight: 500;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .submit-button:hover {
            background-color: #218838;
        }

        .error-message {
            color: #dc3545;
            font-size: 14px;
            margin-top: 15px;
            font-weight: bold;
        }

        .back-link {
            margin-top: 20px;
        }

        .back-link a {
            color: #28a745;
            text-decoration: none;
            font-size: 14px;
        }

        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <h2>🔑 Admin Girişi</h2>
        <form action="giris.php" method="post">
            <div class="form-group">
                <label for="kullanici_adi">Kullanıcı Adı</label>
                <input type="text" name="kullanici_adi" required placeholder="Kullanıcı Adınızı Girin">
            </div>
            <div class="form-group">
                <label for="sifre">Şifre</label>
                <input type="password" name="sifre" required placeholder="Şifrenizi Girin">
            </div>
            <button type="submit" class="submit-button">Giriş Yap</button>

            <?php if (isset($hata)) echo "<div class='error-message'>$hata</div>"; ?>
        </form>

        <div class="back-link">
            <a href="index.php">← Ana Sayfaya Dön</a>
        </div>
    </div>

</body>
</html>
