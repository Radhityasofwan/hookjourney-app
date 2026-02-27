<?php

class ApiAuthMiddleware {
    
    /**
     * Khusus untuk endpoint API (PWA Offline Sync / AJAX).
     * Jika tidak punya akses, balas dengan JSON 401 Unauthorized, bukan redirect HTML.
     */
    public static function handle() {
        Auth::init();
        
        if (!Auth::check()) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode([
                'status'  => 'error',
                'message' => 'Unauthorized. Sesi Anda mungkin telah habis. Silakan login ulang.'
            ]);
            exit;
        }
    }
}