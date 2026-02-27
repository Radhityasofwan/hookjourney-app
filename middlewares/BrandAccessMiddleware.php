<?php

class BrandAccessMiddleware {
    
    /**
     * Gerbang Utama Akses Brand.
     * Memastikan user hanya bisa melihat data brand yang ditugaskan kepadanya.
     */
    public static function checkAccess($brandId = null) {
        // 1. Pastikan user login terlebih dahulu
        AuthMiddleware::handle(); 
        
        $user = Auth::user();
        $db = Database::getInstance()->getConnection();

        // 2. Jika brandId null, coba ambil dari session
        if ($brandId === null) {
            $brandId = $_SESSION['active_brand_id'] ?? null;
        }

        // 3. Jika tetap tidak ada brandId, paksa ke dashboard untuk inisialisasi brand
        if (!$brandId) {
            header("Location: " . base_url('dashboard'));
            exit;
        }

        // --- LOGIKA 1: PRIORITAS UNTUK TEAM / CLIENT ---
        // Cek apakah user terdaftar sebagai member aktif di brand tersebut.
        // Kita join dengan tabel brands untuk memastikan brand itu sendiri juga aktif.
        $sqlM = "SELECT bm.role_in_brand 
                 FROM brand_members bm
                 INNER JOIN brands b ON bm.brand_id = b.id
                 WHERE bm.brand_id = ? AND bm.user_id = ? AND bm.is_active = 1 AND b.is_active = 1 
                 LIMIT 1";
        
        $stmtM = $db->prepare($sqlM);
        $stmtM->execute([$brandId, $user['id']]);
        $membership = $stmtM->fetch();

        if ($membership) {
            return true; // Akses Diizinkan (Lolos)
        }

        // --- LOGIKA 2: KHUSUS LEADER WORKSPACE ---
        // Jika tidak terdaftar di brand_members, cek apakah dia adalah Leader 
        // yang memiliki brand tersebut di workspacenya.
        if ($user['role_global'] === ROLE_LEADER) {
            $sqlL = "SELECT id FROM brands WHERE id = ? AND workspace_id = ? AND is_active = 1 LIMIT 1";
            $stmtL = $db->prepare($sqlL);
            $stmtL->execute([$brandId, $user['workspace_id']]);
            
            if ($stmtL->fetch()) {
                return true; // Akses Diizinkan (Lolos)
            }
        }

        // --- FINAL: DENY ACCESS ---
        // Jika kedua logika di atas gagal, user tidak punya hak di brand ini.
        self::denyAccess();
    }

    /**
     * Menampilkan halaman error 403 atau pesan penolakan
     */
    private static function denyAccess() {
        http_response_code(403);
        
        // Coba load view error jika ada, jika tidak tampilkan pesan standar
        $errorFile = APP_PATH . '/views/errors/403.php';
        if (file_exists($errorFile)) {
            require_once $errorFile;
        } else {
            echo "<div style='font-family:sans-serif; text-align:center; padding:50px;'>";
            echo "<h1 style='color:#e74c3c;'>403 - Akses Terbatas</h1>";
            echo "<p>Maaf, Anda tidak memiliki izin untuk mengakses Brand ini atau akun Anda sedang dinonaktifkan.</p>";
            echo "<a href='".base_url('dashboard')."'>Kembali ke Dashboard</a>";
            echo "</div>";
        }
        exit;
    }
}