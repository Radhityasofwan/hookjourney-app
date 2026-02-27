<?php

class BrandMember extends Model {
    protected $table = 'brand_members';

    /**
     * Cek apakah relasi antara user dan brand sudah ada
     */
    public function checkRelation($brandId, $userId) {
        $sql = "SELECT * FROM {$this->table} WHERE brand_id = :brand_id AND user_id = :user_id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['brand_id' => $brandId, 'user_id' => $userId]);
        return $stmt->fetch();
    }
}