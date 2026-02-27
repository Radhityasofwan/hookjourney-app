<?php

class KeywordController extends Controller {

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

        $keywordModel = $this->model('Keyword');
        $keywords = $keywordModel->getAllByBrand($brandId);

        // Menarik data clusters untuk opsi di Form
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT id, cluster_name FROM keyword_clusters WHERE brand_id = ? AND is_active = 1 ORDER BY cluster_name ASC");
        $stmt->execute([$brandId]);
        $clusters = $stmt->fetchAll();

        $data = [
            'user' => $user,
            'brands' => $brands,
            'activeBrand' => $activeBrand,
            'pageTitle' => 'SEO Keyword Hub',
            'keywords' => $keywords,
            'clusters' => $clusters,
            'success_msg' => $_SESSION['kw_success'] ?? null
        ];
        
        unset($_SESSION['kw_success']);
        $this->view('keywords/index', $data, 'app');
    }

    public function store() {
        AuthMiddleware::handle();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('keywords');
        
        $brandId = $_SESSION['active_brand_id'];
        BrandAccessMiddleware::checkAccess($brandId);
        
        $keywordText = trim($_POST['keyword_text']);
        
        if (!empty($keywordText)) {
            $keywordModel = $this->model('Keyword');
            $keywordModel->insert([
                'brand_id' => $brandId,
                'cluster_id' => !empty($_POST['cluster_id']) ? $_POST['cluster_id'] : null,
                'keyword_text' => $keywordText,
                'intent' => $_POST['intent'] ?? null,
                'volume' => !empty($_POST['volume']) ? (int)$_POST['volume'] : null,
                'difficulty' => !empty($_POST['difficulty']) ? (float)$_POST['difficulty'] : null,
                'keyword_status' => 'new',
                'priority' => $_POST['priority'] ?? 'medium'
            ]);
            $_SESSION['kw_success'] = "Keyword berhasil ditambahkan ke bank data.";
        }
        $this->redirect('keywords');
    }

    // FIX: Fungsi Baru untuk Tambah Cluster via AJAX
    public function storeCluster() {
        AuthMiddleware::handle();
        $brandId = $_SESSION['active_brand_id'] ?? null;
        $clusterName = trim($_POST['cluster_name'] ?? '');

        if ($brandId && !empty($clusterName)) {
            $db = Database::getInstance()->getConnection();
            try {
                $stmt = $db->prepare("INSERT INTO keyword_clusters (brand_id, cluster_name) VALUES (?, ?)");
                $stmt->execute([$brandId, $clusterName]);
                echo json_encode(['success' => true]);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'error' => 'Nama cluster sudah ada atau error DB.']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Nama tidak boleh kosong.']);
        }
        exit;
    }

    public function updateStatus() {
        AuthMiddleware::handle();
        $id = $_POST['id'] ?? null;
        $status = $_POST['status'] ?? null;
        
        if ($id && $status) {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("UPDATE keywords SET keyword_status = ? WHERE id = ? AND brand_id = ?");
            $stmt->execute([$status, $id, $_SESSION['active_brand_id']]);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
        exit;
    }

    public function delete() {
        AuthMiddleware::handle();
        $user = Auth::user();
        
        if ($user['role_global'] !== 'leader') $this->redirect('keywords');

        $id = $_POST['id'] ?? null;
        if ($id) {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("UPDATE keywords SET deleted_at = CURRENT_TIMESTAMP WHERE id = ? AND brand_id = ?");
            $stmt->execute([$id, $_SESSION['active_brand_id']]);
            $_SESSION['kw_success'] = "Keyword berhasil dihapus.";
        }
        
        $this->redirect('keywords');
    }
}