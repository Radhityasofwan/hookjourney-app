<?php

class ShareLinkController extends Controller {

    // --- AREA INTERNAL (Butuh Login) ---
    public function index() {
        AuthMiddleware::handle();
        if (!isset($_SESSION['active_brand_id'])) $this->redirect('dashboard');
        
        $brandId = $_SESSION['active_brand_id'];
        BrandAccessMiddleware::checkAccess($brandId);

        $user = Auth::user();
        $brandModel = $this->model('Brand');
        $brands = $brandModel->getActiveBrandsByUser($user);
        $activeBrand = null;
        foreach ($brands as $b) { if ($b['id'] == $brandId) { $activeBrand = $b; break; } }

        $shareModel = $this->model('ShareLink');
        $links = $shareModel->getAllByBrand($brandId);

        $data = [
            'user' => $user,
            'brands' => $brands,
            'activeBrand' => $activeBrand,
            'pageTitle' => 'Client Portal Links',
            'links' => $links,
            'success_msg' => $_SESSION['share_success'] ?? null
        ];
        
        unset($_SESSION['share_success']);
        $this->view('shared/index', $data, 'app');
    }

    public function generate() {
        AuthMiddleware::handle();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('share-links');
        
        $brandId = $_SESSION['active_brand_id'];
        
        // Generate Token Unik 16 karakter (8 byte hex)
        $token = bin2hex(random_bytes(8));
        $moduleKey = $_POST['module_key'] ?? 'weekly_review';
        $expiresAt = !empty($_POST['expires_at']) ? date('Y-m-d H:i:s', strtotime($_POST['expires_at'])) : null;

        $shareModel = $this->model('ShareLink');
        $shareModel->insert([
            'brand_id' => $brandId,
            'module_key' => $moduleKey,
            'token' => $token,
            'expires_at' => $expiresAt,
            'created_by' => Auth::user()['id']
        ]);

        $_SESSION['share_success'] = "Tautan portal klien berhasil dibuat.";
        $this->redirect('share-links');
    }

    public function delete() {
        AuthMiddleware::handle();
        $user = Auth::user();
        if ($user['role_global'] !== 'leader') $this->redirect('share-links');

        $id = $_POST['id'] ?? null;
        if ($id) {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("DELETE FROM share_links WHERE id = ? AND brand_id = ?");
            $stmt->execute([$id, $_SESSION['active_brand_id']]);
            $_SESSION['share_success'] = "Akses tautan klien berhasil dicabut/dihapus.";
        }
        $this->redirect('share-links');
    }

    // --- AREA PUBLIK (Tanpa Login) ---
    public function portal() {
        $token = $_GET['token'] ?? null;
        if (!$token) die("Token tidak valid atau tidak ditemukan.");

        $shareModel = $this->model('ShareLink');
        $linkData = $shareModel->findByToken($token);

        if (!$linkData) {
            die("Tautan telah kedaluwarsa atau akses telah dicabut.");
        }

        $db = Database::getInstance()->getConnection();
        $stmtBrand = $db->prepare("SELECT name FROM brands WHERE id = ? LIMIT 1");
        $stmtBrand->execute([$linkData['brand_id']]);
        $brand = $stmtBrand->fetch();
        $brandName = $brand ? $brand['name'] : 'Brand';

        $contentData = [];
        
        // ====================================================================
        // FIX: PENARIKAN DATA MENDALAM UNTUK VISUALISASI KLIEN
        // ====================================================================
        if ($linkData['module_key'] == 'weekly_review') {
            // 1. Tarik Data Review Utama
            $sql = "SELECT * FROM weekly_reviews WHERE brand_id = ? AND review_status = 'published' ORDER BY week_start_date DESC LIMIT 1";
            $stmt = $db->prepare($sql);
            $stmt->execute([$linkData['brand_id']]);
            $review = $stmt->fetch();
            
            if ($review) {
                $contentData['review'] = $review;
                
                // 2. Tarik Visualisasi Metrik (Meta Ads, SEO, Content, Tickets)
                $stmtMetrics = $db->prepare("SELECT * FROM weekly_review_metrics WHERE weekly_review_id = ? ORDER BY section ASC");
                $stmtMetrics->execute([$review['id']]);
                $contentData['metrics'] = $stmtMetrics->fetchAll();
                
                // 3. Tarik Rencana Tindakan (Action Items)
                $stmtActions = $db->prepare("SELECT a.*, u.full_name as owner_name FROM weekly_review_action_items a LEFT JOIN users u ON a.owner_user_id = u.id WHERE a.weekly_review_id = ?");
                $stmtActions->execute([$review['id']]);
                $contentData['actions'] = $stmtActions->fetchAll();
            }
            
        } elseif ($linkData['module_key'] == 'dashboard') {
            // Tarik Ringkasan Dashboard (30 Hari Terakhir)
            $sql = "SELECT SUM(spend) as total_spend, SUM(results) as total_results, SUM(impressions) as total_impressions, SUM(clicks) as total_clicks 
                    FROM ad_performance_daily WHERE brand_id = ? AND report_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
            $stmt = $db->prepare($sql);
            $stmt->execute([$linkData['brand_id']]);
            $contentData['dashboard'] = $stmt->fetch();
            
            // Tarik Top Campaigns / Content Winning
            $sqlTop = "SELECT campaign_name, SUM(spend) as spend, SUM(results) as results, IF(SUM(results)>0, SUM(spend)/SUM(results), 0) as cpr 
                       FROM ad_performance_daily WHERE brand_id = ? AND report_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) 
                       GROUP BY campaign_name ORDER BY spend DESC LIMIT 5";
            $stmtTop = $db->prepare($sqlTop);
            $stmtTop->execute([$linkData['brand_id']]);
            $contentData['top_campaigns'] = $stmtTop->fetchAll();
        }

        $data = [
            'linkData' => $linkData,
            'contentData' => $contentData,
            'pageTitle' => 'Client Portal - ' . htmlspecialchars($brandName)
        ];

        $this->view('shared/view', $data, 'client_portal');
    }
}