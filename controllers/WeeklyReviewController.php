<?php

class WeeklyReviewController extends Controller {

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

        $reviewModel = $this->model('WeeklyReview');
        $reviews = $reviewModel->getAllByBrand($brandId);

        $data = [
            'user' => $user,
            'brands' => $brands,
            'activeBrand' => $activeBrand,
            'pageTitle' => 'Weekly Review Hub',
            'reviews' => $reviews,
            'success_msg' => $_SESSION['review_success'] ?? null,
            'error_msg' => $_SESSION['review_error'] ?? null
        ];
        
        unset($_SESSION['review_success'], $_SESSION['review_error']);

        $this->view('reviews/index', $data, 'app');
    }

    public function generate() {
        AuthMiddleware::handle();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('reviews');
        
        $brandId = $_SESSION['active_brand_id'];
        BrandAccessMiddleware::checkAccess($brandId);

        $startDate = $_POST['week_start_date'] ?? '';
        $endDate = $_POST['week_end_date'] ?? '';

        if (empty($startDate) || empty($endDate)) {
            $_SESSION['review_error'] = "Pilih rentang tanggal minggu yang valid.";
            $this->redirect('reviews');
        }

        $reviewModel = $this->model('WeeklyReview');
        
        $existing = $reviewModel->checkExists($brandId, $startDate, $endDate);
        if ($existing) {
            $this->redirect('reviews/edit?id=' . $existing['id']);
        }

        $service = new WeeklyReviewService();
        $metrics = $service->generateDraftData($brandId, $startDate, $endDate);

        $reviewId = $reviewModel->insert([
            'brand_id' => $brandId,
            'week_start_date' => $startDate,
            'week_end_date' => $endDate,
            'review_status' => 'draft',
            'generated_by' => Auth::user()['id']
        ]);

        $metricModel = $this->model('WeeklyReviewMetric');
        foreach ($metrics as $m) {
            $metricModel->insert([
                'weekly_review_id' => $reviewId,
                'metric_key' => $m['key'],
                'metric_label' => $m['label'],
                'metric_value' => $m['value'],
                'section' => $m['section']
            ]);
        }

        $_SESSION['review_success'] = "Draf Evaluasi berhasil di-generate. Silakan lengkapi insight manual.";
        $this->redirect('reviews/edit?id=' . $reviewId);
    }

    public function edit() {
        AuthMiddleware::handle();
        $brandId = $_SESSION['active_brand_id'];
        BrandAccessMiddleware::checkAccess($brandId);

        $reviewId = $_GET['id'] ?? null;
        if (!$reviewId) $this->redirect('reviews');

        $reviewModel = $this->model('WeeklyReview');
        $review = $reviewModel->find($reviewId);

        if (!$review || $review['brand_id'] != $brandId || $review['deleted_at'] != null) {
            $this->redirect('reviews');
        }

        $metricModel = $this->model('WeeklyReviewMetric');
        $metrics = $metricModel->getByReviewId($reviewId);

        // Ambil Action Items yang sudah disimpan
        $actionModel = $this->model('WeeklyReviewActionItem');
        $actionItems = $actionModel->getByReviewId($reviewId);

        $user = Auth::user();
        $brandModel = $this->model('Brand');
        $brands = $brandModel->getActiveBrandsByUser($user);
        $activeBrand = null;
        foreach ($brands as $b) { if ($b['id'] == $brandId) { $activeBrand = $b; break; } }

        // Ambil Data Member untuk Assignee Action Items
        $db = Database::getInstance()->getConnection();
        $sqlTeam = "SELECT DISTINCT u.id, u.full_name, u.role_global FROM users u
                LEFT JOIN brand_members bm ON u.id = bm.user_id AND bm.brand_id = :brand_id_1
                WHERE ((u.workspace_id = :workspace_id AND u.role_global = 'leader') OR (bm.brand_id = :brand_id_2 AND bm.is_active = 1))
                AND u.is_active = 1 AND u.deleted_at IS NULL ORDER BY u.full_name ASC";
        $stmtTeam = $db->prepare($sqlTeam);
        $stmtTeam->execute(['brand_id_1' => $brandId, 'workspace_id' => $user['workspace_id'], 'brand_id_2' => $brandId]);

        $data = [
            'user' => $user,
            'brands' => $brands,
            'activeBrand' => $activeBrand,
            'pageTitle' => 'Edit Draf Weekly Review',
            'review' => $review,
            'metrics' => $metrics,
            'actionItems' => $actionItems,
            'teamMembers' => $stmtTeam->fetchAll(),
            'success_msg' => $_SESSION['review_success'] ?? null
        ];
        
        unset($_SESSION['review_success']);
        $this->view('reviews/form', $data, 'app');
    }

    public function update() {
        AuthMiddleware::handle();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('reviews');
        
        $brandId = $_SESSION['active_brand_id'];
        $reviewId = $_POST['review_id'] ?? null;

        if ($reviewId) {
            $db = Database::getInstance()->getConnection();
            $reviewModel = $this->model('WeeklyReview');
            
            $status = isset($_POST['publish']) ? 'published' : 'draft';
            
            $updateData = [
                'summary_text' => $_POST['summary_text'] ?? null,
                'top_issues_text' => $_POST['top_issues_text'] ?? null,
                'insights_text' => $_POST['insights_text'] ?? null,
                // Menggabungkan text murni jika masih diisi
                'next_actions_text' => $_POST['next_actions_text'] ?? null,
                'review_status' => $status
            ];

            if ($status === 'published') {
                $updateData['published_by'] = Auth::user()['id'];
                $updateData['published_at'] = date('Y-m-d H:i:s');
            }

            // Mulai Transaksi
            $db->beginTransaction();
            try {
                // 1. Update Review Main Data
                $reviewModel->update($reviewId, $updateData);

                // 2. Hapus Action Items Lama (Overwrite Strategy)
                $stmtDel = $db->prepare("DELETE FROM weekly_review_action_items WHERE weekly_review_id = ?");
                $stmtDel->execute([$reviewId]);

                // 3. Masukkan Action Items Baru dari Array Form (Dinamis)
                if (isset($_POST['action_titles']) && is_array($_POST['action_titles'])) {
                    $stmtIns = $db->prepare("INSERT INTO weekly_review_action_items (weekly_review_id, title, owner_user_id, due_date) VALUES (?, ?, ?, ?)");
                    foreach ($_POST['action_titles'] as $idx => $title) {
                        if (!empty(trim($title))) {
                            $owner = !empty($_POST['action_owners'][$idx]) ? $_POST['action_owners'][$idx] : null;
                            $due = !empty($_POST['action_dues'][$idx]) ? $_POST['action_dues'][$idx] : null;
                            $stmtIns->execute([$reviewId, trim($title), $owner, $due]);
                        }
                    }
                }

                $db->commit();
                $_SESSION['review_success'] = $status === 'published' ? "Evaluasi berhasil di-publish!" : "Draf & Action Items berhasil disimpan.";
            } catch (Exception $e) {
                $db->rollBack();
                $_SESSION['review_error'] = "Gagal menyimpan perubahan: " . $e->getMessage();
            }
        }

        $this->redirect('reviews');
    }

    public function delete() {
        AuthMiddleware::handle();
        $user = Auth::user();
        if ($user['role_global'] !== 'leader') $this->redirect('reviews');

        $id = $_POST['id'] ?? null;
        if ($id) {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("UPDATE weekly_reviews SET deleted_at = CURRENT_TIMESTAMP WHERE id = ? AND brand_id = ?");
            $stmt->execute([$id, $_SESSION['active_brand_id']]);
            $_SESSION['review_success'] = "Evaluasi mingguan telah dihapus secara aman.";
        }
        $this->redirect('reviews');
    }
}