<?php

class TeamController extends Controller {

    public function index() {
        AuthMiddleware::handle();
        RoleMiddleware::handle([ROLE_LEADER]); // Hanya leader yang bisa kelola tim
        
        if (!isset($_SESSION['active_brand_id'])) $this->redirect('dashboard');
        
        $brandId = $_SESSION['active_brand_id'];
        $user = Auth::user();
        $brandModel = $this->model('Brand');
        $brands = $brandModel->getActiveBrandsByUser($user);
        
        $activeBrand = null;
        foreach ($brands as $b) { if ($b['id'] == $brandId) { $activeBrand = $b; break; } }

        // Ambil semua member untuk brand ini termasuk kolom permissions_json dan is_active
        $db = Database::getInstance()->getConnection();
        
        // FIX: Tarik data member beserta avatar_url dan phone agar UI tidak rusak
        $sql = "SELECT u.id, u.full_name, u.email, u.phone, u.avatar_url, u.role_global, 
                       bm.role_in_brand, bm.permissions_json, bm.is_active as member_active, bm.id as member_id 
                FROM users u
                JOIN brand_members bm ON u.id = bm.user_id
                WHERE bm.brand_id = :brand_id AND u.deleted_at IS NULL";
                
        $stmt = $db->prepare($sql);
        $stmt->execute(['brand_id' => $brandId]);
        $members = $stmt->fetchAll();

        $data = [
            'user' => $user,
            'brands' => $brands,
            'activeBrand' => $activeBrand,
            'pageTitle' => 'Manajemen Hak Akses Tim',
            'members' => $members,
            'success_msg' => $_SESSION['team_success'] ?? null,
            'error_msg' => $_SESSION['team_error'] ?? null
        ];

        unset($_SESSION['team_success'], $_SESSION['team_error']);
        $this->view('brands/members', $data, 'app');
    }

    public function store() {
        AuthMiddleware::handle();
        RoleMiddleware::handle([ROLE_LEADER]);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('brand/team');

        $brandId = $_SESSION['active_brand_id'];
        $workspaceId = Auth::user()['workspace_id'];
        $fullName = trim($_POST['full_name']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $phone = trim($_POST['phone'] ?? '');
        $role = $_POST['role_in_brand']; // 'team' atau 'client'

        // Tangkap array permissions dan simpan sebagai JSON string
        $permissions = isset($_POST['permissions']) ? json_encode($_POST['permissions']) : json_encode([]);

        // Normalisasi Nomor WhatsApp
        if (!empty($phone)) {
            if (strpos($phone, '0') === 0) $phone = '62' . substr($phone, 1);
            $phone = preg_replace('/[^0-9]/', '', $phone);
        }

        $db = Database::getInstance()->getConnection();

        try {
            $db->beginTransaction();

            // 1. Cek apakah email sudah terdaftar di tabel users
            $stmt = $db->prepare("SELECT id, role_global FROM users WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);
            $existingUser = $stmt->fetch();

            if ($existingUser) {
                $userId = $existingUser['id'];
                
                // Jangan downgrade role jika user tersebut aslinya adalah Leader di workspace-nya sendiri
                if ($existingUser['role_global'] !== ROLE_LEADER && $existingUser['role_global'] !== $role) {
                    $updateUser = $db->prepare("UPDATE users SET role_global = ?, is_active = 1 WHERE id = ?");
                    $updateUser->execute([$role, $userId]);
                }

                // 2. Cek apakah user sudah terdaftar di brand_members untuk brand aktif ini
                $stmtM = $db->prepare("SELECT id FROM brand_members WHERE brand_id = ? AND user_id = ?");
                $stmtM->execute([$brandId, $userId]);
                $existingMember = $stmtM->fetch();

                if ($existingMember) {
                    // Update Izin dan PAKSA status aktif ke 1 (Fix masalah stuck)
                    $stmtU = $db->prepare("UPDATE brand_members SET role_in_brand = ?, permissions_json = ?, is_active = 1 WHERE id = ?");
                    $stmtU->execute([$role, $permissions, $existingMember['id']]);
                } else {
                    // Masukkan sebagai member baru di brand ini dengan status aktif
                    $stmtI = $db->prepare("INSERT INTO brand_members (brand_id, user_id, role_in_brand, permissions_json, is_active) VALUES (?, ?, ?, ?, 1)");
                    $stmtI->execute([$brandId, $userId, $role, $permissions]);
                }
            } else {
                // 3. Buat user baru jika belum ada
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $db->prepare("INSERT INTO users (workspace_id, full_name, email, phone, password_hash, role_global, is_active) VALUES (?, ?, ?, ?, ?, ?, 1)");
                $stmt->execute([$workspaceId, $fullName, $email, $phone, $hashedPassword, $role]);
                $userId = $db->lastInsertId();

                // Hubungkan ke Brand dengan status aktif (is_active = 1)
                $stmtI = $db->prepare("INSERT INTO brand_members (brand_id, user_id, role_in_brand, permissions_json, is_active) VALUES (?, ?, ?, ?, 1)");
                $stmtI->execute([$brandId, $userId, $role, $permissions]);
            }

            $db->commit();
            $_SESSION['team_success'] = "Anggota '{$fullName}' berhasil diaktifkan dengan hak akses khusus.";
        } catch (Exception $e) {
            if ($db->inTransaction()) $db->rollBack();
            $_SESSION['team_error'] = "Gagal konfigurasi tim: " . $e->getMessage();
        }

        $this->redirect('brand/team');
    }

    public function delete() {
        AuthMiddleware::handle();
        RoleMiddleware::handle([ROLE_LEADER]);
        
        $memberId = $_POST['member_id'] ?? null;
        if ($memberId) {
            $db = Database::getInstance()->getConnection();
            
            // FIX: Gunakan Hard Delete pada tabel pivot agar data benar-benar bersih dari view
            // Atau gunakan Soft Delete (UPDATE is_active = 0) jika ingin mempertahankan history.
            // Di sini kita gunakan DELETE permanen pada tabel PIVOT (user tetap aman di tabel users).
            $stmt = $db->prepare("DELETE FROM brand_members WHERE id = ?");
            $stmt->execute([$memberId]);
            
            $_SESSION['team_success'] = "Akses anggota telah dicabut dari brand ini.";
        }
        $this->redirect('brand/team');
    }
}