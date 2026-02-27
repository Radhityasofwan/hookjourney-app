<?php
/**
 * HOOKJOURNEY - GROWTH OPS HUB
 * Auth Layout: views/layouts/auth.php
 * Konsisten dengan app.php: Static aurora, glass card, teks kontras tinggi.
 */
?>
<!DOCTYPE html>
<html lang="id" class="antialiased">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Login — <?= APP_NAME ?></title>

    <meta name="theme-color" content="#060609">

    <!-- Script anti-kedip (FOUC) -->
    <script>
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['-apple-system', 'BlinkMacSystemFont', '"Segoe UI"', 'Roboto', 'Helvetica', 'Arial', 'sans-serif'],
                    },
                    colors: {
                        ios: {
                            blue: '#007AFF',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Phosphor Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>

    <style>
        :root {
            --glass-bg: rgba(255, 255, 255, 0.06);
            --glass-border: rgba(255, 255, 255, 0.12);
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            height: 100%;
            margin: 0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #060609;
            -webkit-tap-highlight-color: transparent;
        }

        /* --- STATIC AURORA (konsisten dengan app.php — no animation) --- */
        .aurora-bg {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            overflow: hidden;
        }

        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(100px);
            opacity: 0.35;
        }

        .blob-1 {
            background: #4361ee;
            width: 500px;
            height: 500px;
            top: -15%;
            left: -10%;
        }

        .blob-2 {
            background: #7c3aed;
            width: 600px;
            height: 600px;
            bottom: -20%;
            right: -10%;
        }

        .blob-3 {
            background: #be185d;
            width: 400px;
            height: 400px;
            bottom: 5%;
            left: 20%;
        }

        /* --- GLASS CARD --- */
        .glass-card {
            position: relative;
            z-index: 10;
            background: rgba(15, 12, 30, 0.6);
            backdrop-filter: blur(40px) saturate(160%);
            -webkit-backdrop-filter: blur(40px) saturate(160%);
            border: 1px solid var(--glass-border);
            border-top-color: rgba(255, 255, 255, 0.2);
            border-radius: 28px;
            box-shadow: 0 32px 64px rgba(0, 0, 0, 0.5), inset 0 1px 0 rgba(255, 255, 255, 0.1);
            padding: 3rem 2.5rem;
            width: 100%;
            max-width: 420px;
        }

        /* --- FORM INPUTS — Dark glass style, teks putih jelas --- */
        .auth-input {
            width: 100%;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 14px;
            padding: 0.85rem 1.1rem;
            font-size: 0.95rem;
            color: #ffffff;
            outline: none;
            transition: border-color 0.15s, box-shadow 0.15s;
        }

        .auth-input::placeholder {
            color: rgba(255, 255, 255, 0.3);
        }

        .auth-input:focus {
            border-color: #007AFF;
            box-shadow: 0 0 0 3px rgba(0, 122, 255, 0.25);
            background: rgba(255, 255, 255, 0.09);
        }

        .auth-input.error {
            border-color: rgba(255, 80, 80, 0.6);
            box-shadow: 0 0 0 3px rgba(255, 80, 80, 0.15);
        }

        .auth-label {
            display: block;
            font-size: 0.78rem;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.55);
            text-transform: uppercase;
            letter-spacing: 0.6px;
            margin-bottom: 0.5rem;
        }

        .auth-btn {
            width: 100%;
            background: #007AFF;
            color: #ffffff;
            border: none;
            border-radius: 14px;
            padding: 0.9rem 1.25rem;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.15s, transform 0.1s;
            box-shadow: 0 8px 24px rgba(0, 122, 255, 0.35);
            letter-spacing: 0.1px;
        }

        .auth-btn:hover {
            background: #0066dd;
        }

        .auth-btn:active {
            transform: scale(0.97);
            background: #005ac8;
        }

        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
    </style>
</head>

<body>
    <!-- Latar Belakang Static Aurora -->
    <div class="aurora-bg">
        <div class="blob blob-1"></div>
        <div class="blob blob-2"></div>
        <div class="blob blob-3"></div>
    </div>

    <!-- Glass Card Wrapper -->
    <div class="px-4 w-full flex justify-center">
        <div class="glass-card">

            <!-- Logo + Header -->
            <div class="text-center mb-8">
                <div
                    class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-white/10 border border-white/20 mb-4">
                    <i class="ph-fill ph-planet text-white text-2xl"></i>
                </div>
                <h1 class="text-2xl font-extrabold text-white tracking-tight mb-1" style="letter-spacing: -0.5px;">
                    <?= APP_NAME ?>
                </h1>
                <p class="text-sm font-medium" style="color: rgba(255,255,255,0.5);">
                    Manajemen operasional multi-brand terpadu.
                </p>
            </div>

            <!-- Injeksi: views/auth/login.php -->
            <?= $content ?>

        </div>
    </div>

</body>

</html>