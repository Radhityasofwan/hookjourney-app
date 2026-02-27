<?php

class BrandController extends Controller {
    
    public function switch() {
        AuthMiddleware::handle();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['brand_id'])) {
            $brandId = (int) $_POST['brand_id'];
            BrandAccessMiddleware::checkAccess($brandId);
            $_SESSION['active_brand_id'] = $brandId;
        }
        
        $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url('dashboard');
        $this->redirect(str_replace(BASE_URL . '/', '', $referer));
    }

    public function create() {
        AuthMiddleware::handle();
        RoleMiddleware::handle([ROLE_LEADER]);

        $user = Auth::user();
        $brandModel = $this->model('Brand');
        $brands = $brandModel->getActiveBrandsByUser($user);

        $activeBrand = null;
        if (isset($_SESSION['active_brand_id'])) {
            foreach ($brands as $b) { 
                if ($b['id'] == $_SESSION['active_brand_id']) { $activeBrand = $b; break; } 
            }
        }

        $data = [
            'user' => $user,
            'brands' => $brands,
            'activeBrand' => $activeBrand,
            'pageTitle' => 'Setup Brand Baru',
            'error_msg' => $_SESSION['brand_error'] ?? null
        ];
        
        unset($_SESSION['brand_error']);
        $this->view('brands/create', $data, 'app');
    }

    public function store() {
        AuthMiddleware::handle();
        RoleMiddleware::handle([ROLE_LEADER]);
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('dashboard');
        }

        $user = Auth::user();
        $name = trim($_POST['name'] ?? '');
        $industry = trim($_POST['industry'] ?? '');
        $currency = $_POST['currency'] ?? 'IDR';
        $timezone = $_POST['timezone'] ?? 'Asia/Jakarta';

        if (empty($name)) {
            $_SESSION['brand_error'] = "Nama brand tidak boleh kosong.";
            $this->redirect('brand/create');
        }

        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
        $slug = $slug . '-' . substr(time(), -4);

        $brandModel = $this->model('Brand');
        
        $newBrandId = $brandModel->insert([
            'workspace_id' => $user['workspace_id'],
            'name' => $name,
            'slug' => $slug,
            'industry' => $industry,
            'timezone' => $timezone,
            'currency' => $currency,
            'is_active' => 1
        ]);

        $_SESSION['active_brand_id'] = $newBrandId;
        $_SESSION['dashboard_success'] = "Selamat! Brand {$name} berhasil dibuat. Mulai kelola operasional Anda.";

        $this->redirect('dashboard');
    }

    public function settings() {
        AuthMiddleware::handle();
        
        if (!isset($_SESSION['active_brand_id'])) {
            $this->redirect('dashboard');
        }
        
        $brandId = $_SESSION['active_brand_id'];
        BrandAccessMiddleware::checkAccess($brandId);

        $user = Auth::user();
        $brandModel = $this->model('Brand');
        $brands = $brandModel->getActiveBrandsByUser($user);
        
        $activeBrand = null;
        foreach ($brands as $b) {
            if ($b['id'] == $brandId) { $activeBrand = $b; break; }
        }

        $adAccountModel = $this->model('AdAccount');
        $adAccounts = $adAccountModel->getActiveByBrand($brandId);

        $pillarModel = $this->model('ContentPillar');
        $pillars = $pillarModel->getActiveByBrand($brandId);

        // FIX: Tarik Master Data untuk Kategori Tiket dan Forum
        $ticketCatModel = $this->model('TicketCategory');
        $ticketCategories = $ticketCatModel->getActiveByBrand($brandId);

        $forumCatModel = $this->model('ForumCategory');
        $forumCategories = $forumCatModel->getActiveByBrand($brandId);

        $data = [
            'user' => $user,
            'brands' => $brands,
            'activeBrand' => $activeBrand,
            'pageTitle' => 'Pengaturan Brand',
            'adAccounts' => $adAccounts,
            'pillars' => $pillars,
            'ticketCategories' => $ticketCategories,
            'forumCategories' => $forumCategories,
            'success_msg' => $_SESSION['setting_success'] ?? null,
            'error_msg' => $_SESSION['setting_error'] ?? null
        ];

        unset($_SESSION['setting_success'], $_SESSION['setting_error']);

        $this->view('brands/settings', $data, 'app');
    }

    public function storeAdAccount() {
        AuthMiddleware::handle();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('brand/settings');
        
        $brandId = $_SESSION['active_brand_id'];
        BrandAccessMiddleware::checkAccess($brandId);

        $adAccountModel = $this->model('AdAccount');
        
        $insertData = [
            'brand_id' => $brandId,
            'platform' => 'meta',
            'account_name' => trim($_POST['account_name']),
            'account_id_external' => trim($_POST['account_id_external']) ?: null,
            'currency' => $_POST['currency'] ?? 'IDR',
            'timezone' => $_POST['timezone'] ?? 'Asia/Jakarta',
            'is_active' => 1
        ];

        if (!empty($insertData['account_name'])) {
            $adAccountModel->insert($insertData);
            $_SESSION['setting_success'] = "Ad Account berhasil ditambahkan.";
        } else {
            $_SESSION['setting_error'] = "Nama Akun Iklan wajib diisi.";
        }

        $this->redirect('brand/settings');
    }

    public function storePillar() {
        AuthMiddleware::handle();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('brand/settings');
        
        $brandId = $_SESSION['active_brand_id'];
        BrandAccessMiddleware::checkAccess($brandId);

        $pillarModel = $this->model('ContentPillar');
        
        $insertData = [
            'brand_id' => $brandId,
            'name' => trim($_POST['name']),
            'description' => trim($_POST['description']) ?: null,
            'is_active' => 1
        ];

        if (!empty($insertData['name'])) {
            $pillarModel->insert($insertData);
            $_SESSION['setting_success'] = "Pilar Konten berhasil ditambahkan.";
        } else {
            $_SESSION['setting_error'] = "Nama Pilar wajib diisi.";
        }

        $this->redirect('brand/settings');
    }

    // FIX: Fungsi Baru Simpan Kategori Tiket
    public function storeTicketCategory() {
        AuthMiddleware::handle();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('brand/settings');
        
        $brandId = $_SESSION['active_brand_id'];
        BrandAccessMiddleware::checkAccess($brandId);

        $model = $this->model('TicketCategory');
        $name = trim($_POST['name']);
        $color = $_POST['color_hex'] ?? '#3b82f6';

        if (!empty($name)) {
            $model->insert([
                'brand_id' => $brandId,
                'name' => $name,
                'color_hex' => $color,
                'is_active' => 1
            ]);
            $_SESSION['setting_success'] = "Kategori Tiket '{$name}' berhasil ditambahkan.";
        } else {
            $_SESSION['setting_error'] = "Nama Kategori Tiket wajib diisi.";
        }

        $this->redirect('brand/settings');
    }

    // FIX: Fungsi Baru Simpan Kategori Forum
    public function storeForumCategory() {
        AuthMiddleware::handle();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('brand/settings');
        
        $brandId = $_SESSION['active_brand_id'];
        BrandAccessMiddleware::checkAccess($brandId);

        $model = $this->model('ForumCategory');
        $name = trim($_POST['name']);
        $desc = trim($_POST['description']) ?: null;

        if (!empty($name)) {
            $model->insert([
                'brand_id' => $brandId,
                'name' => $name,
                'description' => $desc,
                'is_active' => 1
            ]);
            $_SESSION['setting_success'] = "Kategori Forum '{$name}' berhasil ditambahkan.";
        } else {
            $_SESSION['setting_error'] = "Nama Kategori Forum wajib diisi.";
        }

        $this->redirect('brand/settings');
    }
}