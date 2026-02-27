<?php

class Router {
    private $routes = [];

    public function get($url, $controllerMethod, $middlewares = []) {
        $this->addRoute('GET', $url, $controllerMethod, $middlewares);
    }

    public function post($url, $controllerMethod, $middlewares = []) {
        $this->addRoute('POST', $url, $controllerMethod, $middlewares);
    }

    private function addRoute($method, $url, $controllerMethod, $middlewares) {
        $url = trim($url, '/');
        $this->routes[] = [
            'method' => $method,
            'url' => $url,
            'handler' => $controllerMethod,
            'middlewares' => $middlewares
        ];
    }

    public function run() {
        // Ambil URI yang direquest secara aman
        $requestUrl = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
        
        // Hapus Query String (contoh: ?id=1)
        $requestUrl = explode('?', $requestUrl)[0];
        
        // Tangani kasus aplikasi berjalan di sub-folder
        $basePath = dirname($_SERVER['SCRIPT_NAME']);
        if ($basePath !== '/' && $basePath !== '\\') {
            if (strpos($requestUrl, $basePath) === 0) {
                $requestUrl = substr($requestUrl, strlen($basePath));
            }
        }
        
        // Bersihkan slash awal dan akhir
        $requestUrl = trim($requestUrl, '/');
        $requestMethod = $_SERVER['REQUEST_METHOD'];

        // Cari route yang cocok
        foreach ($this->routes as $route) {
            if ($route['method'] === $requestMethod) {
                if ($route['url'] === $requestUrl) {
                    $this->dispatch($route);
                    return;
                }
            }
        }

        // Jika tidak ditemukan satupun, arahkan ke 404
        $this->handle404();
    }

    private function dispatch($route) {
        list($controllerName, $methodName) = explode('@', $route['handler']);
        
        // FIX FATAL ERROR: Gunakan class_exists yang akan memicu Autoloader di App.php
        // Ini mencegah bentrok require_once ganda dan mengatasi masalah case-sensitive di Linux/Hostinger
        if (class_exists($controllerName)) {
            $controller = new $controllerName();
            
            if (method_exists($controller, $methodName)) {
                try {
                    // Jalankan Method Controller di dalam Try-Catch
                    $controller->$methodName();
                } catch (\Throwable $e) {
                    // Tangkap Error Fatal / Exception dari dalam controller dan tampilkan dengan rapi
                    $this->handleError($e);
                }
            } else {
                die("Method '{$methodName}' tidak ditemukan di controller '{$controllerName}'");
            }
        } else {
            die("Controller '{$controllerName}' tidak ditemukan. Pastikan nama file sama persis dengan nama class.");
        }
    }

    private function handle404() {
        http_response_code(404);
        $errorFile = APP_PATH . '/views/errors/404.php';
        if(file_exists($errorFile)) {
            require_once $errorFile;
        } else {
            echo "<h1>404 - Halaman Tidak Ditemukan</h1>";
        }
        exit;
    }

    /**
     * Penangan Error Global (Mencegah White Screen of Death)
     */
    private function handleError(\Throwable $e) {
        http_response_code(500);
        
        // Jika environment development, tampilkan detail error
        if (defined('ENV') && ENV === 'development') {
            echo "<div style='font-family: monospace; background: #111126; color: #ff8e8e; padding: 30px; border-radius: 12px; margin: 40px; box-shadow: 0 10px 30px rgba(0,0,0,0.5);'>";
            echo "<h2 style='color: #ff4757; margin-top: 0;'>Sistem Menemukan Kesalahan (Fatal Error)</h2>";
            echo "<p style='font-size: 16px; color: white;'><strong>Pesan:</strong> " . $e->getMessage() . "</p>";
            echo "<p><strong>File Sumber:</strong> " . $e->getFile() . " pada baris <strong>" . $e->getLine() . "</strong></p>";
            echo "<hr style='border-color: #ffffff20; margin: 20px 0;'>";
            echo "<p style='color: #a4b0be; margin-bottom: 5px;'><strong>Stack Trace:</strong></p>";
            echo "<pre style='background: #000; padding: 15px; border-radius: 8px; color: #7bed9f; overflow-x: auto;'>" . $e->getTraceAsString() . "</pre>";
            echo "</div>";
        } else {
            // Jika production, tampilkan pesan aman
            echo "<div style='font-family: sans-serif; text-align: center; padding: 50px; background: #060609; color: white; height: 100vh;'>";
            echo "<h1 style='color: #ff4757;'>500 - Terjadi Kesalahan Internal</h1>";
            echo "<p style='color: #a4b0be;'>Sistem sedang mengalami gangguan saat memproses data. Tim teknis kami telah diberitahu.</p>";
            echo "<a href='" . base_url('dashboard') . "' style='display: inline-block; margin-top: 20px; padding: 10px 20px; background: #4361ee; color: white; text-decoration: none; border-radius: 8px;'>Kembali ke Dashboard</a>";
            echo "</div>";
        }
        exit;
    }
}