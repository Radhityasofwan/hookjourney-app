<?php

class ProfileController extends Controller {

    public function index() {
        AuthMiddleware::handle();
        $sessionUser = Auth::user();
        
        $db = Database::getInstance()->getConnection();
        
        // Tarik data lengkap user dari database agar email, phone, & avatar tidak NULL
        $stmtUser = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmtUser->execute([$sessionUser['id']]);
        $user = $stmtUser->fetch();
        
        $brandId = $_SESSION['active_brand_id'] ?? null;
        
        $brandModel = $this->model('Brand');
        $brands = $brandModel->getActiveBrandsByUser($user);
        $activeBrand = null;
        if($brandId) {
            foreach ($brands as $b) { 
                if ($b['id'] == $brandId) { $activeBrand = $b; break; } 
            }
        }

        $data = [
            'user' => $user,
            'brands' => $brands,
            'activeBrand' => $activeBrand,
            'pageTitle' => 'Pengaturan Profil',
            'success_msg' => $_SESSION['profile_success'] ?? null,
            'error_msg' => $_SESSION['profile_error'] ?? null
        ];
        
        unset($_SESSION['profile_success'], $_SESSION['profile_error']);
        $this->view('profile/index', $data, 'app');
    }

    public function update() {
        AuthMiddleware::handle();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('profile');

        $sessionUser = Auth::user();
        $db = Database::getInstance()->getConnection();

        $fullName = trim($_POST['full_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if(empty($fullName)) {
            $_SESSION['profile_error'] = "Nama lengkap tidak boleh kosong.";
            $this->redirect('profile');
        }

        // Normalisasi Nomor WhatsApp
        if (strpos($phone, '0') === 0) {
            $phone = '62' . substr($phone, 1);
        }
        $phone = preg_replace('/[^0-9]/', '', $phone);

        $updateParams = [
            'full_name' => $fullName,
            'phone' => $phone,
            'id' => $sessionUser['id']
        ];
        
        $sql = "UPDATE users SET full_name = :full_name, phone = :phone";

        if (!empty($password)) {
            $sql .= ", password_hash = :password_hash";
            $updateParams['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
        }

        // =========================================================================
        // FIX: Penyesuaian Path Absolut untuk Lingkungan Subdomain (APP_PATH)
        // =========================================================================
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                
                // Gunakan APP_PATH untuk memastikan gambar tersimpan di root document subdomain
                $uploadDir = APP_PATH . '/uploads/avatars/';
                
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                $fileName = 'avatar_' . $sessionUser['id'] . '_' . time() . '.' . $ext;
                $destPath = $uploadDir . $fileName;
                
                if (move_uploaded_file($_FILES['avatar']['tmp_name'], $destPath)) {
                    $sql .= ", avatar_url = :avatar_url";
                    $updateParams['avatar_url'] = 'uploads/avatars/' . $fileName;
                    
                    // Bersihkan avatar lama (Hapus file fisik)
                    $stmtOld = $db->prepare("SELECT avatar_url FROM users WHERE id = ?");
                    $stmtOld->execute([$sessionUser['id']]);
                    $oldUser = $stmtOld->fetch();
                    
                    if (!empty($oldUser['avatar_url'])) {
                        $oldFilePath = APP_PATH . '/' . ltrim($oldUser['avatar_url'], '/');
                        if (file_exists($oldFilePath)) {
                            unlink($oldFilePath);
                        }
                    }
                } else {
                    $_SESSION['profile_error'] = "Gagal memindahkan gambar. Pastikan folder uploads/avatars/ memiliki izin tulis (CHMOD 755/777).";
                    $this->redirect('profile');
                }
            } else {
                $_SESSION['profile_error'] = "Format foto tidak didukung. Harap gunakan JPG, PNG, atau WEBP.";
                $this->redirect('profile');
            }
        } elseif (isset($_FILES['avatar']) && $_FILES['avatar']['error'] !== UPLOAD_ERR_NO_FILE) {
            $phpUploadErrors = [
                1 => 'Ukuran file melebihi limit upload_max_filesize di pengaturan server (php.ini).',
                2 => 'Ukuran file melebihi limit MAX_FILE_SIZE.',
                3 => 'File hanya terunggah sebagian. Coba lagi.',
                4 => 'Tidak ada file yang diunggah.',
                6 => 'Folder temporary (tmp) di server hilang.',
                7 => 'Gagal menulis file ke disk server (Penyimpanan penuh/izin akses).'
            ];
            $errCode = $_FILES['avatar']['error'];
            $_SESSION['profile_error'] = "Error Sistem: " . ($phpUploadErrors[$errCode] ?? "Unknown error code ($errCode)");
            $this->redirect('profile');
        }
        // =========================================================================

        $sql .= " WHERE id = :id";
        
        try {
            $stmt = $db->prepare($sql);
            $stmt->execute($updateParams);
            
            $_SESSION['full_name'] = $fullName;
            $_SESSION['profile_success'] = "Profil Anda berhasil diperbarui.";
        } catch (Exception $e) {
            $_SESSION['profile_error'] = "Gagal memperbarui profil: " . $e->getMessage();
        }

        $this->redirect('profile');
    }
}