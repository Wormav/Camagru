<?php

class CSRFProtection {
    public static function generateToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function validateToken($token) {
        return isset($_SESSION['csrf_token']) &&
               hash_equals($_SESSION['csrf_token'], $token);
    }

    public static function getTokenField() {
        $token = self::generateToken();
        return '<input type="hidden" name="csrf_token" value="' .
               htmlspecialchars($token) . '">';
    }

    public static function regenerateToken() {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        return $_SESSION['csrf_token'];
    }
}
