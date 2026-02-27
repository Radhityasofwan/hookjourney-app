<?php

class CreativeController extends Controller {

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

        $creativeModel = $this->model('CreativeAsset');
        $assets = $creativeModel->getAllByBrand($brandId);

        $data = [
            'user' => $user,
            'brands' => $brands,
            'activeBrand' => $activeBrand,
            'pageTitle' => 'Creative Library',
            'assets' => $assets,
            'success_msg' => $_SESSION['creative_success'] ?? null,
            'error_msg' => $_SESSION['creative_error'] ?? null
        ];
        
        unset($_SESSION['creative_success'], $_SESSION['creative_error']);
        $this->view('creative/index', $data, 'app');
    }

    public function store() {
        AuthMiddleware::handle();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('creative');
        
        $brandId = $_SESSION['active_brand_id'];
        BrandAccessMiddleware::checkAccess($brandId);

        $title = trim($_POST['title']);
        $assetType = $_POST['asset_type'] ?? 'video';

        if (empty($title)) {
            $_SESSION['creative_error'] = "Judul aset wajib diisi.";
            $this->redirect('creative');
        }

        $filePath = null;
        
        // Penanganan Upload File Aset
        if (isset($_FILES['asset_file']) && $_FILES['asset_file']['error'] === UPLOAD_ERR_OK) {
            $tmpPath = $_FILES['asset_file']['tmp_name'];
            $fileName = time() . '_' . preg_replace("/[^a-zA-Z0-9.]/", "_", $_FILES['asset_file']['name']);
            $destPath = UPLOAD_DIR_CREATIVE . $fileName;

            if (move_uploaded_file($tmpPath, $destPath)) {
                $filePath = 'uploads/creative_assets/' . $fileName;
            } else {
                $_SESSION['creative_error'] = "Gagal mengunggah file.";
                $this->redirect('creative');
            }
        }

        $creativeModel = $this->model('CreativeAsset');
        $creativeModel->insert([
            'brand_id' => $brandId,
            'asset_type' => $assetType,
            'title' => $title,
            'file_path' => $filePath,
            'asset_source' => 'manual',
            'uploaded_by' => Auth::user()['id']
        ]);

        $_SESSION['creative_success'] = "Aset kreatif berhasil ditambahkan ke library.";
        $this->redirect('creative');
    }
}