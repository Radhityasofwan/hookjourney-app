<?php

class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        // Load konfigurasi
        $config = require APP_PATH . '/config/database.php';

        $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['dbname']};charset={$config['charset']}";
        
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false, // Penting untuk keamanan (mencegah SQL Injection)
        ];

        try {
            $this->pdo = new PDO($dsn, $config['username'], $config['password'], $options);
        } catch (\PDOException $e) {
            // Jika production, jangan tampilkan detail error DB
            if (ENV === 'development') {
                die("Koneksi Database Gagal: " . $e->getMessage());
            } else {
                die("Terjadi kesalahan sistem. Silakan coba beberapa saat lagi.");
            }
        }
    }

    // Menerapkan Singleton Pattern
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // Mendapatkan object PDO
    public function getConnection() {
        return $this->pdo;
    }
}