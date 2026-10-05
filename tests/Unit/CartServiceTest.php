<?php

declare(strict_types=1);

namespace ShopStack\Tests\Unit;

use PHPUnit\Framework\TestCase;
use PDO;
use ShopStack\Models\Product;
use ShopStack\Services\CartService;

final class CartServiceTest extends TestCase
{
    private CartService $cart;

    protected function setUp(): void
    {
        $_SESSION = [];
        $pdo = new PDO('sqlite::memory:');
        $pdo->exec('CREATE TABLE categories (id INTEGER PRIMARY KEY, name TEXT, slug TEXT, description TEXT)');
        $pdo->exec('CREATE TABLE products (id INTEGER PRIMARY KEY, category_id INTEGER, name TEXT, slug TEXT, description TEXT, price DECIMAL(10,2), stock INTEGER, image TEXT)');
        $pdo->exec("INSERT INTO categories (id, name, slug, description) VALUES (1, 'Test', 'test', 'Test')");
        $pdo->exec("INSERT INTO products (id, category_id, name, slug, description, price, stock) VALUES (1, 1, 'Notes', 'notes', 'Test', 10.00, 5), (2, 1, 'Lampka', 'lampka', 'Test', 25.50, 2)");
        $this->cart = new CartService(new Product($pdo));
    }

    public function testAddsProductAndCalculatesSubtotalAndTotal(): void
    {
        $this->cart->add(1, 2);
        $this->cart->add(2, 1);

        self::assertSame(2, $_SESSION['cart'][1]);
        self::assertSame(1, $_SESSION['cart'][2]);
        self::assertSame(2, $this->cart->contents()[0]['quantity']);
        self::assertSame(20.0, $this->cart->contents()[0]['subtotal']);
        self::assertSame(45.5, $this->cart->total());
    }

    public function testAddingSameProductIncreasesQuantity(): void
    {
        $this->cart->add(1, 1);
        $this->cart->add(1, 2);

        self::assertSame(3, $_SESSION['cart'][1]);
    }

    public function testRejectsZeroNegativeAndUnavailableQuantities(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->cart->add(1, 0);
    }

    public function testRejectsQuantityAboveStock(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->cart->add(1, 6);
    }

    public function testRejectsUnknownProduct(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->cart->add(999, 1);
    }

    public function testUpdateRemovesWhenQuantityIsZeroAndRejectsOverstock(): void
    {
        $this->cart->add(1, 2);
        $this->cart->update(['1' => 0]);
        self::assertSame([], $_SESSION['cart']);

        $this->expectException(\InvalidArgumentException::class);
        $this->cart->update(['2' => 3]);
    }

    public function testRemoveAndClearEmptyTheCart(): void
    {
        $this->cart->add(1, 1);
        $this->cart->add(2, 1);
        $this->cart->remove(1);
        self::assertArrayNotHasKey(1, $_SESSION['cart']);
        $this->cart->clear();
        self::assertSame([], $_SESSION['cart']);
        self::assertSame(0.0, $this->cart->total());
    }

    public function testContentsRemovesStaleSessionProducts(): void
    {
        $_SESSION['cart'] = [999 => 2, 1 => 1];

        $items = $this->cart->contents();

        self::assertCount(1, $items);
        self::assertArrayNotHasKey(999, $_SESSION['cart']);
    }
}
