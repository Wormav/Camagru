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

    public function getById($imageId) {
        $stmt = $this->db->prepare("SELECT * FROM images WHERE id = ?");
        $stmt->execute([$imageId]);
        return $stmt->fetch();
    }

    public function delete($imageId, $userId) {
        $stmt = $this->db->prepare("DELETE FROM images WHERE id = ? AND user_id = ?");
        return $stmt->execute([$imageId, $userId]);
    }

    public function getTotalCount() {
        $stmt = $this->db->query("SELECT COUNT(*) FROM images");
        return $stmt->fetchColumn();
    }

    public function getByIdWithUser($imageId) {
        $stmt = $this->db->prepare("
            SELECT i.*, u.username, u.profile_picture,
                   COUNT(DISTINCT l.id) as like_count,
                   COUNT(DISTINCT c.id) as comment_count
            FROM images i
            LEFT JOIN users u ON i.user_id = u.id
            LEFT JOIN likes l ON i.id = l.image_id
            LEFT JOIN comments c ON i.id = c.image_id
            WHERE i.id = ?
            GROUP BY i.id
        ");
        $stmt->execute([$imageId]);
        return $stmt->fetch();
    }

    public function getComments($imageId) {
        $stmt = $this->db->prepare("
            SELECT c.*, u.username, u.profile_picture
            FROM comments c
            LEFT JOIN users u ON c.user_id = u.id
            WHERE c.image_id = ?
            ORDER BY c.created_at ASC
        ");
        $stmt->execute([$imageId]);
        return $stmt->fetchAll();
    }

    public function toggleLike($userId, $imageId) {
        $this->db->beginTransaction();

        try {
            $stmt = $this->db->prepare("SELECT id FROM likes WHERE user_id = ? AND image_id = ?");
            $stmt->execute([$userId, $imageId]);
            $existingLike = $stmt->fetch();

            if ($existingLike) {
                // Supprimer le like existant
                $stmt = $this->db->prepare("DELETE FROM likes WHERE user_id = ? AND image_id = ?");
                $result = $stmt->execute([$userId, $imageId]);
                if (!$result) {
                    throw new Exception('Failed to delete like');
                }
                $this->db->commit();
                return false; // Pas liké maintenant
            } else {
                // Ajouter un nouveau like
                $stmt = $this->db->prepare("INSERT INTO likes (user_id, image_id) VALUES (?, ?)");
                $result = $stmt->execute([$userId, $imageId]);
                if (!$result) {
                    throw new Exception('Failed to insert like');
                }
                $this->db->commit();
                return true; // Liké maintenant
            }
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Error in toggleLike: " . $e->getMessage());
            throw $e; // Relancer l'exception
        }
    }

    public function getLikeCount($imageId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM likes WHERE image_id = ?");
        $stmt->execute([$imageId]);
        return $stmt->fetchColumn();
    }

    public function isLikedByUser($userId, $imageId) {
        $stmt = $this->db->prepare("SELECT id FROM likes WHERE user_id = ? AND image_id = ?");
        $stmt->execute([$userId, $imageId]);
        return $stmt->fetch() !== false;
    }

    public function addComment($userId, $imageId, $comment) {
        $stmt = $this->db->prepare("
            INSERT INTO comments (user_id, image_id, content)
            VALUES (?, ?, ?)
        ");

        if ($stmt->execute([$userId, $imageId, $comment])) {
            return $this->db->lastInsertId();
        }
        return false;
    }

    public function deleteComment($commentId, $userId) {
        $stmt = $this->db->prepare("DELETE FROM comments WHERE id = ? AND user_id = ?");
        return $stmt->execute([$commentId, $userId]);
    }
}
