<?php

class Controller {
    
    // Fungsi untuk memuat view dengan layouting terpadu
    protected function view($viewName, $data = [], $layout = 'app') {
        // PERBAIKAN FINAL: Gunakan class View statis (dari core/View.php)
        // Ini memastikan view (misal: auth/login) dibungkus oleh layout (misal: layouts/auth)
        // sehingga halaman tidak blank dan CSS/JS ter-load dengan benar.
        View::render($viewName, $data, $layout);
    }

    // Fungsi untuk memuat model
    protected function model($modelName) {
        $modelFile = APP_PATH . '/models/' . $modelName . '.php';
        
        if (file_exists($modelFile)) {
            require_once $modelFile;
            return new $modelName();
        } else {
            die("Model {$modelName} tidak ditemukan.");
        }
    }

    // Fungsi helper untuk merespon JSON (untuk API / PWA)
    protected function jsonResponse($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    // Fungsi helper redirect
    protected function redirect($path) {
        header("Location: " . BASE_URL . "/" . ltrim($path, '/'));
        exit;
    }
}