<?php

class ForumThread extends Model
{
    protected $table = 'forum_threads';

    public function getAllByBrand($brandId, $limit = 10, $offset = 0)
    {
        // FIX: Tambahkan u.avatar_url dan perbaiki kalkulasi reply_count agar tidak minus (-1)
        // OPTIMIZATION: Hapus SELECT t.*, gunakan field spesifik untuk hemat RAM
        $sql = "SELECT t.id, t.title, t.is_pinned, t.is_locked, t.is_resolved, t.last_post_at, t.created_at, t.category_id,
                       u.full_name as creator_name, u.avatar_url as creator_avatar, c.name as category_name,
                       GREATEST(0, (SELECT COUNT(*) FROM forum_posts p WHERE p.thread_id = t.id) - 1) as reply_count
                FROM {$this->table} t
                LEFT JOIN users u ON t.created_by = u.id
                LEFT JOIN forum_categories c ON t.category_id = c.id
                WHERE t.brand_id = :brand_id AND t.deleted_at IS NULL
                ORDER BY t.is_pinned DESC, t.last_post_at DESC, t.created_at DESC
                LIMIT " . (int) $limit . " OFFSET " . (int) $offset;
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['brand_id' => $brandId]);
        return $stmt->fetchAll();
    }
}