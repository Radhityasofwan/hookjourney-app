<?php

class User extends Model {
    protected $table = 'users';

    /**
     * Mencari user berdasarkan email yang aktif dan belum dihapus
     */
    public function findByEmail($email) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE email = :email 
                AND is_active = 1 
                AND deleted_at IS NULL 
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }

    /**
     * Update kolom last_login_at saat user berhasil login
     */
    public function updateLastLogin($id) {
        $sql = "UPDATE {$this->table} SET last_login_at = CURRENT_TIMESTAMP WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}