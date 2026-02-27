<?php

class DashboardController extends Controller {

    public function index() {
        // 1. GERBANG KEAMANAN: Pastikan user sudah login
        AuthMiddleware::handle();

        $user = Auth::user();
        
        // 2. Ambil daftar brand yang bisa diakses user ini
        $brandModel = $this->model('Brand');
        $brands = $brandModel->getActiveBrandsByUser($user);

        // 3. Set Brand Aktif Default
        $activeBrand = null;
        if (isset($_SESSION['active_brand_id'])) {
            foreach ($brands as $b) { 
                if ($b['id'] == $_SESSION['active_brand_id']) { 
                    $activeBrand = $b; 
                    break; 
                } 
            }
        } 
        
        if (!$activeBrand && !empty($brands)) {
            $activeBrand = $brands[0];
            $_SESSION['active_brand_id'] = $activeBrand['id'];
        }

        // 4. Tangkap Parameter Filter Tanggal (Default: 7 Hari Terakhir)
        $startDate = $_GET['start_date'] ?? date('Y-m-d', strtotime('-6 days'));
        $endDate = $_GET['end_date'] ?? date('Y-m-d');
        
        // Format datetime untuk query yang menggunakan timestamp (created_at)
        $startDateTime = $startDate . ' 00:00:00';
        $endDateTime = $endDate . ' 23:59:59';

        // 5. Siapkan Array Metrik Default
        $metrics = [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'ads_spend' => 0,
            'ads_results' => 0,
            'ads_impressions' => 0,
            'cpr' => 0,
            'ctr' => 0,
            'active_contents' => 0,
            'open_tickets' => 0,
            'chart_data' => []
        ];
        
        $recentActivities = [];

        // 6. Tarik Data Terpusat dari Seluruh Modul Berdasarkan Range Tanggal
        if ($activeBrand) {
            $db = Database::getInstance()->getConnection();
            $brandId = $activeBrand['id'];

            // --- A. Metrics Meta Ads (SINKRONISASI DENGAN AdPerformanceController) ---
            $sqlAds = "SELECT 
                        COALESCE(SUM(spend), 0) as total_spend, 
                        COALESCE(SUM(impressions), 0) as total_impressions, 
                        COALESCE(SUM(clicks), 0) as total_clicks, 
                        COALESCE(SUM(results), 0) as total_results 
                       FROM ad_performance_daily 
                       WHERE brand_id = ? AND report_date >= ? AND report_date <= ?";
            $stmtAds = $db->prepare($sqlAds);
            $stmtAds->execute([$brandId, $startDate, $endDate]);
            $adsData = $stmtAds->fetch();
            
            $metrics['ads_spend'] = $adsData['total_spend'];
            $metrics['ads_results'] = $adsData['total_results'];
            $metrics['ads_impressions'] = $adsData['total_impressions'];
            
            // Kalkulasi CPR & CTR persis seperti di AdPerformanceController
            $metrics['cpr'] = $metrics['ads_results'] > 0 ? ($metrics['ads_spend'] / $metrics['ads_results']) : 0;
            $metrics['ctr'] = $metrics['ads_impressions'] > 0 ? ($adsData['total_clicks'] / $metrics['ads_impressions']) * 100 : 0;

            // --- B. Metrics Konten (Aktif saat ini) ---
            $stmtContent = $db->prepare("SELECT COUNT(*) FROM content_items WHERE brand_id = ? AND content_status NOT IN ('published', 'archived', 'idea')");
            $stmtContent->execute([$brandId]);
            $metrics['active_contents'] = $stmtContent->fetchColumn();

            // --- C. Metrics Open Tickets (Aktif saat ini) ---
            $stmtTicket = $db->prepare("SELECT COUNT(*) FROM tickets WHERE brand_id = ? AND ticket_status NOT IN ('done', 'cancelled')");
            $stmtTicket->execute([$brandId]);
            $metrics['open_tickets'] = $stmtTicket->fetchColumn();

            // --- D. Chart Data (Dinamis Sesuai Filter Tanggal) ---
            $sqlChart = "SELECT report_date, COALESCE(SUM(spend), 0) as spend, COALESCE(SUM(results), 0) as results 
                         FROM ad_performance_daily 
                         WHERE brand_id = ? AND report_date >= ? AND report_date <= ?
                         GROUP BY report_date 
                         ORDER BY report_date ASC";
            $stmtChart = $db->prepare($sqlChart);
            $stmtChart->execute([$brandId, $startDate, $endDate]);
            $chartRows = $stmtChart->fetchAll();
            
            // Map data agar tanggal yang kosong (0 spend) tetap tampil di chart
            $chartMap = [];
            $maxSpend = 0;
            foreach ($chartRows as $r) {
                $chartMap[$r['report_date']] = [
                    'spend' => $r['spend'],
                    'results' => $r['results']
                ];
                if ($r['spend'] > $maxSpend) $maxSpend = $r['spend'];
            }

            $chartData = [];
            $begin = new DateTime($startDate);
            $end = new DateTime($endDate);
            $end->modify('+1 day'); // Supaya hari terakhir ikut ter-loop
            
            $interval = DateInterval::createFromDateString('1 day');
            $period = new DatePeriod($begin, $interval, $end);
            
            $hariIndo = ['Min','Sen','Sel','Rab','Kam','Jum','Sab'];

            foreach ($period as $dt) {
                $d = $dt->format('Y-m-d');
                $spend = $chartMap[$d]['spend'] ?? 0;
                $results = $chartMap[$d]['results'] ?? 0;
                $percent = $maxSpend > 0 ? round(($spend / $maxSpend) * 100) : 0;
                
                $chartData[] = [
                    'day' => $hariIndo[$dt->format('w')],
                    'date' => $d,
                    'spend' => $spend,
                    'results' => $results,
                    'percent' => $percent < 5 ? 5 : $percent // Min-height 5% untuk visualisasi
                ];
            }
            $metrics['chart_data'] = $chartData;

            // --- E. Aktivitas Terbaru (Difilter Berdasarkan Tanggal) ---
            $sqlAct = "
                SELECT title, created_at, 'ticket' as type FROM tickets WHERE brand_id = ? AND created_at BETWEEN ? AND ?
                UNION ALL 
                SELECT title, created_at, 'content' as type FROM content_items WHERE brand_id = ? AND created_at BETWEEN ? AND ?
                UNION ALL
                SELECT title, created_at, 'creative' as type FROM creative_assets WHERE brand_id = ? AND created_at BETWEEN ? AND ?
                UNION ALL
                SELECT title, created_at, 'forum' as type FROM forum_threads WHERE brand_id = ? AND created_at BETWEEN ? AND ?
                UNION ALL
                SELECT keyword_text as title, created_at, 'keyword' as type FROM keywords WHERE brand_id = ? AND created_at BETWEEN ? AND ?
                ORDER BY created_at DESC LIMIT 8
            ";
            $stmtAct = $db->prepare($sqlAct);
            $stmtAct->execute([
                $brandId, $startDateTime, $endDateTime,
                $brandId, $startDateTime, $endDateTime,
                $brandId, $startDateTime, $endDateTime,
                $brandId, $startDateTime, $endDateTime,
                $brandId, $startDateTime, $endDateTime
            ]);
            $recentActivities = $stmtAct->fetchAll();

        } else {
            // Mockup kosong jika belum punya brand
            $days = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];
            foreach ($days as $d) {
                $metrics['chart_data'][] = ['day' => $d, 'percent' => 5, 'spend' => 0, 'results' => 0, 'date' => ''];
            }
        }

        // 7. Render View
        $data = [
            'user' => $user,
            'brands' => $brands,
            'activeBrand' => $activeBrand,
            'pageTitle' => 'Dashboard Inti',
            'metrics' => $metrics,
            'recentActivities' => $recentActivities
        ];

        $this->view('dashboard/index', $data, 'app');
    }
}