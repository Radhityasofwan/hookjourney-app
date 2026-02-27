<?php

class RoleMiddleware {
    
    /**
     * Mengecek role_global user ('leader', 'team', 'client')
     * Berguna untuk halaman seperti Master Settings yang hanya boleh diakses Leader.
     * * @param array $allowedRoles contoh: ['leader', 'team']
     */
    public static function handle($allowedRoles = []) {
        AuthMiddleware::handle(); // Pastikan login dulu
        
        $user = Auth::user();
        
        if (!in_array($user['role_global'], $allowedRoles)) {
            self::denyAccess();
        }
    }

    private static function denyAccess() {
        http_response_code(403);
        $errorFile = APP_PATH . '/views/errors/403.php';
        if (file_exists($errorFile)) {
            require_once $errorFile;
        } else {
            echo "403 Forbidden - Anda tidak memiliki izin (Role) untuk mengakses halaman ini.";
        }
        exit;
    }
}