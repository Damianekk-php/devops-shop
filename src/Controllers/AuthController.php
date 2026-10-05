<?php

declare(strict_types=1);

namespace ShopStack\Controllers;

use ShopStack\Core\Logger;
use ShopStack\Core\View;
use ShopStack\Models\User;

final class AuthController
{
    public function __construct(private readonly View $view, private readonly User $users, private readonly Logger $logger) {}
    public function loginForm(): void { $this->view->render('auth/login'); }
    public function login(): never { $email = strtolower(trim((string) ($_POST['email'] ?? ''))); $password = (string) ($_POST['password'] ?? ''); $user = $this->users->findByEmail($email); if (!$user || !password_verify($password, $user['password'])) { $this->logger->warning('Failed login', ['email' => $email]); flash('error', 'Nieprawidłowy email lub hasło.'); redirect('login'); } session_regenerate_id(true); unset($user['password']); $_SESSION['user'] = $user; flash('success', 'Zalogowano pomyślnie.'); redirect('account'); }
    public function registerForm(): void { $this->view->render('auth/register'); }
    public function register(): never { $name = trim((string) ($_POST['name'] ?? '')); $email = strtolower(trim((string) ($_POST['email'] ?? ''))); $password = (string) ($_POST['password'] ?? ''); if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) { flash('error', 'Podaj poprawne dane. Hasło musi mieć co najmniej 8 znaków.'); redirect('register'); } if ($this->users->findByEmail($email)) { flash('error', 'Ten email jest już zajęty.'); redirect('register'); } try { $this->users->create($name, $email, $password); flash('success', 'Konto utworzone. Możesz się zalogować.'); redirect('login'); } catch (\Throwable $e) { $this->logger->error('Registration failed', ['exception' => $e->getMessage()]); flash('error', 'Nie udało się utworzyć konta.'); redirect('register'); } }
    public function logout(): never { $_SESSION = []; if (ini_get('session.use_cookies')) { $params = session_get_cookie_params(); setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], (bool) $params['secure'], (bool) $params['httponly']); } session_destroy(); redirect(''); }
}
