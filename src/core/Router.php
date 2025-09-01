<?php

class Router {
    private $routes = [];

    public function get($path, $handler) {
        $this->routes['GET'][$path] = $handler;
    }

    public function post($path, $handler) {
        $this->routes['POST'][$path] = $handler;
    }

    public function dispatch($uri, $method) {
        $path = parse_url($uri, PHP_URL_PATH);

        if (isset($this->routes[$method][$path])) {
            $handler = $this->routes[$method][$path];
            list($controller, $action) = explode('@', $handler);

            $controllerInstance = new $controller();
            $controllerInstance->$action();
        } else {
            http_response_code(404);
            echo "404 - Page not found";
        }
    }
}
