<?php

class Controller {
    protected $db;

    public function __construct() {
        $this->db = DatabaseConfig::getConnection();
    }

    protected function view($template, $data = []) {
        extract($data);

        ob_start();
        require_once __DIR__ . "/../views/{$template}.php";
        $content = ob_get_clean();

        require_once __DIR__ . "/../views/layouts/main.php";
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
