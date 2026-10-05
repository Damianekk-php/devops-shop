<?php

declare(strict_types=1);

namespace ShopStack\Services;

use PDO;
use ShopStack\Models\Product;

final class CheckoutService
{
    public function __construct(private readonly PDO $pdo, private readonly Product $products) {}
    public function placeOrder(int $userId, array $shipping, array $cart): int
    {
        if ($cart === []) throw new \InvalidArgumentException('Koszyk jest pusty.');
        $this->pdo->beginTransaction();
        try {
            $fresh = $this->products->findMany(array_keys($cart), true); $total = 0.0;
            foreach ($cart as $productId => $quantity) { if (!isset($fresh[$productId]) || $quantity < 1 || $quantity > (int) $fresh[$productId]['stock']) throw new \InvalidArgumentException('Produkt jest niedostępny w wybranej ilości.'); $total += (float) $fresh[$productId]['price'] * $quantity; }
            $order = $this->pdo->prepare('INSERT INTO orders (user_id, status, total_amount, shipping_name, shipping_address, shipping_city, shipping_postal_code) VALUES (:user_id, \'pending\', :total, :shipping_name, :shipping_address, :shipping_city, :shipping_postal_code)');
            $order->execute(['user_id' => $userId, 'total' => $total, 'shipping_name' => $shipping['shipping_name'], 'shipping_address' => $shipping['shipping_address'], 'shipping_city' => $shipping['shipping_city'], 'shipping_postal_code' => $shipping['shipping_postal_code']]); $orderId = (int) $this->pdo->lastInsertId();
            $item = $this->pdo->prepare('INSERT INTO order_items (order_id, product_id, product_name, price, quantity, subtotal) VALUES (:order_id, :product_id, :product_name, :price, :quantity, :subtotal)'); $stock = $this->pdo->prepare('UPDATE products SET stock = stock - :quantity, updated_at = NOW() WHERE id = :id');
            foreach ($cart as $productId => $quantity) { $product = $fresh[$productId]; $item->execute(['order_id' => $orderId, 'product_id' => $productId, 'product_name' => $product['name'], 'price' => $product['price'], 'quantity' => $quantity, 'subtotal' => (float) $product['price'] * $quantity]); $stock->execute(['quantity' => $quantity, 'id' => $productId]); }
            $this->pdo->commit(); return $orderId;
        } catch (\Throwable $exception) { if ($this->pdo->inTransaction()) $this->pdo->rollBack(); throw $exception; }
    }
}
