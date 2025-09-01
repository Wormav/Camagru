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
}
