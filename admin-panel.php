<?php
session_start();
if (!isset($_SESSION["admin"])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #1c1e26;
            color: #f0f0f0;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 60px;
        }

        h1 {
            margin-bottom: 40px;
            font-size: 36px;
            color: #00adb5;
        }

        .card-container {
            display: flex;
            gap: 60px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .card-column {
            display: flex;
            flex-direction: column;
            gap: 30px;
            align-items: center;
        }

        .card {
            background-color: #2f313a;
            border-radius: 15px;
            padding: 30px;
            width: 220px;
            text-align: center;
            text-decoration: none;
            color: #ffffff;
            box-shadow: 0 6px 12px rgba(0,0,0,0.2);
            transition: transform 0.3s, background-color 0.3s;
        }

        .card:hover {
            transform: scale(1.05);
            background-color: #3a3d47;
        }

        .card-icon {
            font-size: 40px;
            margin-bottom: 15px;
        }

        .card-title {
            font-size: 18px;
            font-weight: bold;
        }

        @media (max-width: 800px) {
            .card-container {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>
<body>

    <h1>🔧 Admin Panel</h1>

    <div class="card-container">

    
        <!-- Satılık Araba İşlemleri -->
        <div class="card-column">
            <a href="araba-listesi.php" class="card">
                <div class="card-icon">📝</div>
                <div class="card-title">Satılık Araba Listesi</div>
            </a>

            <a href="ekle.php" class="card">
                <div class="card-icon">➕</div>
                <div class="card-title">Satılık Yeni Araba Ekle</div>
            </a>
        </div>

        <!-- Kiralık Araba İşlemleri -->
        <div class="card-column">
            <a href="rent.php" class="card">
                <div class="card-icon">📋</div>
                <div class="card-title">Kiralık Araba Listesi</div>
            </a>

            <a href="rent-ekle.php" class="card">
                <div class="card-icon">➕</div>
                <div class="card-title">Kiralık Araba Ekle</div>
            </a>
        </div>
        

        <!-- Çalışanlar İşlemleri -->
        <div class="card-column">
            <a href="calisanlar-listesi.php" class="card">
                <div class="card-icon">👷‍♂️</div>
                <div class="card-title">Çalışanlar</div>
            </a>

            <a href="calisanlar-ekle.php" class="card">
                <div class="card-icon">➕</div>
                <div class="card-title">Yeni Çalışan Ekle</div>
            </a>
        </div>

        <!-- Temizlik İşlemleri Butonu -->
        <div class="card-column">
            <a href="temizlik.php" class="card">
                <div class="card-icon">🧼</div>
                <div class="card-title">Temizlik İşlemleri</div>
            </a>
        </div>
        <!-- Kiralama Yönet Butonu -->
<div class="card-column">
    <a href="rent-yonet.php" class="card">
        <div class="card-icon">🔁</div>
        <div class="card-title">Kiralama Yönet</div>
    </a>
</div>


        <!-- Çıkış -->
        <div class="card-column">
            <a href="cikis.php" class="card">
                <div class="card-icon">🚪</div>
                <div class="card-title">Çıkış Yap</div>
            </a>
        </div>

    </div>

</body>
</html>
