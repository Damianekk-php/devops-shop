<?php

declare(strict_types=1);

namespace ShopStack\Controllers;

use ShopStack\Core\View;
use ShopStack\Models\Category;
use ShopStack\Models\Product;
use ShopStack\Services\CartService;

final class ShopController
{
    public function __construct(private readonly View $view, private readonly Product $products, private readonly Category $categories, private readonly CartService $cart) {}
    public function home(): void { $this->view->render('home', ['products' => array_slice($this->products->all(), 0, 6)]); }
    public function products(): void { $this->view->render('products/index', ['products' => $this->products->all($_GET['q'] ?? null, isset($_GET['category']) ? (int) $_GET['category'] : null, $_GET['sort'] ?? 'newest'), 'categories' => $this->categories->all(), 'query' => $_GET['q'] ?? '', 'selectedCategory' => (int) ($_GET['category'] ?? 0), 'sort' => $_GET['sort'] ?? 'newest']); }
    public function product(string $id): void { $product = $this->products->find((int) $id); if (!$product) { http_response_code(404); exit('Produkt nie istnieje.'); } $this->view->render('products/show', ['product' => $product]); }
    public function cart(): void { $this->view->render('cart/index', ['items' => $this->cart->contents(), 'total' => $this->cart->total()]); }
    public function addToCart(): never { try { $this->cart->add((int) ($_POST['product_id'] ?? 0), (int) ($_POST['quantity'] ?? 1)); flash('success', 'Produkt dodano do koszyka.'); } catch (\Throwable $e) { flash('error', $e->getMessage()); } redirect('cart'); }
    public function updateCart(): never { try { $this->cart->update($_POST['quantities'] ?? []); flash('success', 'Koszyk został zaktualizowany.'); } catch (\Throwable $e) { flash('error', $e->getMessage()); } redirect('cart'); }
    public function removeFromCart(string $id): never { $this->cart->remove((int) $id); redirect('cart'); }
}
