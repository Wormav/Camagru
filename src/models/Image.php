<?php

class Image {
    private $db;

    public function __construct() {
        $this->db = DatabaseConfig::getConnection();
    }

    public function create($userId, $filename, $originalFilename, $overlayUsed) {
        $stmt = $this->db->prepare("
            INSERT INTO images (user_id, filename, original_filename, overlay_used)
            VALUES (?, ?, ?, ?)
        ");

        return $stmt->execute([$userId, $filename, $originalFilename, $overlayUsed]);
    }

    public function getAllWithPagination($page = 1, $limit = IMAGES_PER_PAGE) {
        $offset = ($page - 1) * $limit;

        $stmt = $this->db->prepare("
            SELECT i.*, u.username,
                   COUNT(DISTINCT l.id) as like_count,
                   COUNT(DISTINCT c.id) as comment_count
            FROM images i
            LEFT JOIN users u ON i.user_id = u.id
            LEFT JOIN likes l ON i.id = l.image_id
            LEFT JOIN comments c ON i.id = c.image_id
            GROUP BY i.id
            ORDER BY i.created_at DESC
            LIMIT ? OFFSET ?
        ");

        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll();
    }

    public function getByUserId($userId) {
        $stmt = $this->db->prepare("
            SELECT * FROM images
            WHERE user_id = ?
            ORDER BY created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function delete($imageId, $userId) {
        $stmt = $this->db->prepare("DELETE FROM images WHERE id = ? AND user_id = ?");
        return $stmt->execute([$imageId, $userId]);
    }

    public function getTotalCount() {
        $stmt = $this->db->query("SELECT COUNT(*) FROM images");
        return $stmt->fetchColumn();
    }
}
