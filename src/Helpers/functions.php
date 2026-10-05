<?php

declare(strict_types=1);

function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function old(string $key, mixed $default = ''): mixed
{
    return $_SESSION['_old'][$key] ?? $default;
}

function flash(string $key, ?string $value = null): ?string
{
    if ($value !== null) {
        $_SESSION['_flash'][$key] = $value;
        return null;
    }
    $message = $_SESSION['_flash'][$key] ?? null;
    unset($_SESSION['_flash'][$key]);
    return $message;
}

function redirect(string $path): never
{
    global $config;
    header('Location: ' . $config['url'] . '/' . ltrim($path, '/'));
    exit;
}

function csrf_token(): string
{
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf'];
}

function verify_csrf(): void
{
    $token = $_POST['_csrf'] ?? '';
    if (!is_string($token) || !hash_equals($_SESSION['_csrf'] ?? '', $token)) {
        http_response_code(419);
        exit('Invalid CSRF token.');
    }
}

function csrf_handler(callable $handler): callable
{
    return static function (...$arguments) use ($handler): mixed {
        verify_csrf();
        return $handler(...$arguments);
    };
}

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function require_auth(): void
{
    if (current_user() === null) {
        flash('error', 'Zaloguj się, aby kontynuować.');
        redirect('login');
    }
}

function require_admin(): void
{
    require_auth();
    if ((current_user()['role'] ?? '') !== 'admin') {
        http_response_code(403);
        exit('Brak uprawnień.');
    }
}

function money(float|int|string $amount): string
{
    return number_format((float) $amount, 2, ',', ' ') . ' zł';
}
