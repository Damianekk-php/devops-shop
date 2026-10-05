<?php

declare(strict_types=1);

namespace ShopStack\Models;

use PDO;

final class User
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function findByEmail(string $email): ?array
    {
        $statement = $this->pdo->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $statement->execute(['email' => $email]);
        return $statement->fetch() ?: null;
    }

    public function create(string $name, string $email, string $password): int
    {
        $statement = $this->pdo->prepare('INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, \'user\')');
        $statement->execute(['name' => $name, 'email' => $email, 'password' => password_hash($password, PASSWORD_DEFAULT)]);
        return (int) $this->pdo->lastInsertId();
    }

    public function all(): array
    {
        return $this->pdo->query('SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC')->fetchAll();
    }

    public function updateRole(int $id, string $role): void
    {
        $statement = $this->pdo->prepare('UPDATE users SET role = :role, updated_at = NOW() WHERE id = :id');
        $statement->execute(['role' => $role, 'id' => $id]);
    }
}
