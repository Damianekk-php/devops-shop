<?php

declare(strict_types=1);

namespace ShopStack\Tests\Integration;

use ShopStack\Models\Product;
use ShopStack\Services\CheckoutService;
use ShopStack\Tests\Support\MySqlTestCase;

final class CheckoutServiceTest extends MySqlTestCase
{
    public function testCheckoutCreatesOrderItemsAndDecrementsStock(): void
    {
        $service = new CheckoutService($this->pdo, new Product($this->pdo));
        $orderId = $service->placeOrder(1, $this->shipping(), [1 => 2, 2 => 1]);

        $order = $this->pdo->query('SELECT * FROM orders WHERE id = ' . $orderId)->fetch();
        $items = $this->pdo->query('SELECT * FROM order_items WHERE order_id = ' . $orderId . ' ORDER BY id')->fetchAll();
        $stock = $this->pdo->query('SELECT stock FROM products WHERE id = 1')->fetchColumn();

        self::assertSame('pending', $order['status']);
        self::assertSame('45.50', $order['total_amount']);
        self::assertCount(2, $items);
        self::assertSame('10.00', $items[0]['price']);
        self::assertSame(3, (int) $stock);
    }

    public function testCheckoutRejectsInsufficientStockAndCreatesNothing(): void
    {
        $service = new CheckoutService($this->pdo, new Product($this->pdo));
        $this->expectException(\InvalidArgumentException::class);
        try {
            $service->placeOrder(1, $this->shipping(), [2 => 3]);
        } finally {
            self::assertSame(0, (int) $this->pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn());
            self::assertSame(0, (int) $this->pdo->query('SELECT COUNT(*) FROM order_items')->fetchColumn());
            self::assertSame('2', (string) $this->pdo->query('SELECT stock FROM products WHERE id = 2')->fetchColumn());
        }
    }

    public function testCheckoutRollsBackWhenUserForeignKeyFails(): void
    {
        $service = new CheckoutService($this->pdo, new Product($this->pdo));
        $this->expectException(\PDOException::class);
        try {
            $service->placeOrder(999, $this->shipping(), [1 => 1]);
        } finally {
            self::assertSame(0, (int) $this->pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn());
            self::assertSame('5', (string) $this->pdo->query('SELECT stock FROM products WHERE id = 1')->fetchColumn());
        }
    }

    private function shipping(): array
    {
        return ['shipping_name' => 'Test User', 'shipping_address' => 'Test Street 1', 'shipping_city' => 'Test City', 'shipping_postal_code' => '00-001'];
    }
}
