<?php
session_start();
require_once "veritabani.php";

if (!isset($_SESSION["admin"])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    // Çalışan ekleme işlemi
    $ad = $_POST['ad'];
    $soyad = $_POST['soyad'];
    $pozisyon = $_POST['pozisyon'];
    $email = $_POST['email'];
    $telefon = $_POST['telefon'];
    $maas = $_POST['maas'];

    try {
        $stmt = $baglanti->prepare("INSERT INTO calisanlar (ad, soyad, pozisyon, email, telefon, maas) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$ad, $soyad, $pozisyon, $email, $telefon, $maas]);
        
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
    <meta charset="UTF-8" />
    <title>Çalışan Ekle - K Tasarımı</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Segoe+UI&display=swap');

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(270deg, #1e3c72, #2a5298, #1e3c72);
            background-size: 600% 600%;
            animation: gradientBG 15s ease infinite;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        @keyframes gradientBG {
            0% {background-position: 0% 50%;}
            50% {background-position: 100% 50%;}
            100% {background-position: 0% 50%;}
        }

        .card {
            background: rgba(30, 30, 30, 0.85);
            backdrop-filter: blur(12px);
            border-radius: 0 !important;
            padding: 40px 35px;
            max-width: 480px;
            width: 100%;
            box-shadow: 0 12px 40px rgba(0,0,0,0.7);
            color: #f1f1f1;
            box-sizing: border-box;
            transition: transform 0.3s ease;
            border: 1.5px solid rgba(255 255 255 / 0.1);
        }
        .card:hover {
            transform: scale(1.02);
            box-shadow: 0 16px 48px rgba(0,0,0,0.85);
            border-radius: 0 !important;
        }

        h2 {
            margin-top: 0;
            margin-bottom: 28px;
            font-weight: 700;
            font-size: 30px;
            text-align: center;
            color: #e5e7eb;
            text-shadow: 0 0 8px #8aaaffcc;
            user-select: none;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        label {
            font-weight: 600;
            font-size: 14px;
            color: #cfd4e2;
            margin-bottom: 6px;
            user-select: none;
        }

        input[type="text"],
        input[type="email"],
        input[type="number"] {
            padding: 14px 16px;
            border-radius: 10px;
            border: none;
            font-size: 16px;
            outline: none;
            background: #f9f9f9;
            color: #2a2a2a;
            box-shadow: inset 0 0 6px #aac0ff88;
            transition: box-shadow 0.3s ease, background-color 0.3s ease;
            user-select: text;
        }
        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="number"]:focus {
            box-shadow: inset 0 0 12px #7099ffdd;
            background-color: #ffffff;
        }

        input[type="submit"] {
            cursor: pointer;
            font-weight: 700;
            font-size: 18px;
            background: #e7e9ee;
            color: #1e3c72;
            border: none;
            border-radius: 14px;
            padding: 16px 0;
            box-shadow: 0 0 18px #7ea0ffbb;
            transition: background 0.3s ease, transform 0.25s ease;
            user-select: none;
            user-drag: none;
            margin-top: 8px;
        }
        input[type="submit"]:hover {
            background: #d9dbe1;
            transform: scale(1.07);
            box-shadow: 0 0 26px #8bb3ffcc;
            color: #0f2451;
        }

        .back-button {
            margin-top: 26px;
            display: block;
            text-align: center;
            padding: 14px 0;
            border-radius: 14px;
            background: #e7e9ee;
            color: #1e3c72;
            font-weight: 700;
            font-size: 16px;
            text-decoration: none;
            box-shadow: 0 0 18px #7ea0ffbb;
            user-select: none;
            user-drag: none;
            transition: background 0.3s ease, transform 0.25s ease, color 0.3s ease;
        }
        .back-button:hover {
            background: #d9dbe1;
            transform: scale(1.07);
            box-shadow: 0 0 26px #8bb3ffcc;
            color: #0f2451;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="card" role="main" aria-label="Yeni çalışan ekleme formu">
        <h2>Yeni Çalışan Ekle</h2>
        <form method="post" action="">
            <label for="ad">Ad:</label>
            <input type="text" id="ad" name="ad" required>

            <label for="soyad">Soyad:</label>
            <input type="text" id="soyad" name="soyad" required>

            <label for="pozisyon">Pozisyon:</label>
            <input type="text" id="pozisyon" name="pozisyon">

            <label for="email">Email:</label>
            <input type="email" id="email" name="email">

            <label for="telefon">Telefon:</label>
            <input type="text" id="telefon" name="telefon">

            <label for="maas">Maaş:</label>
            <input type="number" id="maas" name="maas" step="0.01">

            <input type="submit" name="submit" value="Ekle">
        </form>
        <a href="admin-panel.php" class="back-button">Geri Dön</a>
    </div>
</body>
</html>
