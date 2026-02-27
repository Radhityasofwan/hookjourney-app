<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?= APP_NAME ?></title>
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #050511; /* Dark space background */
            color: #ffffff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            overflow: hidden;
        }

        /* --- EFEK LIQUID AURORA --- */
        .aurora-bg {
            position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; z-index: -1;
        }
        .blob {
            position: absolute; border-radius: 50%; filter: blur(90px); opacity: 0.7;
            animation: float 12s infinite ease-in-out alternate;
        }
        /* Kombinasi Warna Premium (Pink, Ungu, Biru) */
        .blob-1 { background: #ff007f; width: 450px; height: 450px; top: -10%; left: -10%; }
        .blob-2 { background: #7209b7; width: 550px; height: 550px; bottom: -20%; right: -10%; animation-delay: -3s; }
        .blob-3 { background: #4361ee; width: 400px; height: 400px; bottom: 10%; left: 15%; animation-delay: -6s; }

        @keyframes float {
            0% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(40px, -60px) scale(1.05); }
            100% { transform: translate(-30px, 30px) scale(0.95); }
        }

        /* --- GLASSMORPHISM CARD --- */
        .glass-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(24px); /* Efek kaca buram iOS */
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-top: 1px solid rgba(255, 255, 255, 0.3);
            border-left: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 28px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3), inset 0px 0px 15px rgba(255, 255, 255, 0.05);
            padding: 3.5rem 3rem;
            width: 100%;
            max-width: 440px;
            z-index: 10;
        }
    </style>
</head>
<body>

    <!-- Latar Belakang Liquid -->
    <div class="aurora-bg">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="blob blob-3"></div>
    </div>

    <!-- Area Konten Form -->
    <div class="container d-flex justify-content-center px-4">
        <div class="glass-card">
            <div class="text-center mb-5">
                <h1 class="fw-bold mb-2" style="letter-spacing: -0.5px;"><?= APP_NAME ?></h1>
                <p class="text-white-50" style="font-size: 0.95rem;">Manajemen operasional multi-brand terpadu.</p>
            </div>

            <!-- Injeksi file views/auth/login.php -->
            <?= $content ?>
            
        </div>
    </div>

</body>
</html>