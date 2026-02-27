<?php

class CreativeAsset extends Model {
    protected $table = 'creative_assets';

    public function getAllByBrand($brandId) {
        $sql = "SELECT a.*, u.full_name as uploader_name 
                FROM {$this->table} a
                LEFT JOIN users u ON a.uploaded_by = u.id
                WHERE a.brand_id = :brand_id AND a.deleted_at IS NULL
                ORDER BY a.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['brand_id' => $brandId]);
        return $stmt->fetchAll();
    }
}