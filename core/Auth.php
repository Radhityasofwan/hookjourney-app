<?php

class Auth {
    
    // Inisialisasi session dengan aman
    public static function init() {
        if (session_status() === PHP_SESSION_NONE) {
            // Set session agar lebih aman (Mencegah Session Hijacking)
            ini_set('session.cookie_httponly', 1);
            ini_set('session.use_only_cookies', 1);
            session_start();
        }
    }

    // Login user (simpan data esensial di session)
    public static function login($user) {
        self::init();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['role_global'] = $user['role_global'];
        $_SESSION['workspace_id'] = $user['workspace_id'];
        
        // Buat ID session baru untuk keamanan
        session_regenerate_id(true);
    }

    // Cek apakah user sedang login
    public static function check() {
        self::init();
        return isset($_SESSION['user_id']);
    }

    // Ambil data user yang sedang login
    public static function user() {
        self::init();
        if (self::check()) {
            return [
                'id' => $_SESSION['user_id'],
                'full_name' => $_SESSION['full_name'],
                'role_global' => $_SESSION['role_global'],
                'workspace_id' => $_SESSION['workspace_id']
            ];
        }
        return null;
    }

    // Logout dan hancurkan session
    public static function logout() {
        self::init();
        $_SESSION = [];
        session_destroy();
    }
}