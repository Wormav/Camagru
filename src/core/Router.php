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
            return;
        }

        foreach ($this->routes[$method] as $route => $handler) {
            if ($this->matchRoute($route, $path)) {
                $params = $this->extractParams($route, $path);
                list($controller, $action) = explode('@', $handler);

                $controllerInstance = new $controller();

                if (!empty($params)) {
                    call_user_func_array([$controllerInstance, $action], $params);
                } else {
                    $controllerInstance->$action();
                }
                return;
            }
        }

        http_response_code(404);
        echo "404 - Page not found";
    }

    private function matchRoute($route, $path) {
        $routePattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $route);
        $routePattern = '#^' . $routePattern . '$#';
        return preg_match($routePattern, $path);
    }

    private function extractParams($route, $path) {
        $routePattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $route);
        $routePattern = '#^' . $routePattern . '$#';

        if (preg_match($routePattern, $path, $matches)) {
            array_shift($matches);
            return $matches;
        }

        return [];
    }
}
