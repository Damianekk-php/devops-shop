<?php

declare(strict_types=1);

namespace ShopStack\Tests\Integration;

use ShopStack\Models\Order;
use ShopStack\Tests\Support\MySqlTestCase;

final class OrderModelTest extends MySqlTestCase
{
    public function testUserOrdersAreScopedToThatUser(): void
    {
        $this->pdo->exec("INSERT INTO orders (user_id, status, total_amount, shipping_name, shipping_address, shipping_city, shipping_postal_code) VALUES (1, 'pending', 20.00, 'User', 'Street 1', 'City', '00-001'), (2, 'completed', 30.00, 'Admin', 'Street 2', 'City', '00-002')");
        $orders = (new Order($this->pdo))->forUser(1);

        self::assertCount(1, $orders);
        self::assertSame('20.00', $orders[0]['total_amount']);
    }

    public function testAdminOrderStatusCanBeChangedAndInvalidStatusIsRejectedByDatabase(): void
    {
        $this->pdo->exec("INSERT INTO orders (user_id, status, total_amount, shipping_name, shipping_address, shipping_city, shipping_postal_code) VALUES (1, 'pending', 20.00, 'User', 'Street 1', 'City', '00-001')");
        $id = (int) $this->pdo->lastInsertId();
        $orders = new Order($this->pdo);
        $orders->updateStatus($id, 'shipped');
        self::assertSame('shipped', $orders->find($id)['status']);
        $this->expectException(\InvalidArgumentException::class);
        $orders->updateStatus($id, 'not-a-status');
    }
}
