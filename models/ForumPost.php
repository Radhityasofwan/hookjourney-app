<?php

class ForumPost extends Model {
    protected $table = 'forum_posts';

    public function getByThread($threadId) {
        // FIX: Tambahkan u.avatar_url agar foto profil muncul di setiap postingan
        $sql = "SELECT p.*, u.full_name as author_name, u.avatar_url as author_avatar, u.role_global
                FROM {$this->table} p
                LEFT JOIN users u ON p.user_id = u.id
                WHERE p.thread_id = :thread_id
                ORDER BY p.created_at ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['thread_id' => $threadId]);
        return $stmt->fetchAll();
    }
}