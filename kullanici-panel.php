<?php
session_start();
require_once "veritabani.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $sifre = $_POST["sifre"];

    // Kullanıcıyı email ile çek
    $sorgu = $baglanti->prepare("SELECT * FROM kullanicilar WHERE email = ?");
    $sorgu->execute([$email]);
    $kullanici = $sorgu->fetch(PDO::FETCH_ASSOC);

    if ($kullanici) {
        // Şifre doğrulaması (veritabanında hashed şifre var varsayarak)
        if (password_verify($sifre, $kullanici["sifre"])) {
            // Doğru şifre, session setle
            $_SESSION["email"] = $kullanici["email"];
            $_SESSION["kullanici_id"] = $kullanici["kullanici_id"];
            $_SESSION["kullanici_adi"] = $kullanici["email"]; // varsa ekle

            header("Location: kullanici-panel.php");
            exit();
        } else {
            $hata = "Geçersiz email veya şifre!";
        }
    } else {
        $hata = "Geçersiz email veya şifre!";
    }
}


$email = $_SESSION["email"];
$query = $baglanti->prepare("SELECT * FROM kullanicilar WHERE email = ?");
$query->execute([$email]);
$user = $query->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8" />
  <title>Kullanıcı Paneli</title>
  <!-- Google Fonts: Oswald -->
  <link href="https://fonts.googleapis.com/css2?family=Oswald:wght@700&display=swap" rel="stylesheet">
  <style>
    * {
      box-sizing: border-box;
    }

    html, body {
  height: 100%;
  margin: 0;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background-color: #0a0f2c;
  color: #fff;
  overflow-x: hidden;
}


    .header {
  position: relative;
  padding: 15px 40px;
  display: flex;
  justify-content: center; /* Ortala */
  align-items: center;
  z-index: 10;
}

.header-title {
  font-size: 52px;
  font-weight: 900;
  color: #e0f7fa; /* buz beyazı ton */
  font-family: 'Oswald', sans-serif;
  text-transform: uppercase;
  letter-spacing: 5px;
  user-select: none;
  z-index: 11;

  text-shadow:
    2px 2px 4px rgba(224, 247, 250, 0.8),
    4px 4px 8px rgba(224, 247, 250, 0.6),
    0 0 12px rgba(224, 247, 250, 0.7);

  position: relative;
  animation: neonGlow 3s ease-in-out infinite alternate;
}

@keyframes neonGlow {
  from {
    text-shadow:
      2px 2px 4px rgba(224, 247, 250, 0.8),
      4px 4px 8px rgba(224, 247, 250, 0.6),
      0 0 12px rgba(224, 247, 250, 0.7);
  }
  to {
    text-shadow:
      3px 3px 6px rgba(224, 247, 250, 0.9),
      6px 6px 10px rgba(224, 247, 250, 0.8),
      0 0 16px rgba(224, 247, 250, 0.8);
  }
}



.user-menu {
  position: absolute;
  right: 40px;  /* Sağdan boşluk */
  top: 50%;
  transform: translateY(-50%);
}


    .user-icon {
      background: linear-gradient(135deg,rgb(255, 255, 255),rgb(255, 255, 255));
      color: white;
      border: none;
      border-radius: 50%;
      padding: 12px 15px;
      font-size: 20px;
      cursor: pointer;
      box-shadow: 0 0 12px #1abc9c;
      transition: transform 0.3s ease;
      user-select: none;
    }

    .user-icon:hover {
      transform: scale(1.1);
    }

    .dropdown {
      display: none;
      position: absolute;
      right: 0;
      top: 50px;
      background-color: rgba(255,255,255,0.05);
      border: 1px solid rgba(255,255,255,0.1);
      backdrop-filter: blur(12px);
      border-radius: 12px;
      overflow: hidden;
      min-width: 180px;
      z-index: 1000;
    }

    .dropdown a {
      display: block;
      padding: 14px 18px;
      text-decoration: none;
      color: #fff;
      font-size: 15px;
      transition: background 0.2s ease;
    }

    .dropdown a:hover {
      background: rgba(255, 255, 255, 0.08);
    }

    .user-menu:hover .dropdown {
      display: block;
    }

    /* --- Ana container üçe bölünecek --- */
    .container {
      display: flex;
      height: calc(100vh - 80px); /* header yüksekliği kadar çıkar */
      padding: 0 10px;
    }

    /* Her buton alanı eşit genişlikte ve yüksek */
    .button-section {
      flex: 1;
      margin: 10px;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .custom-button {
  width: 100%;
  height: 80vh;
  max-height: 600px;
  font-size: 2.8rem;
  font-weight: 900;
  color: #fff;
  background: #667eea; /* Sabit renk */
  border: none;
  border-radius: 40px;
  text-decoration: none;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  user-select: none;
}

.btn-satilik {
  background-image: url('images/satilik.jpg');
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
}

.btn-temizlik {
  background-image: url('images/temizlik.jpg');
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
}

.btn-kiralik {
  background-image: url('images/kiralik.jpg');
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
}


    .custom-button span {
      font-size: 4rem;
      margin-bottom: 20px;
      filter: drop-shadow(0 0 10px rgba(255,255,255,0.7));
    }

    /* Modal */
    .modal {
      display: none;
      position: fixed;
      z-index: 10;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0,0,0,0.7);
      overflow: auto;
    }

    .modal-content {
      background-color: #1e2a38;
      margin: 5% auto;
      padding: 30px;
      border-radius: 16px;
      width: 90%;
      max-width: 500px;
      color: #fff;
      border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .close {
      float: right;
      font-size: 28px;
      font-weight: bold;
      color: #aaa;
      cursor: pointer;
    }

    .close:hover {
      color: #fff;
    }
  </style>
</head>
<body>

<div class="header">
  <div class="header-title">BEMA AUTO</div>
  <div class="user-menu">
    <button class="user-icon">👤</button>
    <div class="dropdown">
      <a href="javascript:void(0);" id="profilBtn">👁‍🗨 Profil Bilgileri</a>
      <a href="cikis.php">🚪 Çıkış Yap</a>
    </div>
  </div>
</div>


<div class="container">
  <div class="button-section">
    <a href="araba-listesi.php" class="custom-button btn-satilik">
      Satılık Arabalar
    </a>
  </div>
  <div class="button-section">
    <a href="temizlik.php" class="custom-button btn-temizlik">
      Temizlik
    </a>
  </div>
  <div class="button-section">
    <a href="rent.php" class="custom-button btn-kiralik">
      Kiralık Arabalar
    </a>
  </div>
</div>

<div id="profilModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h3>🧾 Profil Bilgileri</h3>
    <p><strong>Ad:</strong> <?php echo htmlspecialchars($user['ad']); ?></p>
    <p><strong>Soyad:</strong> <?php echo htmlspecialchars($user['soyad']); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
  </div>
</div>

<script>
  const modal = document.getElementById("profilModal");
  const btn = document.getElementById("profilBtn");
  const span = document.querySelector(".close");

  btn.onclick = () => modal.style.display = "block";
  span.onclick = () => modal.style.display = "none";
  window.onclick = event => {
    if (event.target === modal) modal.style.display = "none";
  };
</script>

</body>
</html>