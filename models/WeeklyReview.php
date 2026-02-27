<?php

class WeeklyReview extends Model {
    protected $table = 'weekly_reviews';

    public function getAllByBrand($brandId) {
        $sql = "SELECT r.*, u.full_name as generator_name 
                FROM {$this->table} r
                LEFT JOIN users u ON r.generated_by = u.id
                WHERE r.brand_id = :brand_id AND r.deleted_at IS NULL
                ORDER BY r.week_start_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['brand_id' => $brandId]);
        return $stmt->fetchAll();
    }

    public function checkExists($brandId, $startDate, $endDate) {
        $sql = "SELECT id FROM {$this->table} 
                WHERE brand_id = :brand_id 
                AND week_start_date = :start 
                AND week_end_date = :end 
                AND deleted_at IS NULL LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['brand_id' => $brandId, 'start' => $startDate, 'end' => $endDate]);
        return $stmt->fetch();
    }
}