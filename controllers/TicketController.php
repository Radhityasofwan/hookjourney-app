<?php

class TicketController extends Controller {

    // =========================================================================
    // 1. RENDER HALAMAN UTAMA (LIST TIKET)
    // =========================================================================
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

        $db = Database::getInstance()->getConnection();
        
        // Mengambil tiket beserta info Assignee, Kategori, dan jumlah Komentar/Attachment
        $sql = "SELECT t.*, c.name as category_name, c.color_hex,
                       u.full_name as assignee_name, u.avatar_url as assignee_avatar, u.phone as assignee_phone,
                       (SELECT COUNT(*) FROM ticket_comments WHERE ticket_id = t.id) as comment_count,
                       (SELECT COUNT(*) FROM ticket_attachments WHERE ticket_id = t.id) as attachment_count,
                       (SELECT COUNT(*) FROM ticket_checklists WHERE ticket_id = t.id AND is_done = 1) as checklist_done,
                       (SELECT COUNT(*) FROM ticket_checklists WHERE ticket_id = t.id) as checklist_total
                FROM tickets t 
                LEFT JOIN ticket_categories c ON t.category_id = c.id 
                LEFT JOIN users u ON t.assigned_to = u.id 
                WHERE t.brand_id = :brand_id AND t.deleted_at IS NULL";
                
        $params = ['brand_id' => $brandId];
        
        // Logika Privasi: Team/Client HANYA melihat tugas yang di-assign ke dia sendiri
        if ($user['role_global'] !== 'leader') {
            $sql .= " AND t.assigned_to = :user_id";
            $params['user_id'] = $user['id'];
        }
        
        $sql .= " ORDER BY 
                    CASE t.ticket_status
                        WHEN 'open' THEN 1
                        WHEN 'in_progress' THEN 2
                        WHEN 'review' THEN 3
                        WHEN 'blocked' THEN 4
                        WHEN 'done' THEN 5
                        WHEN 'cancelled' THEN 6
                        ELSE 7
                    END,
                    t.due_at ASC, t.created_at DESC";
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $tickets = $stmt->fetchAll();

        $data = [
            'user' => $user,
            'brands' => $brands,
            'activeBrand' => $activeBrand,
            'pageTitle' => 'Task & Ticketing',
            'tickets' => $tickets,
            'success_msg' => $_SESSION['ticket_success'] ?? null
        ];
        
