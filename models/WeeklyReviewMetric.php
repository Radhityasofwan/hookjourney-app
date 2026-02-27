<?php

class WeeklyReviewMetric extends Model {
    protected $table = 'weekly_review_metrics';

    public function getByReviewId($reviewId) {
        $sql = "SELECT * FROM {$this->table} WHERE weekly_review_id = :review_id ORDER BY section ASC, metric_label ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['review_id' => $reviewId]);
        return $stmt->fetchAll();
    }
}