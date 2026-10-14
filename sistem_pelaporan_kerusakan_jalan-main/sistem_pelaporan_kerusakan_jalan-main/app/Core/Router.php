<?php
namespace App\Core;

class Router
{
    private array $routes = [];
    private string $basePath;

    public function __construct(?array $appConfig = null)
    {
        $appConfig ??= require __DIR__ . '/../Config/app.php';
        $this->basePath = rtrim($appConfig['base_url'] ?? '', '/');
    }

    public function get(string $path, string $controller, string $method): void
    {
        $this->routes['GET'][$path] = ['controller' => $controller, 'method' => $method];
    }

    public function post(string $path, string $controller, string $method): void
    {
        $this->routes['POST'][$path] = ['controller' => $controller, 'method' => $method];
    }

    public function dispatch(string $requestUri, string $requestMethod): void
    {
        $uri = parse_url($requestUri, PHP_URL_PATH);
        if ($this->basePath !== '') {
            $uri = preg_replace('#^' . preg_quote($this->basePath, '#') . '#', '', $uri);
        }
        $uri = '/' . trim($uri, '/');
        if ($uri === '/') $uri = '/';

        $method = strtoupper($requestMethod);

        if (isset($this->routes[$method][$uri])) {
            $this->callRoute($this->routes[$method][$uri], []);
            return;
        }

        foreach ($this->routes[$method] ?? [] as $route => $handler) {
            $pattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $route);
            $pattern = '@^' . $pattern . '$@';
            if (preg_match($pattern, $uri, $matches)) {
                array_shift($matches);
                $this->callRoute($handler, $matches);
                return;
            }
        }

        http_response_code(404);
        $appConfig = require __DIR__ . '/../Config/app.php';
        require __DIR__ . '/../Views/errors/404.php';
    }

    private function callRoute(array $handler, array $params): void
    {
        $controllerClass = $handler['controller'];
        $method          = $handler['method'];

        if (!class_exists($controllerClass)) {
            http_response_code(500);
            die('Controller tidak ditemukan: ' . htmlspecialchars($controllerClass));
        }

        $controller = new $controllerClass();
        $controller->$method(...$params);
    }
}
