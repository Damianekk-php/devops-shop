<?php

declare(strict_types=1);

namespace ShopStack\Models;

use PDO;

final class Product
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function all(?string $search = null, ?int $categoryId = null, string $sort = 'newest'): array
    {
        $conditions = ['p.stock >= 0'];
        $params = [];
        if ($search !== null && $search !== '') { $conditions[] = '(p.name LIKE :search OR p.description LIKE :search)'; $params['search'] = '%' . $search . '%'; }
        if ($categoryId !== null) { $conditions[] = 'p.category_id = :category_id'; $params['category_id'] = $categoryId; }
        $order = ['price_asc' => 'p.price ASC', 'price_desc' => 'p.price DESC', 'name' => 'p.name ASC'][$sort] ?? 'p.created_at DESC';
        $statement = $this->pdo->prepare('SELECT p.*, c.name AS category_name, c.slug AS category_slug FROM products p JOIN categories c ON c.id = p.category_id WHERE ' . implode(' AND ', $conditions) . ' ORDER BY ' . $order);
        $statement->execute($params);
        return $statement->fetchAll();
    }

    public function find(int $id): ?array
    {
        $statement = $this->pdo->prepare('SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON c.id = p.category_id WHERE p.id = :id');
        $statement->execute(['id' => $id]);
        return $statement->fetch() ?: null;
    }

    public function findMany(array $ids, bool $forUpdate = false): array
    {
        if ($ids === []) return [];
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $statement = $this->pdo->prepare('SELECT * FROM products WHERE id IN (' . $placeholders . ')' . ($forUpdate ? ' FOR UPDATE' : ''));
        $statement->execute(array_values($ids));
        $products = [];
        foreach ($statement->fetchAll() as $product) $products[(int) $product['id']] = $product;
        return $products;
    }

    public function save(array $data, ?int $id = null): int
    {
        if ($id === null) {
            $statement = $this->pdo->prepare('INSERT INTO products (category_id, name, slug, description, price, stock, image) VALUES (:category_id, :name, :slug, :description, :price, :stock, :image)');
        } else {
            $data['id'] = $id;
            $statement = $this->pdo->prepare('UPDATE products SET category_id = :category_id, name = :name, slug = :slug, description = :description, price = :price, stock = :stock, image = :image, updated_at = NOW() WHERE id = :id');
        }
        $statement->execute($data);
        return $id ?? (int) $this->pdo->lastInsertId();
    }

    public function delete(int $id): void { $statement = $this->pdo->prepare('DELETE FROM products WHERE id = :id'); $statement->execute(['id' => $id]); }
    public function lowStock(int $limit = 5): array { $statement = $this->pdo->prepare('SELECT * FROM products WHERE stock <= :limit ORDER BY stock ASC'); $statement->execute(['limit' => $limit]); return $statement->fetchAll(); }
    public function count(): int { return (int) $this->pdo->query('SELECT COUNT(*) FROM products')->fetchColumn(); }
}
