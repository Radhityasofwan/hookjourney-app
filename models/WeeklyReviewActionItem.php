<?php

class WeeklyReviewActionItem extends Model {
    protected $table = 'weekly_review_action_items';

    public function getByReviewId($reviewId) {
        $sql = "SELECT a.*, u.full_name as owner_name 
                FROM {$this->table} a
                LEFT JOIN users u ON a.owner_user_id = u.id
                WHERE a.weekly_review_id = :review_id 
                ORDER BY a.id ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['review_id' => $reviewId]);
        return $stmt->fetchAll();
    }
}