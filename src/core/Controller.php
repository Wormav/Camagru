<?php

class Controller {
    protected $db;

    public function __construct() {
        $this->db = DatabaseConfig::getConnection();
    }

    protected function view($template, $data = []) {
        extract($data);
        require_once __DIR__ . "/../views/{$template}.php";
    }

    protected function redirect($url) {
        header("Location: $url");
        exit;
    }

    protected function json($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
