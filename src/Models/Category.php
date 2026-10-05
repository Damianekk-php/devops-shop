<?php

declare(strict_types=1);

namespace ShopStack\Models;

use PDO;

final class Category
{
    public function __construct(private readonly PDO $pdo) {}
    public function all(): array { return $this->pdo->query('SELECT * FROM categories ORDER BY name')->fetchAll(); }
    public function find(int $id): ?array { $s = $this->pdo->prepare('SELECT * FROM categories WHERE id = :id'); $s->execute(['id' => $id]); return $s->fetch() ?: null; }
    public function save(array $data, ?int $id = null): int { if ($id === null) { $s = $this->pdo->prepare('INSERT INTO categories (name, slug, description) VALUES (:name, :slug, :description)'); } else { $data['id'] = $id; $s = $this->pdo->prepare('UPDATE categories SET name = :name, slug = :slug, description = :description, updated_at = NOW() WHERE id = :id'); } $s->execute($data); return $id ?? (int) $this->pdo->lastInsertId(); }
    public function delete(int $id): void { $s = $this->pdo->prepare('DELETE FROM categories WHERE id = :id'); $s->execute(['id' => $id]); }
}
