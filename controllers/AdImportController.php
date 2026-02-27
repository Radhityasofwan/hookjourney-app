<?php

class AdImportController extends Controller
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

        $db = Database::getInstance()->getConnection();

        $sqlAcc = "SELECT id, account_name, account_id_external FROM ad_accounts WHERE brand_id = ? AND is_active = 1";
        $stmtAcc = $db->prepare($sqlAcc);
        $stmtAcc->execute([$brandId]);
        $adAccounts = $stmtAcc->fetchAll();

        $sqlHist = "SELECT i.id, i.file_name, i.import_status, i.created_at, a.account_name 
                    FROM ad_report_imports i 
                    LEFT JOIN ad_accounts a ON i.ad_account_id = a.id 
                    WHERE i.brand_id = ? 
                    ORDER BY i.created_at DESC LIMIT 10";
        $stmtHist = $db->prepare($sqlHist);
        $stmtHist->execute([$brandId]);
        $importHistory = $stmtHist->fetchAll();

        $hasPending = false;
        foreach ($importHistory as $row) {
            if ($row['import_status'] === 'uploaded') {
                $hasPending = true;
                break;
            }
        }

        $data = [
            'user' => $user,
            'brands' => $brands,
            'activeBrand' => $activeBrand,
            'pageTitle' => 'Import Meta Ads Report',
            'adAccounts' => $adAccounts,
            'importHistory' => $importHistory,
            'hasPending' => $hasPending,
            'success_msg' => $_SESSION['import_success'] ?? null,
            'error_msg' => $_SESSION['import_error'] ?? null
        ];

        unset($_SESSION['import_success'], $_SESSION['import_error']);

        $this->view('ads/import', $data, 'app');
    }

    public function upload()
    {
        AuthMiddleware::handle();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST')
            $this->redirect('ads/import');
        if (!isset($_SESSION['active_brand_id']))
            $this->redirect('dashboard');

        $brandId = $_SESSION['active_brand_id'];
        BrandAccessMiddleware::checkAccess($brandId);

        $adAccountId = $_POST['ad_account_id'] ?? '';

        if (empty($adAccountId)) {
            $_SESSION['import_error'] = "Silakan pilih Ad Account terlebih dahulu.";
            $this->redirect('ads/import');
        }

        if (isset($_FILES['report_file']) && $_FILES['report_file']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['report_file']['tmp_name'];
            $fileName = $_FILES['report_file']['name'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));

            $allowedfileExtensions = ['csv', 'xlsx'];

            if (in_array($fileExtension, $allowedfileExtensions)) {

                $uploadDir = dirname(APP_PATH) . '/public/uploads/ads_reports/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $newFileName = time() . '_' . substr(md5($fileName), 0, 10) . '.' . $fileExtension;
                $destPath = $uploadDir . $newFileName;

                if (move_uploaded_file($fileTmpPath, $destPath)) {

                    $relativePath = 'uploads/ads_reports/' . $newFileName;

                    // FIX: Tambahkan _time() agar hash file selalu unik. 
                    // Ini memungkinkan user upload ulang file yang SAMA PERSIS (Pembaruan Data/Overwrite)
                    $fileHash = md5_file($destPath) . '_' . time();

                    $db = Database::getInstance()->getConnection();
                    $sqlInsert = "INSERT INTO ad_report_imports 
                                  (brand_id, ad_account_id, file_name, file_path, file_hash, import_status, imported_by) 
                                  VALUES (?, ?, ?, ?, ?, 'uploaded', ?)";
                    $stmt = $db->prepare($sqlInsert);

                    try {
                        $stmt->execute([
                            $brandId,
                            $adAccountId,
                            $fileName,
                            $relativePath,
                            $fileHash,
                            Auth::user()['id']
                        ]);
                        $_SESSION['import_success'] = "File laporan berhasil diunggah. Sistem sedang menyinkronkan pembaruan data Anda...";
                    } catch (PDOException $e) {
                        $_SESSION['import_error'] = "Gagal menyimpan ke database: " . $e->getMessage();
                        unlink($destPath);
                    }

                } else {
                    $_SESSION['import_error'] = "Terjadi kesalahan sistem saat memindahkan file unggahan.";
                }
            } else {
                $_SESSION['import_error'] = "Ekstensi file tidak diizinkan. Harap unggah file berformat .CSV.";
            }
        } else {
            $_SESSION['import_error'] = "Terdapat masalah saat membaca file yang diunggah. Pastikan ukuran di bawah 10MB.";
        }

        $this->redirect('ads/import');
    }

    public function processQueue()
    {
        header('Content-Type: application/json');

        try {
            require_once APP_PATH . '/services/AdsImportService.php';
            $service = new AdsImportService();
            $service->processPendingImports();

            echo json_encode(['status' => 'success', 'message' => 'Antrean diproses']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
        exit;
    }

    public function delete()
    {
        AuthMiddleware::handle();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST')
            $this->redirect('ads/import');
        if (!isset($_SESSION['active_brand_id']))
            $this->redirect('dashboard');

        $brandId = $_SESSION['active_brand_id'];
        $importId = $_POST['import_id'] ?? null;

        if ($importId) {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT file_path FROM ad_report_imports WHERE id = ? AND brand_id = ?");
            $stmt->execute([$importId, $brandId]);
            $import = $stmt->fetch();

            if ($import) {
                $db->beginTransaction();
                try {
                    $stmtPerf = $db->prepare("DELETE FROM ad_performance_daily WHERE import_id = ?");
                    $stmtPerf->execute([$importId]);

                    $stmtRaw = $db->prepare("DELETE FROM ad_report_rows_raw WHERE import_id = ?");
                    $stmtRaw->execute([$importId]);

                    $stmtImp = $db->prepare("DELETE FROM ad_report_imports WHERE id = ?");
                    $stmtImp->execute([$importId]);

                    $filePath = dirname(APP_PATH) . '/public/' . ltrim($import['file_path'], '/');
                    if (file_exists($filePath)) {
                        unlink($filePath);
                    }

                    $db->commit();
                    $_SESSION['import_success'] = "Riwayat import dan seluruh data performa dari file tersebut berhasil dihapus.";
                } catch (Exception $e) {
                    $db->rollBack();
                    $_SESSION['import_error'] = "Gagal menghapus data: " . $e->getMessage();
                }
            }
        }
        $this->redirect('ads/import');
    }
}