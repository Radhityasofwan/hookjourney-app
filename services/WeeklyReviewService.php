<?php

class WeeklyReviewService {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Menarik data operasional (Ads, Konten, Task, SEO) untuk digenerate ke draf evaluasi
     */
    public function generateDraftData($brandId, $startDate, $endDate) {
        $metrics = [];

        // 1. Tarik Data Meta Ads (Agregat)
        $sqlAds = "SELECT SUM(spend) as total_spend, SUM(results) as total_results, 
                          SUM(clicks) as total_clicks, SUM(impressions) as total_impressions
                   FROM ad_performance_daily 
                   WHERE brand_id = :brand_id AND report_date BETWEEN :start AND :end";
        $stmtAds = $this->db->prepare($sqlAds);
        $stmtAds->execute(['brand_id' => $brandId, 'start' => $startDate, 'end' => $endDate]);
        $adsData = $stmtAds->fetch();

        $spend = (float)($adsData['total_spend'] ?? 0);
        $results = (float)($adsData['total_results'] ?? 0);
        $impressions = (int)($adsData['total_impressions'] ?? 0);
        $clicks = (int)($adsData['total_clicks'] ?? 0);

        $cpr = $results > 0 ? ($spend / $results) : 0;
        $ctr = $impressions > 0 ? ($clicks / $impressions) * 100 : 0;

        $metrics[] = ['section' => 'ads', 'key' => 'ads_spend', 'label' => 'Total Spend', 'value' => $spend];
        $metrics[] = ['section' => 'ads', 'key' => 'ads_results', 'label' => 'Total Results', 'value' => $results];
        $metrics[] = ['section' => 'ads', 'key' => 'ads_cpr', 'label' => 'Cost per Result', 'value' => $cpr];
        $metrics[] = ['section' => 'ads', 'key' => 'ads_ctr', 'label' => 'Avg CTR (%)', 'value' => $ctr];

        // 2. Tarik Data Konten (Yang terbit minggu ini)
        $sqlContent = "SELECT COUNT(*) as published_count 
                       FROM content_items 
                       WHERE brand_id = :brand_id AND content_status = 'published' 
                       AND published_at BETWEEN :start AND :end AND deleted_at IS NULL";
        $stmtContent = $this->db->prepare($sqlContent);
        $stmtContent->execute(['brand_id' => $brandId, 'start' => $startDate . ' 00:00:00', 'end' => $endDate . ' 23:59:59']);
        $contentData = $stmtContent->fetch();

        $metrics[] = ['section' => 'content', 'key' => 'content_published', 'label' => 'Konten Terbit', 'value' => $contentData['published_count']];

        // 3. Tarik Data Tim/Task (Tiket Selesai)
        $sqlTask = "SELECT COUNT(*) as done_count 
                    FROM tickets 
                    WHERE brand_id = :brand_id AND ticket_status = 'done' 
                    AND completed_at BETWEEN :start AND :end AND deleted_at IS NULL";
        $stmtTask = $this->db->prepare($sqlTask);
        $stmtTask->execute(['brand_id' => $brandId, 'start' => $startDate . ' 00:00:00', 'end' => $endDate . ' 23:59:59']);
        $taskData = $stmtTask->fetch();

        $metrics[] = ['section' => 'team', 'key' => 'ticket_done', 'label' => 'Task Selesai', 'value' => $taskData['done_count']];

        // 4. Tarik Data SEO (Keyword Tembus Page 1)
        $sqlSeo = "SELECT COUNT(*) as won_count 
                   FROM keywords 
                   WHERE brand_id = :brand_id AND keyword_status = 'won' AND deleted_at IS NULL";
        $stmtSeo = $this->db->prepare($sqlSeo);
        $stmtSeo->execute(['brand_id' => $brandId]);
        $seoData = $stmtSeo->fetch();

        $metrics[] = ['section' => 'seo', 'key' => 'seo_won', 'label' => 'Keyword Masuk Page 1', 'value' => $seoData['won_count']];

        return $metrics;
    }
}