<?php

declare(strict_types=1);

$envFile = dirname(__DIR__) . '/.env';
if (is_file($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }
        [$key, $value] = explode('=', $line, 2);
        $_ENV[trim($key)] = trim($value, " \t\n\r\0\x0B\"");
    }
}

function env(string $key, mixed $default = null): mixed
{
    return $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key) ?: $default;
}

return [
    'env' => (string) env('APP_ENV', 'local'),
    'debug' => filter_var(env('APP_DEBUG', false), FILTER_VALIDATE_BOOLEAN),
    'url' => rtrim((string) env('APP_URL', 'http://localhost/sklepik/public'), '/'),
    'database' => [
        'host' => (string) env('DB_HOST', '127.0.0.1'),
        'port' => (string) env('DB_PORT', '3306'),
        'name' => (string) env('DB_DATABASE', 'shopstack'),
        'user' => (string) env('DB_USERNAME', 'root'),
        'password' => (string) env('DB_PASSWORD', ''),
    ],
];
