<?php

declare(strict_types=1);

namespace ShopStack\Core;

final class Router
{
    private array $routes = [];

    public function get(string $path, callable $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    public function post(string $path, callable $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    public function dispatch(string $method, string $uri): mixed
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $path = preg_replace('#^/sklepik/public#', '', $path) ?: '/';
        $path = rtrim($path, '/') ?: '/';

        foreach ($this->routes as $route) {
            if ($route['method'] !== strtoupper($method)) {
                continue;
            }
            $pattern = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '([^/]+)', $route['path']);
            if (preg_match('#^' . $pattern . '$#', $path, $matches)) {
                array_shift($matches);
                return ($route['handler'])(...array_map('urldecode', $matches));
            }
        }

        http_response_code(404);
        return 'Not found';
    }

    private function add(string $method, string $path, callable $handler): void
    {
        $this->routes[] = ['method' => $method, 'path' => rtrim($path, '/') ?: '/', 'handler' => $handler];
    }
}
