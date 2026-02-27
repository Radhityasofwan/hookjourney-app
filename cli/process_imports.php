<?php
/**
 * CRON JOB: Process Meta Ads Imports
 * Dieksekusi setiap menit via cron (misal: * * * * * php /home/u830768701/domains/hookjourney.com/public_html/app/cli/process_imports.php)
 */

define('APP_PATH', dirname(__DIR__));
define('ENV', 'production'); // Pastikan error tidak tercetak merusak output CLI

// Muat konfigurasi DB
require_once APP_PATH . '/config/database.php';

// Mock Singleton Database & Autoloader minimalis untuk CLI
spl_autoload_register(function ($className) {
    $directories = ['core', 'models', 'services'];
    foreach ($directories as $dir) {
        $file = APP_PATH . '/' . $dir . '/' . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

echo "[" . date('Y-m-d H:i:s') . "] Memulai Background Process Ads Import...\n";

try {
    $service = new AdsImportService();
    $service->processPendingImports();
    echo "[" . date('Y-m-d H:i:s') . "] Pemrosesan selesai.\n";
} catch (Exception $e) {
    echo "[" . date('Y-m-d H:i:s') . "] CRITICAL ERROR: " . $e->getMessage() . "\n";
}