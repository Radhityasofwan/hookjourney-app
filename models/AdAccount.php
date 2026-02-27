<?php

class AdAccount extends Model {
    protected $table = 'ad_accounts';

    /**
     * Ambil akun iklan aktif berdasarkan brand_id
     */
    public function getActiveByBrand($brandId) {
        $sql = "SELECT * FROM {$this->table} WHERE brand_id = :brand_id AND is_active = 1 ORDER BY account_name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['brand_id' => $brandId]);
        return $stmt->fetchAll();
    }
}