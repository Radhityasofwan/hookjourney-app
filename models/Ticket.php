<?php

class Ticket extends Model {
    protected $table = 'tickets';

    /**
     * Mengambil daftar tiket dengan join ke user assignee dan kategori
     */
    public function getAllByBrand($brandId) {
        $sql = "SELECT t.*, 
                       u_assign.full_name as assignee_name, 
                       u_creator.full_name as creator_name,
                       c.name as category_name, c.color_hex
                FROM {$this->table} t
                LEFT JOIN users u_assign ON t.assigned_to = u_assign.id
                LEFT JOIN users u_creator ON t.created_by = u_creator.id
                LEFT JOIN ticket_categories c ON t.category_id = c.id
                WHERE t.brand_id = :brand_id AND t.deleted_at IS NULL
                ORDER BY 
                    CASE t.ticket_status
                        WHEN 'open' THEN 1
                        WHEN 'in_progress' THEN 2
                        WHEN 'review' THEN 3
                        WHEN 'blocked' THEN 4
                        WHEN 'done' THEN 5
                        WHEN 'cancelled' THEN 6
                        ELSE 7
                    END,
                    t.due_at ASC, t.created_at DESC";
                    
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['brand_id' => $brandId]);
        return $stmt->fetchAll();
    }
}