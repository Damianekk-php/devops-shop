<?php

declare(strict_types=1);

namespace ShopStack\Controllers;

use ShopStack\Core\Logger;
use ShopStack\Core\View;
use ShopStack\Services\CartService;
use ShopStack\Services\CheckoutService;

final class CheckoutController
{
    public function __construct(private readonly View $view, private readonly CartService $cart, private readonly CheckoutService $checkout, private readonly Logger $logger) {}
    public function form(): void { require_auth(); $this->view->render('checkout/index', ['items' => $this->cart->contents(), 'total' => $this->cart->total()]); }
    public function store(): never { require_auth(); $fields = ['shipping_name', 'shipping_address', 'shipping_city', 'shipping_postal_code']; $shipping = []; foreach ($fields as $field) $shipping[$field] = trim((string) ($_POST[$field] ?? '')); if (in_array('', $shipping, true)) { flash('error', 'Uzupełnij wszystkie dane dostawy.'); redirect('checkout'); } try { $this->cart->contents(); $id = $this->checkout->placeOrder((int) current_user()['id'], $shipping, $_SESSION['cart'] ?? []); $this->cart->clear(); flash('success', 'Zamówienie zostało złożone.'); redirect('account/orders'); } catch (\Throwable $e) { $this->logger->error('Checkout failed', ['user_id' => current_user()['id'], 'exception' => $e->getMessage()]); flash('error', 'Nie udało się złożyć zamówienia. Sprawdź dostępność produktów.'); redirect('checkout'); } }
}
