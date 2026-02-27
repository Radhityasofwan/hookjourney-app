<?php

class ShareLink extends Model {
    protected $table = 'share_links';

    public function getAllByBrand($brandId) {
        $sql = "SELECT s.*, u.full_name as creator_name 
                FROM {$this->table} s
                LEFT JOIN users u ON s.created_by = u.id
                WHERE s.brand_id = :brand_id 
                ORDER BY s.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['brand_id' => $brandId]);
        return $stmt->fetchAll();
    }

    public function findByToken($token) {
        $sql = "SELECT s.*, b.name as brand_name, b.logo_url 
                FROM {$this->table} s
                INNER JOIN brands b ON s.brand_id = b.id
                WHERE s.token = :token AND (s.expires_at IS NULL OR s.expires_at > NOW())
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['token' => $token]);
        return $stmt->fetch();
    }
}