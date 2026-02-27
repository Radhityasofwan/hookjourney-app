<?php
// Konfigurasi Utama Aplikasi Hookjourney
define('APP_NAME', 'Hookjourney');

// Sesuaikan BASE_URL dengan domain atau localhost Anda
// Jangan gunakan trailing slash (garis miring di akhir)
define('BASE_URL', 'https://app.hookjourney.com');

define('TIMEZONE', 'Asia/Jakarta');

// Ubah ke 'production' saat sudah online agar error tidak tampil di browser
define('ENV', 'development');

// Konfigurasi Error Reporting otomatis
if (ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Set default timezone
date_default_timezone_set(TIMEZONE);