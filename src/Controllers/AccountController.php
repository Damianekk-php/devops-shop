<?php

declare(strict_types=1);

namespace ShopStack\Controllers;

use ShopStack\Core\View;
use ShopStack\Models\Order;

final class AccountController
{
    public function __construct(private readonly View $view, private readonly Order $orders) {}
    public function index(): void { $this->view->render('account/index'); }
    public function orders(): void { $orders = $this->orders->forUser((int) current_user()['id']); foreach ($orders as &$order) $order['items'] = $this->orders->items((int) $order['id']); $this->view->render('orders/index', ['orders' => $orders]); }
}
