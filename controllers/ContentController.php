<?php

class ContentController extends Controller {

    public function kanban() {
        AuthMiddleware::handle();
        if (!isset($_SESSION['active_brand_id'])) $this->redirect('dashboard'); 
        
        $brandId = $_SESSION['active_brand_id'];
        BrandAccessMiddleware::checkAccess($brandId);

        $user = Auth::user();
        $brandModel = $this->model('Brand');
        $brands = $brandModel->getActiveBrandsByUser($user);
        
        $activeBrand = null;
        foreach ($brands as $b) {
            if ($b['id'] == $brandId) { $activeBrand = $b; break; }
        }

        $activeView = $_GET['v'] ?? 'kanban';
        if (!in_array($activeView, ['kanban', 'table', 'calendar', 'timeline'])) {
            $activeView = 'kanban';
        }

        $contentModel = $this->model('ContentItem');
        // Kuncinya: Pastikan findAllByBrand() di ContentItem Model men-join kolom u.avatar_url & u.phone.
        // Jika tidak, Anda tetap aman karena sistem Views sudah kita bungkus dengan isset() / empty() fallback.
        $rawItems = $contentModel->findAllByBrand($brandId);

        $columns = [
            'idea'         => ['title' => 'Ide Konten', 'items' => []],
            'script_ready' => ['title' => 'Script Ready', 'items' => []],
            'production'   => ['title' => 'Produksi (Shooting)', 'items' => []],
            'editing'      => ['title' => 'Editing', 'items' => []],
            'ready_post'   => ['title' => 'Siap / Terjadwal', 'items' => []],
            'published'    => ['title' => 'Published', 'items' => []]
        ];

        foreach ($rawItems as $item) {
            $status = $item['content_status'];
            if ($status === 'scheduled') $status = 'ready_post'; 
            if (isset($columns[$status])) {
                $columns[$status]['items'][] = $item;
            }
        }

        $calendarData = [];
        $monthStr = $_GET['month'] ?? date('Y-m');
        $firstDayOfMonth = date('Y-m-01', strtotime($monthStr));
        $lastDayOfMonth = date('Y-m-t', strtotime($monthStr));
        
        foreach ($rawItems as $item) {
            if (!empty($item['scheduled_post_at'])) {
                $dateOnly = date('Y-m-d', strtotime($item['scheduled_post_at']));
                if (!isset($calendarData[$dateOnly])) {
                    $calendarData[$dateOnly] = [];
                }
                $calendarData[$dateOnly][] = $item;
            }
        }

        $data = [
            'user' => $user,
            'brands' => $brands,
            'activeBrand' => $activeBrand,
            'pageTitle' => 'Content Planner',
            'activeView' => $activeView,
            'kanbanColumns' => $columns,
            'rawItems' => $rawItems,
            'monthStr' => $monthStr,
            'firstDayOfMonth' => $firstDayOfMonth,
            'lastDayOfMonth' => $lastDayOfMonth,
            'calendarData' => $calendarData
        ];

        $this->view('content/kanban', $data, 'app');
    }

    public function create() {
        AuthMiddleware::handle();
        if (!isset($_SESSION['active_brand_id'])) $this->redirect('dashboard');
        
        $brandId = $_SESSION['active_brand_id'];
        BrandAccessMiddleware::checkAccess($brandId);

        $user = Auth::user();
        $brandModel = $this->model('Brand');
        $brands = $brandModel->getActiveBrandsByUser($user);
        
        $activeBrand = null;
        foreach ($brands as $b) { if ($b['id'] == $brandId) { $activeBrand = $b; break; } }

        $pillarModel = $this->model('ContentPillar');
        $pillars = $pillarModel->getActiveByBrand($brandId);

        // FIX: Tarik avatar_url dan phone untuk digunakan saat memilih assignee (PIC)
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT DISTINCT u.id, u.full_name, u.role_global, u.avatar_url, u.phone FROM users u
                LEFT JOIN brand_members bm ON u.id = bm.user_id AND bm.brand_id = :brand_id_1
                WHERE ((u.workspace_id = :workspace_id AND u.role_global = 'leader') OR (bm.brand_id = :brand_id_2 AND bm.is_active = 1))
                AND u.is_active = 1 AND u.deleted_at IS NULL ORDER BY u.full_name ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute(['brand_id_1' => $brandId, 'workspace_id' => $user['workspace_id'], 'brand_id_2' => $brandId]);
        $teamMembers = $stmt->fetchAll();

        $data = [
            'user' => $user, 'brands' => $brands, 'activeBrand' => $activeBrand,
            'pageTitle' => 'Buat Ide Konten', 'pillars' => $pillars, 'teamMembers' => $teamMembers,
            'errors' => $_SESSION['form_errors'] ?? [], 'old' => $_SESSION['form_old'] ?? []
        ];
        unset($_SESSION['form_errors'], $_SESSION['form_old']);
        $this->view('content/form', $data, 'app');
    }

