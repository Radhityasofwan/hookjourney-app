<?php

class AdReportImport extends Model {
    protected $table = 'ad_report_imports';

    /**
     * Ambil riwayat import berdasarkan brand_id
     */
    public function getHistoryByBrand($brandId, $limit = 10) {
        $sql = "SELECT i.*, a.account_name, u.full_name as uploader_name 
                FROM {$this->table} i
                LEFT JOIN ad_accounts a ON i.ad_account_id = a.id
                LEFT JOIN users u ON i.imported_by = u.id
                WHERE i.brand_id = :brand_id 
                ORDER BY i.created_at DESC 
                LIMIT " . (int)$limit;
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['brand_id' => $brandId]);
        return $stmt->fetchAll();
    }
}