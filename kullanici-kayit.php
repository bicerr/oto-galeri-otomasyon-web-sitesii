<?php
session_start();
require_once "veritabani.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ad = $_POST["ad"];
    $soyad = $_POST["soyad"];
    $email = $_POST["email"];
    $sifre = password_hash($_POST["sifre"], PASSWORD_DEFAULT);

    $sorgu = $baglanti->prepare("INSERT INTO kullanicilar (ad, soyad, email, sifre) 
                                  VALUES (?, ?, ?, ?)");
    $sorgu->execute([$ad, $soyad, $email, $sifre]);

    if ($sorgu) {
        header("Location: kullanici-giris.php");
        exit();
    } else {
        $hata = "Bir hata oluştu, lütfen tekrar deneyin!";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kullanıcı Kayıt</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet" />
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #0a0f2c, #121a3b, #1b275a);
            color: #ddd;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            overflow: hidden;
        }

        h2 {
            font-size: 32px;
            margin-bottom: 20px;
            color: #8ab6ff;
            text-shadow: 0 0 8px #508aff;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.05);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 0 30px rgba(90, 154, 255, 0.2);
            backdrop-filter: blur(10px);
            width: 90%;
            max-width: 400px;
            border: 1.5px solid rgba(255, 255, 255, 0.1);
            animation: fadeInUp 1s ease forwards;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-group label {
            display: block;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 8px;
            color: #a6c8ff;
        }

        input[type="text"],
        input[type="password"],
        input[type="email"] {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            background-color: rgba(255, 255, 255, 0.1);
            color: #fff;
            outline: none;
        }

        button {
            background: rgba(255, 255, 255, 0.08);
            color: #8ab6ff;
            padding: 14px;
            font-size: 16px;
            border: 2px solid #5a9aff;
            border-radius: 10px;
            cursor: pointer;
            width: 100%;
            backdrop-filter: blur(6px);
            box-shadow: 0 0 15px rgba(90, 154, 255, 0.3);
            transition: all 0.3s ease;
            font-weight: bold;
        }

        button:hover {
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 0 30px #b3d5ff;
            color: #fff;
            transform: scale(1.05);
        }

        .error-message {
            color: #ff6b6b;
            margin-top: 15px;
            font-weight: bold;
            text-align: center;
        }

        .back-link {
            margin-top: 25px;
            text-align: center;
        }

        .back-link a {
            color: #8ab6ff;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s ease, border-bottom 0.3s ease;
            border-bottom: 2px solid transparent;
        }

        .back-link a:hover {
            color: #fff;
            border-bottom: 2px solid #b3d5ff;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(25px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>

    <h2>📝 Kullanıcı Kayıt</h2>

    <div class="login-container">
        <form action="kullanici-kayit.php" method="post">
            <div class="form-group">
                <label for="ad">🧾 Ad</label>
                <input type="text" name="ad" required>
            </div>
            <div class="form-group">
                <label for="soyad">🧾 Soyad</label>
                <input type="text" name="soyad" required>
            </div>
            <div class="form-group">
                <label for="email">📧 E-posta</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="sifre">🔒 Şifre</label>
                <input type="password" name="sifre" required>
            </div>
            <button type="submit">Kayıt Ol</button>

            <?php if (isset($hata)) echo "<div class='error-message'>$hata</div>"; ?>
        </form>
    </div>

    <div class="back-link">
        <a href="index.php">← Ana Sayfaya Dön</a>
    </div>

</body>
</html>