<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Halaman Tidak Ditemukan | <?= APP_NAME ?></title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background-color: #f3f4f6; color: #1f2937; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        .container { text-align: center; background: white; padding: 40px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); max-width: 500px; width: 90%; }
        h1 { color: #d97706; font-size: 48px; margin: 0 0 10px 0; }
        p { font-size: 16px; margin-bottom: 20px; color: #4b5563; }
        a { display: inline-block; padding: 10px 20px; background-color: #2563eb; color: white; text-decoration: none; border-radius: 5px; font-weight: 500; }
        a:hover { background-color: #1d4ed8; }
    </style>
</head>
<body>
    <div class="container">
        <h1>404</h1>
        <h2>Halaman Tidak Ditemukan</h2>
        <p>Maaf, halaman yang Anda cari mungkin telah dihapus, dipindah, atau URL-nya salah.</p>
        <a href="<?= base_url('dashboard') ?>">Kembali ke Beranda</a>
    </div>
</body>
</html>