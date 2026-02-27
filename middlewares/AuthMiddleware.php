<?php

class AuthMiddleware {
    
    /**
     * Mengecek apakah user sudah login.
     * Jika belum, lempar kembali ke halaman login.
     */
    public static function handle() {
        Auth::init();
        
        if (!Auth::check()) {
            // Catat URL yang ingin diakses sebelum dilempar ke login (opsional untuk fitur redirect back)
            $_SESSION['redirect_url'] = current_uri();
            
            redirect('/auth/login');
        }
    }
}