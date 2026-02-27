<?php

class ForumController extends Controller
{

    public function index()
    {
        AuthMiddleware::handle();
        if (!isset($_SESSION['active_brand_id']))
            $this->redirect('dashboard');

        $brandId = $_SESSION['active_brand_id'];
        BrandAccessMiddleware::checkAccess($brandId);

        $user = Auth::user();
        $brandModel = $this->model('Brand');
        $brands = $brandModel->getActiveBrandsByUser($user);
        $activeBrand = null;
        foreach ($brands as $b) {
            if ($b['id'] == $brandId) {
                $activeBrand = $b;
                break;
            }
        }

        $page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $threadModel = $this->model('ForumThread');
        $threads = $threadModel->getAllByBrand($brandId, $limit, $offset);

        $catModel = $this->model('ForumCategory');
        $categories = $catModel->getActiveByBrand($brandId);

        $data = [
            'user' => $user,
            'brands' => $brands,
            'activeBrand' => $activeBrand,
            'pageTitle' => 'Forum Diskusi Tim',
            'threads' => $threads,
            'page' => $page,
            'categories' => $categories,
            'success_msg' => $_SESSION['forum_success'] ?? null
        ];

        unset($_SESSION['forum_success']);

        if (isset($_SERVER['HTTP_HX_REQUEST']) && $page > 1) {
            $this->view('forum/_threads', $data, false);
        } else {
            $this->view('forum/index', $data, 'app');
        }
    }

    public function store()
    {
        AuthMiddleware::handle();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST')
            $this->redirect('forum');

        $brandId = $_SESSION['active_brand_id'];
        BrandAccessMiddleware::checkAccess($brandId);

        $title = trim($_POST['title']);
        $postText = trim($_POST['post_text']);
        $categoryId = !empty($_POST['category_id']) ? $_POST['category_id'] : null;

        if (!empty($title) && !empty($postText)) {
            $db = Database::getInstance()->getConnection();
            $db->beginTransaction();
            try {
                // 1. Buat Thread Baru
                $threadModel = $this->model('ForumThread');
                $threadId = $threadModel->insert([
                    'brand_id' => $brandId,
                    'category_id' => $categoryId,
                    'title' => $title,
                    'created_by' => Auth::user()['id'],
                    'last_post_at' => date('Y-m-d H:i:s')
                ]);

                // 2. Buat Post Pertama (Isi dari thread)
                $postModel = $this->model('ForumPost');
                $postModel->insert([
                    'thread_id' => $threadId,
                    'user_id' => Auth::user()['id'],
                    'post_text' => $postText
                ]);

                $db->commit();
                $_SESSION['forum_success'] = "Thread diskusi berhasil dibuat.";
            } catch (Exception $e) {
                $db->rollBack();
            }
        }
        $this->redirect('forum');
    }

    public function show()
    {
        AuthMiddleware::handle();
        $brandId = $_SESSION['active_brand_id'];
        BrandAccessMiddleware::checkAccess($brandId);

        $threadId = $_GET['id'] ?? null;
        if (!$threadId)
            $this->redirect('forum');

        $threadModel = $this->model('ForumThread');
        $thread = $threadModel->find($threadId);

        if (!$thread || $thread['brand_id'] != $brandId || $thread['deleted_at'] != null) {
            $this->redirect('forum');
        }

        // Ambil Data Relasi
        $db = Database::getInstance()->getConnection();
        if ($thread['category_id']) {
            $stmtC = $db->prepare("SELECT name FROM forum_categories WHERE id = ?");
            $stmtC->execute([$thread['category_id']]);
            $cat = $stmtC->fetch();
            $thread['category_name'] = $cat ? $cat['name'] : null;
        }

        $postModel = $this->model('ForumPost');
        $posts = $postModel->getByThread($threadId);

        $user = Auth::user();
        $brandModel = $this->model('Brand');
        $brands = $brandModel->getActiveBrandsByUser($user);
        $activeBrand = null;
        foreach ($brands as $b) {
            if ($b['id'] == $brandId) {
                $activeBrand = $b;
                break;
            }
        }

        $data = [
            'user' => $user,
            'brands' => $brands,
            'activeBrand' => $activeBrand,
            'pageTitle' => $thread['title'],
            'thread' => $thread,
            'posts' => $posts
        ];

        $this->view('forum/show', $data, 'app');
    }

    public function reply()
    {
        AuthMiddleware::handle();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST')
            $this->redirect('forum');

        $threadId = $_POST['thread_id'];
        $postText = trim($_POST['post_text']);

        if (!empty($postText) && !empty($threadId)) {
            $postModel = $this->model('ForumPost');
            $postModel->insert([
                'thread_id' => $threadId,
                'user_id' => Auth::user()['id'],
                'post_text' => $postText
            ]);

            // Update timestamp thread
            $threadModel = $this->model('ForumThread');
            $threadModel->update($threadId, ['last_post_at' => date('Y-m-d H:i:s')]);
        }
        $this->redirect('forum/thread?id=' . $threadId);
    }

    // --- FIX: FITUR AKSI LEADER (PIN, LOCK, RESOLVE) ---
    public function updateStatus()
    {
        AuthMiddleware::handle();
        $user = Auth::user();

        // Proteksi: Hanya Leader
        if ($user['role_global'] !== 'leader')
            $this->redirect('forum');

        $id = $_POST['id'] ?? null;
        $action = $_POST['action'] ?? null;
        $val = $_POST['value'] ?? 0;

        $allowedActions = ['is_pinned', 'is_locked', 'is_resolved'];

        if ($id && in_array($action, $allowedActions)) {
            $threadModel = $this->model('ForumThread');
            $threadModel->update($id, [$action => $val]);
        }

        $this->redirect('forum/thread?id=' . $id);
    }

    // --- FIX: FITUR HAPUS THREAD (LEADER ONLY) ---
    public function delete()
    {
        AuthMiddleware::handle();
        $user = Auth::user();

        if ($user['role_global'] !== 'leader')
            $this->redirect('forum');

        $id = $_POST['id'] ?? null;
        if ($id) {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("UPDATE forum_threads SET deleted_at = CURRENT_TIMESTAMP WHERE id = ? AND brand_id = ?");
            $stmt->execute([$id, $_SESSION['active_brand_id']]);
            $_SESSION['forum_success'] = "Diskusi berhasil dihapus.";
        }

        $this->redirect('forum');
    }
}