<?php

class ContentPillar extends Model {
    protected $table = 'content_pillars';

    /**
     * Ambil pilar konten yang aktif untuk dropdown
     */
    public function getActiveByBrand($brandId) {
        $sql = "SELECT * FROM {$this->table} WHERE brand_id = :brand_id AND is_active = 1 ORDER BY name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['brand_id' => $brandId]);
        return $stmt->fetchAll();
    }
}