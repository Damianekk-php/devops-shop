<?php

declare(strict_types=1);

namespace ShopStack\Services;

use ShopStack\Models\Product;

final class CartService
{
    public function __construct(private readonly Product $products) { $_SESSION['cart'] ??= []; }
    public function add(int $productId, int $quantity): void { $product = $this->products->find($productId); if (!$product || $quantity < 1) throw new \InvalidArgumentException('Nieprawidłowy produkt.'); $current = (int) ($_SESSION['cart'][$productId] ?? 0); if ($current + $quantity > (int) $product['stock']) throw new \InvalidArgumentException('Brak wystarczającej liczby produktów.'); $_SESSION['cart'][$productId] = $current + $quantity; }
    public function update(array $quantities): void { foreach ($quantities as $id => $quantity) { $id = (int) $id; $quantity = max(0, (int) $quantity); $product = $this->products->find($id); if (!$product) { unset($_SESSION['cart'][$id]); continue; } if ($quantity > (int) $product['stock']) throw new \InvalidArgumentException('Ilość przekracza dostępny stan.'); if ($quantity === 0) unset($_SESSION['cart'][$id]); else $_SESSION['cart'][$id] = $quantity; } }
    public function remove(int $productId): void { unset($_SESSION['cart'][$productId]); }
    public function clear(): void { $_SESSION['cart'] = []; }
    public function contents(): array { $products = $this->products->findMany(array_map('intval', array_keys($_SESSION['cart']))); $items = []; foreach ($_SESSION['cart'] as $id => $quantity) { $id = (int) $id; if (!isset($products[$id])) { unset($_SESSION['cart'][$id]); continue; } $quantity = (int) $quantity; if ($quantity < 1) { unset($_SESSION['cart'][$id]); continue; } $product = $products[$id]; $product['quantity'] = $quantity; $product['subtotal'] = (float) $product['price'] * $quantity; $items[] = $product; } return $items; }
    public function total(): float { return array_sum(array_column($this->contents(), 'subtotal')); }
}
