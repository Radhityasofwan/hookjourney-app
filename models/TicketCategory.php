<?php

class TicketCategory extends Model {
    protected $table = 'ticket_categories';

    /**
     * Ambil kategori tiket aktif berdasarkan brand_id
     */
    public function getActiveByBrand($brandId) {
        $sql = "SELECT * FROM {$this->table} WHERE brand_id = :brand_id AND is_active = 1 ORDER BY name ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['brand_id' => $brandId]);
        return $stmt->fetchAll();
    }
}