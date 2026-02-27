<?php

class ContentItem extends Model {
    protected $table = 'content_items';

    /**
     * Mengambil seluruh konten milik suatu brand, di-join dengan tabel users 
     * untuk menarik data Nama, Avatar, dan Nomor WhatsApp PIC.
     */
    public function findAllByBrand($brandId) {
        $sql = "SELECT c.*, 
                       u.full_name as pic_name, 
                       u.avatar_url as pic_avatar_url, 
                       u.phone as pic_phone
                FROM {$this->table} c
                LEFT JOIN users u ON c.pic_user_id = u.id
                WHERE c.brand_id = :brand_id 
                AND c.deleted_at IS NULL
                ORDER BY c.priority DESC, c.created_at DESC";
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['brand_id' => $brandId]);
        return $stmt->fetchAll();
    }
}