        unset($_SESSION['ticket_success']);
        $this->view('tickets/index', $data, 'app');
    }

    // =========================================================================
    // 2. FORM & PROSES CRUD TIKET
    // =========================================================================
    public function create() {
        AuthMiddleware::handle();
        if (!isset($_SESSION['active_brand_id'])) $this->redirect('dashboard');
        
        $user = Auth::user();
        if($user['role_global'] !== 'leader') $this->redirect('tickets');

        $brandId = $_SESSION['active_brand_id'];
        BrandAccessMiddleware::checkAccess($brandId);

        $brandModel = $this->model('Brand');
        $brands = $brandModel->getActiveBrandsByUser($user);
        $activeBrand = null;
        foreach ($brands as $b) { if ($b['id'] == $brandId) { $activeBrand = $b; break; } }

        $db = Database::getInstance()->getConnection();
        $sql = "SELECT DISTINCT u.id, u.full_name, u.role_global 
                FROM users u
                LEFT JOIN brand_members bm ON u.id = bm.user_id AND bm.brand_id = :brand_id_1
                WHERE ((u.workspace_id = :workspace_id AND u.role_global = 'leader') OR (bm.brand_id = :brand_id_2 AND bm.is_active = 1))
                AND u.is_active = 1 AND u.deleted_at IS NULL ORDER BY u.full_name ASC";
                
        $stmt = $db->prepare($sql);
        $stmt->execute(['brand_id_1' => $brandId, 'workspace_id' => $user['workspace_id'], 'brand_id_2' => $brandId]);
        $teamMembers = $stmt->fetchAll();
        
        $categoryModel = $this->model('TicketCategory');
        $categories = $categoryModel->getActiveByBrand($brandId);

        $data = [
            'user' => $user,
            'brands' => $brands,
            'activeBrand' => $activeBrand,
            'pageTitle' => 'Buat Tiket Baru',
            'teamMembers' => $teamMembers,
            'categories' => $categories,
            'errors' => $_SESSION['form_errors'] ?? [],
            'old' => $_SESSION['form_old'] ?? []
        ];

        unset($_SESSION['form_errors'], $_SESSION['form_old']);
        $this->view('tickets/form', $data, 'app');
    }

    public function edit() {
        AuthMiddleware::handle();
        if (!isset($_SESSION['active_brand_id'])) $this->redirect('dashboard');
        
        $user = Auth::user();
        if($user['role_global'] !== 'leader') $this->redirect('tickets');

        $id = $_GET['id'] ?? null;
        if (!$id) $this->redirect('tickets');

        $brandId = $_SESSION['active_brand_id'];
        BrandAccessMiddleware::checkAccess($brandId);

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM tickets WHERE id = ? AND brand_id = ? AND deleted_at IS NULL");
        $stmt->execute([$id, $brandId]);
        $ticket = $stmt->fetch();

        if(!$ticket) $this->redirect('tickets');

        $brandModel = $this->model('Brand');
        $brands = $brandModel->getActiveBrandsByUser($user);
        $activeBrand = null;
        foreach ($brands as $b) { if ($b['id'] == $brandId) { $activeBrand = $b; break; } }

        $sql = "SELECT DISTINCT u.id, u.full_name, u.role_global FROM users u LEFT JOIN brand_members bm ON u.id = bm.user_id AND bm.brand_id = :brand_id_1 WHERE ((u.workspace_id = :workspace_id AND u.role_global = 'leader') OR (bm.brand_id = :brand_id_2 AND bm.is_active = 1)) AND u.is_active = 1 AND u.deleted_at IS NULL ORDER BY u.full_name ASC";
        $stmtTeam = $db->prepare($sql);
        $stmtTeam->execute(['brand_id_1' => $brandId, 'workspace_id' => $user['workspace_id'], 'brand_id_2' => $brandId]);
        
        $categoryModel = $this->model('TicketCategory');

        if(!empty($ticket['due_at'])) {
            $ticket['due_at'] = date('Y-m-d\TH:i', strtotime($ticket['due_at']));
        }

        $data = [
            'user' => $user,
            'brands' => $brands,
            'activeBrand' => $activeBrand,
            'pageTitle' => 'Edit Tugas',
            'teamMembers' => $stmtTeam->fetchAll(),
            'categories' => $categoryModel->getActiveByBrand($brandId),
            'errors' => $_SESSION['form_errors'] ?? [],
            'old' => $_SESSION['form_old'] ?? $ticket,
            'ticketId' => $id
        ];

        unset($_SESSION['form_errors'], $_SESSION['form_old']);
        $this->view('tickets/form', $data, 'app');
    }

    public function store() {
        AuthMiddleware::handle();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('tickets');
        if (!isset($_SESSION['active_brand_id'])) $this->redirect('dashboard');
        
        $brandId = $_SESSION['active_brand_id'];
        $validator = new Validator();
        $rules = [ 'title' => 'required|max:200', 'priority' => 'required' ];

        if ($validator->validate($_POST, $rules)) {
            $ticketModel = $this->model('Ticket');
            $ticketNo = 'TICK-B' . $brandId . '-' . strtoupper(substr(uniqid(), -5));

            $insertData = [
                'brand_id' => $brandId,
                'ticket_no' => $ticketNo,
                'category_id' => !empty($_POST['category_id']) ? $_POST['category_id'] : null,
                'title' => trim($_POST['title']),
                'description' => trim($_POST['description']) ?: null,
                'priority' => $_POST['priority'],
                'ticket_status' => 'open', 
                'created_by' => Auth::user()['id'],
                'assigned_to' => !empty($_POST['assigned_to']) ? $_POST['assigned_to'] : null,
                'due_at' => !empty($_POST['due_at']) ? date('Y-m-d H:i:s', strtotime($_POST['due_at'])) : null,
                'source_type' => 'manual'
            ];

            $ticketModel->insert($insertData);
            $_SESSION['ticket_success'] = "Tiket baru berhasil dibuat.";
            $this->redirect('tickets');
        } else {
            $_SESSION['form_errors'] = $validator->getErrors();
            $_SESSION['form_old'] = $_POST;
            $this->redirect('tickets/create');
        }
    }

    public function update() {
        AuthMiddleware::handle();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') $this->redirect('tickets');
        
        $id = $_POST['id'] ?? null;
        $validator = new Validator();
        
        if ($id && $validator->validate($_POST, ['title' => 'required|max:200', 'priority' => 'required'])) {
            $db = Database::getInstance()->getConnection();
            $updateData = [
                'category_id' => !empty($_POST['category_id']) ? $_POST['category_id'] : null,
                'title' => trim($_POST['title']),
                'description' => trim($_POST['description']) ?: null,
                'priority' => $_POST['priority'],
                'assigned_to' => !empty($_POST['assigned_to']) ? $_POST['assigned_to'] : null,
                'due_at' => !empty($_POST['due_at']) ? date('Y-m-d H:i:s', strtotime($_POST['due_at'])) : null
            ];

            $sql = "UPDATE tickets SET category_id=?, title=?, description=?, priority=?, assigned_to=?, due_at=? WHERE id=? AND brand_id=?";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                $updateData['category_id'], $updateData['title'], $updateData['description'], 
                $updateData['priority'], $updateData['assigned_to'], $updateData['due_at'],
                $id, $_SESSION['active_brand_id']
            ]);

            $_SESSION['ticket_success'] = "Tugas berhasil diperbarui.";
            $this->redirect('tickets');
        } else {
            $_SESSION['form_errors'] = $validator->getErrors();
            $_SESSION['form_old'] = $_POST;
            $this->redirect('tickets/edit?id='.$id);
        }
    }

    public function delete() {
        AuthMiddleware::handle();
        $id = $_POST['id'] ?? null;
        $user = Auth::user();

        if ($id && $user['role_global'] === 'leader') {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("UPDATE tickets SET deleted_at = CURRENT_TIMESTAMP WHERE id = ? AND brand_id = ?");
            $stmt->execute([$id, $_SESSION['active_brand_id']]);
            $_SESSION['ticket_success'] = "Tugas telah dihapus.";
        }
        $this->redirect('tickets');
    }

    // =========================================================================
    // 3. API ENDPOINTS (UNTUK MODAL DAN INTERAKSI AJAX)
    // =========================================================================

    /**
     * Memperbarui Status Tiket (Merekam started_at dan completed_at secara otomatis)
     */
    public function updateStatus() {
        AuthMiddleware::handle();
        $id = $_POST['id'] ?? null;
        $status = $_POST['status'] ?? null;
        
        if ($id && $status) {
            $db = Database::getInstance()->getConnection();
            
            $sql = "UPDATE tickets SET ticket_status = ?";
            $params = [$status];

            // Pencatatan waktu pintar berdasarkan status
            if ($status === 'in_progress') {
                $sql .= ", started_at = COALESCE(started_at, CURRENT_TIMESTAMP)";
            } elseif ($status === 'done') {
                $sql .= ", completed_at = CURRENT_TIMESTAMP";
            }

            $sql .= " WHERE id = ? AND brand_id = ?";
            $params[] = $id;
            $params[] = $_SESSION['active_brand_id'];

            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
        exit;
    }

    /**
     * Fetch Detail Tiket beserta Relasinya (Comments, Checklists, Attachments) untuk Modal
     */
    public function apiDetail() {
        AuthMiddleware::handle();
        $id = $_GET['id'] ?? null;
        $brandId = $_SESSION['active_brand_id'];

        if (!$id) { echo json_encode(['error' => 'ID tidak valid']); exit; }

        $db = Database::getInstance()->getConnection();

        // 1. Ambil Data Tiket Utama
        $stmt = $db->prepare("SELECT t.*, c.name as category_name, c.color_hex,
                                     u.full_name as assignee_name, u.avatar_url as assignee_avatar, u.phone as assignee_phone,
                                     uc.full_name as creator_name
                              FROM tickets t
                              LEFT JOIN ticket_categories c ON t.category_id = c.id
                              LEFT JOIN users u ON t.assigned_to = u.id
                              LEFT JOIN users uc ON t.created_by = uc.id
                              WHERE t.id = ? AND t.brand_id = ? AND t.deleted_at IS NULL");
        $stmt->execute([$id, $brandId]);
        $ticket = $stmt->fetch();

        if (!$ticket) { echo json_encode(['error' => 'Tiket tidak ditemukan']); exit; }

        // 2. Ambil Checklists (Sub-task)
        $stmtCheck = $db->prepare("SELECT * FROM ticket_checklists WHERE ticket_id = ? ORDER BY sort_order ASC, id ASC");
        $stmtCheck->execute([$id]);
        $checklists = $stmtCheck->fetchAll();

        // 3. Ambil Komentar/Log Diskusi
        $stmtComm = $db->prepare("SELECT c.*, u.full_name as author_name, u.avatar_url as author_avatar
                                  FROM ticket_comments c
                                  LEFT JOIN users u ON c.user_id = u.id
                                  WHERE c.ticket_id = ?
                                  ORDER BY c.created_at ASC");
        $stmtComm->execute([$id]);
        $comments = $stmtComm->fetchAll();

        // 4. Ambil Lampiran File
        $stmtFile = $db->prepare("SELECT a.*, u.full_name as uploader_name
                                  FROM ticket_attachments a
                                  LEFT JOIN users u ON a.uploaded_by = u.id
                                  WHERE a.ticket_id = ?
                                  ORDER BY a.created_at DESC");
        $stmtFile->execute([$id]);
        $attachments = $stmtFile->fetchAll();

        // Format tanggal agar ramah UI
        if($ticket['due_at']) $ticket['due_at_formatted'] = date('d M Y, H:i', strtotime($ticket['due_at']));
        if($ticket['created_at']) $ticket['created_at_formatted'] = date('d M Y', strtotime($ticket['created_at']));

        echo json_encode([
            'success' => true,
            'ticket' => $ticket,
            'checklists' => $checklists,
            'comments' => $comments,
            'attachments' => $attachments
        ]);
        exit;
    }

    /**
     * Tambah Komentar Baru ke Tiket
     */
    public function storeComment() {
        AuthMiddleware::handle();
        $db = Database::getInstance()->getConnection();
        $ticketId = $_POST['ticket_id'] ?? null;
        $text = trim($_POST['comment_text'] ?? '');
        
        if ($ticketId && $text) {
            $stmt = $db->prepare("INSERT INTO ticket_comments (ticket_id, user_id, comment_text) VALUES (?, ?, ?)");
            $stmt->execute([$ticketId, Auth::user()['id'], $text]);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Teks komentar kosong']);
        }
        exit;
    }

    /**
     * Update Checklist (Centang/Uncentang Sub-task)
     */
    public function toggleChecklist() {
        AuthMiddleware::handle();
        $db = Database::getInstance()->getConnection();
        $id = $_POST['id'] ?? null;
        $isDone = $_POST['is_done'] ?? 0;
        
        if ($id) {
            $doneBy = $isDone ? Auth::user()['id'] : null;
            $doneAt = $isDone ? date('Y-m-d H:i:s') : null;
            $stmt = $db->prepare("UPDATE ticket_checklists SET is_done = ?, done_by = ?, done_at = ? WHERE id = ?");
            $stmt->execute([$isDone, $doneBy, $doneAt, $id]);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
        exit;
    }
}