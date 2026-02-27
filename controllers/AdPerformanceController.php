<?php

class AdPerformanceController extends Controller {

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
        
        // =========================================================================
        // FIX ERROR: AUTO-PATCH & DYNAMIC COLUMN CHECK
        // =========================================================================
        $hasResultType = false;
        try {
            $checkCol = $db->query("SHOW COLUMNS FROM ad_performance_daily LIKE 'result_type'");
            if ($checkCol->fetch() === false) {
                $db->exec("ALTER TABLE ad_performance_daily ADD COLUMN result_type VARCHAR(150) NULL AFTER results");
            }
            $hasResultType = true;
        } catch (Exception $e) {
            $hasResultType = false;
        }
        
        $resultTypeQuery = $hasResultType 
            ? "GROUP_CONCAT(DISTINCT result_type SEPARATOR ', ') as result_types," 
            : "'' as result_types,";

        // =========================================================================
        // LOGIKA FILTER DINAMIS (TANGGAL & TIPE HASIL)
        // =========================================================================
        // Default: 30 Hari Terakhir jika tidak ada filter yang dipilih
        $startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-30 days'));
        $endDate = $_GET['end_date'] ?? date('Y-m-d');
        $filterResultType = $_GET['result_type'] ?? '';

        // Ambil daftar unik "Tipe Hasil" dari database untuk dropdown filter
        $availableResultTypes = [];
        if ($hasResultType) {
            $sqlTypes = "SELECT DISTINCT result_type FROM ad_performance_daily 
                         WHERE brand_id = :brand_id AND result_type IS NOT NULL AND result_type != '' 
                         ORDER BY result_type ASC";
            $stmtTypes = $db->prepare($sqlTypes);
            $stmtTypes->execute(['brand_id' => $brandId]);
            $availableResultTypes = $stmtTypes->fetchAll(PDO::FETCH_COLUMN);
        }

        // Bangun base WHERE clause dan parameter binding
        $whereClause = "brand_id = :brand_id AND report_date BETWEEN :start_date AND :end_date";
        $params = [
            'brand_id' => $brandId,
            'start_date' => $startDate,
            'end_date' => $endDate
        ];

        // Jika user memfilter berdasarkan Tipe Hasil spesifik
        if ($hasResultType && !empty($filterResultType)) {
            $whereClause .= " AND result_type = :result_type";
            $params['result_type'] = $filterResultType;
        }

        // =========================================================================
        // EKSEKUSI QUERY DENGAN FILTER
        // =========================================================================
        $sqlSum = "SELECT 
                    COALESCE(SUM(spend), 0) as total_spend, 
                    COALESCE(SUM(impressions), 0) as total_impressions, 
                    COALESCE(SUM(clicks), 0) as total_clicks, 
                    COALESCE(SUM(results), 0) as total_results
                   FROM ad_performance_daily 
                   WHERE {$whereClause}";
        $stmt = $db->prepare($sqlSum);
        $stmt->execute($params);
        $summary = $stmt->fetch();

        $aggCtr = $summary['total_impressions'] > 0 ? ($summary['total_clicks'] / $summary['total_impressions']) * 100 : 0;
        $aggCpr = $summary['total_results'] > 0 ? ($summary['total_spend'] / $summary['total_results']) : 0;

        $sqlCamp = "SELECT 
                        campaign_name, 
                        adset_name,
                        ad_name,
                        SUM(spend) as spend, 
                        SUM(results) as results, 
                        {$resultTypeQuery}
                        SUM(impressions) as impressions,
                        SUM(reach) as reach,
                        IF(SUM(results) > 0, (SUM(spend)/SUM(results)), 0) as cpr, 
                        IF(SUM(impressions) > 0, (SUM(clicks)/SUM(impressions)*100), 0) as ctr,
                        IF(SUM(impressions) > 0, (SUM(spend)/SUM(impressions)*1000), 0) as cpm,
                        SUM(video_25) as video_25,
                        SUM(video_50) as video_50,
                        SUM(video_75) as video_75,
                        SUM(video_100) as video_100
                    FROM ad_performance_daily 
                    WHERE {$whereClause}
                    GROUP BY campaign_name, adset_name, ad_name
                    ORDER BY spend DESC LIMIT 150";
                    
        $stmt2 = $db->prepare($sqlCamp);
        $stmt2->execute($params);
        $campaigns = $stmt2->fetchAll();

        // Kembalikan state filter ke View agar input tidak reset saat direload
        $filters = [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'result_type' => $filterResultType
        ];

        $data = [
            'user' => $user,
            'brands' => $brands,
            'activeBrand' => $activeBrand,
            'pageTitle' => 'Performa Creative Meta Ads',
            'summary' => $summary,
            'aggCtr' => $aggCtr,
            'aggCpr' => $aggCpr,
            'campaigns' => $campaigns,
            'filters' => $filters,
            'availableResultTypes' => $availableResultTypes
        ];

        $this->view('ads/performance', $data, 'app');
    }
}