    public function edit() {
        AuthMiddleware::handle();
        $id = $_GET['id'] ?? null;
        if (!$id) $this->redirect('content/kanban');

        $contentModel = $this->model('ContentItem');
        $item = $contentModel->find($id);
        
        if (!$item || $item['brand_id'] != $_SESSION['active_brand_id']) $this->redirect('content/kanban');

        $user = Auth::user();
        $brandId = $_SESSION['active_brand_id'];
        $brandModel = $this->model('Brand');
        $brands = $brandModel->getActiveBrandsByUser($user);
        $activeBrand = null;
        foreach ($brands as $b) { if ($b['id'] == $brandId) { $activeBrand = $b; break; } }

        $pillarModel = $this->model('ContentPillar');
        $pillars = $pillarModel->getActiveByBrand($brandId);

        // FIX: Tarik avatar_url dan phone 
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT DISTINCT u.id, u.full_name, u.role_global, u.avatar_url, u.phone FROM users u
                LEFT JOIN brand_members bm ON u.id = bm.user_id AND bm.brand_id = :brand_id_1
                WHERE ((u.workspace_id = :workspace_id AND u.role_global = 'leader') OR (bm.brand_id = :brand_id_2 AND bm.is_active = 1))
                AND u.is_active = 1 AND u.deleted_at IS NULL ORDER BY u.full_name ASC";
        $stmt = $db->prepare($sql);
        $stmt->execute(['brand_id_1' => $brandId, 'workspace_id' => $user['workspace_id'], 'brand_id_2' => $brandId]);
        $teamMembers = $stmt->fetchAll();

        $data = [
            'user' => $user, 'brands' => $brands, 'activeBrand' => $activeBrand,
            'pageTitle' => 'Edit Konten', 'pillars' => $pillars, 'teamMembers' => $teamMembers,
            'item' => $item, 'old' => $item
        ];
        $this->view('content/form', $data, 'app');
    }

    public function store() {
        AuthMiddleware::handle();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('content/kanban');
        
        $brandId = $_SESSION['active_brand_id'];
        $validator = new Validator();
        if ($validator->validate($_POST, ['title' => 'required|max:200', 'goal' => 'required', 'content_type' => 'required'])) {
            $contentModel = $this->model('ContentItem');
            $platforms = isset($_POST['platforms']) ? json_encode($_POST['platforms']) : json_encode([]);

            $insertData = [
                'brand_id' => $brandId,
                'pillar_id' => !empty($_POST['pillar_id']) ? $_POST['pillar_id'] : null,
                'title' => $_POST['title'],
                'goal' => $_POST['goal'],
                'platforms_json' => $platforms,
                'content_type' => $_POST['content_type'],
                'angle' => $_POST['angle'] ?? null,
                'target_audience' => $_POST['target_audience'] ?? null,
                'hook_text' => $_POST['hook_text'] ?? null,
                'body_text' => $_POST['body_text'] ?? null,
                'cta_text' => $_POST['cta_text'] ?? null,
                'content_status' => 'idea',
                'priority' => $_POST['priority'] ?? 'medium',
                'pic_user_id' => !empty($_POST['pic_user_id']) ? $_POST['pic_user_id'] : null,
                'created_by' => Auth::user()['id'],
                'scheduled_post_at' => !empty($_POST['scheduled_post_at']) ? $_POST['scheduled_post_at'] : null
            ];

            $contentModel->insert($insertData);
            $this->redirect('content/kanban');
        } else {
            $_SESSION['form_errors'] = $validator->getErrors();
            $_SESSION['form_old'] = $_POST;
            $this->redirect('content/create');
        }
    }

    public function update() {
        AuthMiddleware::handle();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('content/kanban');
        
        $id = $_POST['id'] ?? null;
        $contentModel = $this->model('ContentItem');
        $item = $contentModel->find($id);
        if (!$item || $item['brand_id'] != $_SESSION['active_brand_id']) $this->redirect('content/kanban');

        $platforms = isset($_POST['platforms']) ? json_encode($_POST['platforms']) : json_encode([]);
        $updateData = [
            'pillar_id' => !empty($_POST['pillar_id']) ? $_POST['pillar_id'] : null,
            'title' => $_POST['title'],
            'goal' => $_POST['goal'],
            'platforms_json' => $platforms,
            'content_type' => $_POST['content_type'],
            'angle' => $_POST['angle'] ?? null,
            'target_audience' => $_POST['target_audience'] ?? null,
            'hook_text' => $_POST['hook_text'] ?? null,
            'body_text' => $_POST['body_text'] ?? null,
            'cta_text' => $_POST['cta_text'] ?? null,
            'priority' => $_POST['priority'] ?? 'medium',
            'pic_user_id' => !empty($_POST['pic_user_id']) ? $_POST['pic_user_id'] : null,
            'scheduled_post_at' => !empty($_POST['scheduled_post_at']) ? $_POST['scheduled_post_at'] : null
        ];

        $contentModel->update($id, $updateData);
        $this->redirect('content/kanban');
    }

    public function delete() {
        AuthMiddleware::handle();
        $id = $_POST['id'] ?? null;
        $contentModel = $this->model('ContentItem');
        $item = $contentModel->find($id);
        if ($item && $item['brand_id'] == $_SESSION['active_brand_id']) {
            $contentModel->softDelete($id);
        }
        $this->redirect('content/kanban');
    }

    public function updateStatus() {
        AuthMiddleware::handle();
        $id = $_POST['id'] ?? null;
        $newStatus = $_POST['status'] ?? null;
        
        if ($id && $newStatus) {
            $contentModel = $this->model('ContentItem');
            $contentModel->update($id, ['content_status' => $newStatus]);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
        exit;
    }
}