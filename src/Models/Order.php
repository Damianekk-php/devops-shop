<?php

declare(strict_types=1);

namespace ShopStack\Models;

use PDO;

final class Order
{
    public function __construct(private readonly PDO $pdo) {}
    public function forUser(int $userId): array { $s = $this->pdo->prepare('SELECT * FROM orders WHERE user_id = :user_id ORDER BY created_at DESC'); $s->execute(['user_id' => $userId]); return $s->fetchAll(); }
    public function items(int $orderId): array { $s = $this->pdo->prepare('SELECT * FROM order_items WHERE order_id = :order_id'); $s->execute(['order_id' => $orderId]); return $s->fetchAll(); }
    public function all(): array { return $this->pdo->query('SELECT o.*, u.name AS user_name, u.email FROM orders o JOIN users u ON u.id = o.user_id ORDER BY o.created_at DESC')->fetchAll(); }
    public function find(int $id): ?array { $s = $this->pdo->prepare('SELECT o.*, u.name AS user_name, u.email FROM orders o JOIN users u ON u.id = o.user_id WHERE o.id = :id'); $s->execute(['id' => $id]); return $s->fetch() ?: null; }
    public function updateStatus(int $id, string $status): void { $s = $this->pdo->prepare('UPDATE orders SET status = :status, updated_at = NOW() WHERE id = :id'); $s->execute(['status' => $status, 'id' => $id]); }
    public function count(): int { return (int) $this->pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn(); }
    public function pendingCount(): int { return (int) $this->pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn(); }
    public function revenue(): float { return (float) $this->pdo->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE status <> 'cancelled'")->fetchColumn(); }
}
