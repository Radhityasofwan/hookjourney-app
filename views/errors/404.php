<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Halaman Tidak Ditemukan | <?= APP_NAME ?></title>
    <script>
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches)
            document.documentElement.classList.add('dark');
        const t = localStorage.getItem('theme');
        if (t === 'dark') document.documentElement.classList.add('dark');
        else if (t === 'light') document.documentElement.classList.remove('dark');
    </script>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: #f0f2f8;
            color: #111118;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            transition: background 0.2s, color 0.2s;
        }

        html.dark body {
            background: #060609;
            color: #ffffff;
        }

        .card {
            text-align: center;
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(0, 0, 0, 0.1);
            padding: 48px 40px;
            border-radius: 28px;
            box-shadow: 0 16px 48px rgba(0, 0, 0, 0.08);
            max-width: 480px;
            width: 90%;
            backdrop-filter: blur(20px);
        }

        html.dark .card {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.1);
            box-shadow: 0 16px 48px rgba(0, 0, 0, 0.4);
        }

        .code {
            font-size: 4rem;
            font-weight: 900;
            color: #d97706;
            margin: 0 0 8px;
            letter-spacing: -2px;
        }

        h2 {
            font-size: 1.3rem;
            font-weight: 700;
            margin: 0 0 12px;
        }

        p {
            font-size: 0.95rem;
            color: rgba(17, 17, 24, 0.6);
            margin-bottom: 28px;
            line-height: 1.6;
        }

        html.dark p {
            color: rgba(255, 255, 255, 0.55);
        }

        a {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: #2563eb;
            color: #fff;
            text-decoration: none;
            border-radius: 50px;
            font-size: 0.9rem;
            font-weight: 600;
            transition: background 0.15s, transform 0.1s;
        }

        a:hover {
            background: #1d4ed8;
        }

        a:active {
            transform: scale(0.97);
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="code">404</div>
        <h2>Halaman Tidak Ditemukan</h2>
        <p>Maaf, halaman yang Anda cari mungkin telah dihapus, dipindah, atau URL-nya salah.</p>
        <a href="<?= base_url('dashboard') ?>">← Kembali ke Beranda</a>
    </div>
</body>

</html>