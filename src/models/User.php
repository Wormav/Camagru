<?php

class User {
    private $db;

    public function __construct() {
        $this->db = DatabaseConfig::getConnection();
    }

    public function create($username, $email, $password, $verificationToken = null) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $token = $verificationToken ?: bin2hex(random_bytes(32));

        $stmt = $this->db->prepare("
            INSERT INTO users (username, email, password_hash, verification_token)
            VALUES (?, ?, ?, ?)
        ");

        return $stmt->execute([$username, $email, $hashedPassword, $token]);
    }    public function findByUsername($username) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch();
    }

    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function findByUsernameOrEmail($login) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$login, $login]);
        return $stmt->fetch();
    }

    public function verifyEmail($token) {
        $stmt = $this->db->prepare("
            UPDATE users
            SET email_verified = TRUE, verification_token = NULL
            WHERE verification_token = ?
        ");
        return $stmt->execute([$token]);
    }

    public function updatePassword($userId, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
        return $stmt->execute([$hashedPassword, $userId]);
    }

    public function createResetToken($userId, $resetToken) {
        $stmt = $this->db->prepare("UPDATE users SET reset_token = ? WHERE id = ?");
        return $stmt->execute([$resetToken, $userId]);
    }

    public function findByResetToken($token) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE reset_token = ?");
        $stmt->execute([$token]);
        return $stmt->fetch();
    }

    public function resetPassword($userId, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("
            UPDATE users
            SET password_hash = ?, reset_token = NULL
            WHERE id = ?
        ");
        return $stmt->execute([$hashedPassword, $userId]);
    }

    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function updateProfile($userId, $username, $email) {
        $stmt = $this->db->prepare("
            UPDATE users
            SET username = ?, email = ?, updated_at = CURRENT_TIMESTAMP
            WHERE id = ?
        ");
        return $stmt->execute([$username, $email, $userId]);
    }

    public function updateProfilePicture($userId, $profilePicture) {
        $stmt = $this->db->prepare("
            UPDATE users
            SET profile_picture = ?, updated_at = CURRENT_TIMESTAMP
            WHERE id = ?
        ");
        return $stmt->execute([$profilePicture, $userId]);
    }

    public function checkEmailExists($email, $excludeUserId = null) {
        if ($excludeUserId) {
            $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->execute([$email, $excludeUserId]);
        } else {
            $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
        }
        return $stmt->fetch() !== false;
    }

    public function checkUsernameExists($username, $excludeUserId = null) {
        if ($excludeUserId) {
            $stmt = $this->db->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
            $stmt->execute([$username, $excludeUserId]);
        } else {
            $stmt = $this->db->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
        }
        return $stmt->fetch() !== false;
    }

    public function verifyPassword($userId, $password) {
        $stmt = $this->db->prepare("SELECT password_hash FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();

        if ($user) {
            return password_verify($password, $user['password_hash']);
        }
        return false;
    }
}
