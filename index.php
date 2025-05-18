<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8" />
    <title>Ana Sayfa - BEMA OTO GALERİ</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet" />
    <style>
        * {
            box-sizing: border-box;
        }

        body {
    margin: 0;
    padding: 160px 20px 20px;
    font-family: 'Poppins', sans-serif;
    background-color: #0a0f2c;
    color: #ddd;
    min-height: 100vh;
    overflow-x: hidden;
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
}

body::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 0;
    background: url('data:image/svg+xml;utf8,<svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg"><path d="M0 100 Q150 200 300 100 T600 100 T900 100 T1200 100" stroke="%235a9aff" stroke-width="1.5" fill="none" opacity="0.05"/><path d="M0 300 Q150 400 300 300 T600 300 T900 300 T1200 300" stroke="%235a9aff" stroke-width="1.5" fill="none" opacity="0.05"/><path d="M0 500 Q150 600 300 500 T600 500 T900 500 T1200 500" stroke="%235a9aff" stroke-width="1.5" fill="none" opacity="0.05"/></svg>');
    background-repeat: no-repeat;
    background-size: cover;
    pointer-events: none;
}




        .logo {
            position: absolute;
            top: 30px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 36px;
            font-weight: 700;
            color: #8ab6ff;
            text-shadow: 0 0 10px #508aff;
            background: rgba(255, 255, 255, 0.1);
            padding: 10px 30px;
            border-radius: 25px;
            backdrop-filter: blur(8px);
            border: 1.5px solid rgba(255, 255, 255, 0.15);
            user-select: none;
            box-shadow: 0 0 15px #508aff;
            animation: glow 2.5s ease-in-out infinite alternate;
        }

        @keyframes glow {
    0% {
        text-shadow: 0 0 5px #8ab6ff, 0 0 10px #8ab6ff;
        box-shadow: 0 0 10px #508aff, 0 0 20px #508aff;
    }
    100% {
        text-shadow: 0 0 15px #b3d5ff, 0 0 25px #b3d5ff;
        box-shadow: 0 0 20px #b3d5ff, 0 0 40px #b3d5ff;
    }
}

        
.logo-image {
  display: block;
  margin: 30px auto;
  max-width: 300px;
  width: 100%;
  height: auto;
  box-shadow:
    0 0 8px #3f68c2,
    0 0 15px #3f68c2,
    0 0 20px #3f68c2,
    0 0 30px #3f68c2;
}





        .subtitle {
            font-size: 22px;
            color: #a6c8ff;
            margin-bottom: 50px;
            animation: fadeInDown 1.3s ease forwards;
            text-shadow: 0 0 5px #3f68c2;
        }

        .button-container {
            display: flex;
            flex-direction: column;
            gap: 25px;
            width: 100%;
            max-width: 320px;
            animation: fadeInUp 1.5s ease forwards;
        }

        form {
            width: 100%;
        }

        button {
            width: 100%;
            padding: 16px;
            font-size: 18px;
            font-weight: 600;
            color: #8ab6ff;
            background: rgba(255, 255, 255, 0.08);
            border: 2px solid #5a9aff;
            border-radius: 20px;
            cursor: pointer;
            backdrop-filter: blur(10px);
            box-shadow: 0 0 15px rgba(90, 154, 255, 0.5);
            transition: all 0.3s ease-in-out;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        button:hover {
            background: rgba(255, 255, 255, 0.15);
            border-color: #b3d5ff;
            box-shadow: 0 0 25px #b3d5ff, 0 0 40px #b3d5ff;
            transform: scale(1.05);
            color: #fff;
        }

        button:active {
            transform: scale(0.97);
        }

        .icon {
            margin-right: 12px;
            font-size: 22px;
        }

        .admin-button {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(255, 255, 255, 0.08);
            color: #8ab6ff;
            border: 1.5px solid #5a9aff;
            padding: 9px 16px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            box-shadow: 0 0 10px rgba(90, 154, 255, 0.5);
            backdrop-filter: blur(6px);
            transition: all 0.3s ease;
        }

        .admin-button:hover {
            background: rgba(255, 255, 255, 0.18);
            box-shadow: 0 0 30px #b3d5ff;
            color: #fff;
            transform: scale(1.1);
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-25px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
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
    <a href="giris.php" class="admin-button">Admin Girişi</a>

    

    <!--
<h1>BEMA OTO GALERİ</h1>
-->
<img src="images/logo.png" alt="BEMA OTO GALERİ Logo" class="logo-image" />
    <div class="subtitle">Hoş Geldiniz</div>

    <div class="button-container">
        <form action="kullanici-giris.php" method="get">
            <button type="submit"><span class="icon">🔓</span>Kullanıcı Giriş</button>
        </form>

        <form action="kullanici-kayit.php" method="get">
            <button type="submit"><span class="icon">📝</span>Kullanıcı Kayıt</button>
        </form>
    </div>
</body>
</html>
