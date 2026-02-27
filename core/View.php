<?php

class View {
    
    /**
     * Render view file dan tempelkan ke dalam layout (Templating Engine sederhana)
     * @param string $viewName (contoh: 'dashboard/index')
     * @param array $data (Variabel yang dikirim ke view)
     * @param string|null $layout (contoh: 'app' untuk views/layouts/app.php)
     */
    public static function render($viewName, $data = [], $layout = 'app') {
        // Ekstrak data agar bisa dipanggil langsung dengan nama variabel ($nama_variabel)
        extract($data);
        
        // Path konten view
        $viewFile = APP_PATH . '/views/' . ltrim($viewName, '/') . '.php';
        
        if (!file_exists($viewFile)) {
            die("Error: View '{$viewName}' tidak ditemukan di {$viewFile}");
        }

        // Tangkap output view ke dalam buffer (jangan tampilkan dulu)
        ob_start();
        require $viewFile;
        $content = ob_get_clean(); // Masukkan output ke variabel $content

        $isHtmxRequest = isset($_SERVER['HTTP_HX_REQUEST']) && $_SERVER['HTTP_HX_REQUEST'] == 'true';

        // Jika menggunakan layout dan bukan request HTMX, masukkan $content ke dalam layout
        if ($layout && !$isHtmxRequest) {
            $layoutFile = APP_PATH . '/views/layouts/' . $layout . '.php';
            if (file_exists($layoutFile)) {
                require $layoutFile; // Layout file ini HARUS me-echo $content di dalamnya
            } else {
                die("Error: Layout '{$layout}' tidak ditemukan di {$layoutFile}");
            }
        } else {
            // Jika tidak pakai layout atau via HTMX, tampilkan langsung
            echo $content;
        }
    }
}