<?php

class Keyword extends Model {
    protected $table = 'keywords';

    public function getAllByBrand($brandId) {
        $sql = "SELECT k.*, c.cluster_name
                FROM {$this->table} k
                LEFT JOIN keyword_clusters c ON k.cluster_id = c.id
                WHERE k.brand_id = :brand_id AND k.deleted_at IS NULL
                ORDER BY 
                    CASE k.priority WHEN 'high' THEN 1 WHEN 'medium' THEN 2 ELSE 3 END, 
                    k.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['brand_id' => $brandId]);
        return $stmt->fetchAll();
    }
}