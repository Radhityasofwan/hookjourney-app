<?php

class Brand extends Model {
    protected $table = 'brands';

    /**
     * Mengambil daftar brand yang berhak diakses oleh user.
     * Sesuai konsep: Leader melihat semua brand di workspace-nya.
     * Team/Client hanya melihat brand yang di-assign via tabel brand_members.
     */
    public function getActiveBrandsByUser($user) {
        if ($user['role_global'] === ROLE_LEADER) {
            // Leader: Ambil semua brand aktif di workspace
            $sql = "SELECT * FROM {$this->table} 
                    WHERE workspace_id = :workspace_id 
                    AND is_active = 1 
                    AND deleted_at IS NULL 
                    ORDER BY name ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['workspace_id' => $user['workspace_id']]);
            return $stmt->fetchAll();
        } else {
            // Team / Client: Ambil dari relasi brand_members
            $sql = "SELECT b.*, bm.role_in_brand 
                    FROM {$this->table} b
                    INNER JOIN brand_members bm ON b.id = bm.brand_id
                    WHERE bm.user_id = :user_id 
                    AND b.is_active = 1 
                    AND bm.is_active = 1
                    AND b.deleted_at IS NULL 
                    ORDER BY b.name ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['user_id' => $user['id']]);
            return $stmt->fetchAll();
        }
    }
}