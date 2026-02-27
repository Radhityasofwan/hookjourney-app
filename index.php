<?php
/**
 * HOOKJOURNEY (GrowthOps Hub)
 * Entry Point Utama (Front Controller)
 * Posisi: Document Root (app.hookjourney.com)
 */

// 1. Tentukan path konstan untuk root aplikasi
define('PUBLIC_PATH', __DIR__);

// 2. Muat class utama App dari direktori core/ (karena berada di folder yang sama)
require_once __DIR__ . '/core/App.php';

// 3. Inisialisasi dan jalankan aplikasi
$app = new App();
$app->run();