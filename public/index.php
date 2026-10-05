<?php

declare(strict_types=1);

use ShopStack\Controllers\AccountController;
use ShopStack\Controllers\AdminController;
use ShopStack\Controllers\AuthController;
use ShopStack\Controllers\CheckoutController;
use ShopStack\Controllers\ShopController;
use ShopStack\Core\Database;
use ShopStack\Core\Logger;
use ShopStack\Core\Router;
use ShopStack\Core\View;
use ShopStack\Middleware\SessionMiddleware;
use ShopStack\Models\Category;
use ShopStack\Models\Order;
use ShopStack\Models\Product;
use ShopStack\Models\User;
use ShopStack\Services\CartService;
use ShopStack\Services\CheckoutService;

$config = require dirname(__DIR__) . '/config/config.php';
require dirname(__DIR__) . '/src/Helpers/functions.php';
require dirname(__DIR__) . '/src/Middleware/SessionMiddleware.php';
foreach (glob(dirname(__DIR__) . '/src/Core/*.php') as $file) require_once $file;
foreach (glob(dirname(__DIR__) . '/src/Models/*.php') as $file) require_once $file;
foreach (glob(dirname(__DIR__) . '/src/Services/*.php') as $file) require_once $file;
foreach (glob(dirname(__DIR__) . '/src/Controllers/*.php') as $file) require_once $file;

SessionMiddleware::start();
$logger = new Logger(dirname(__DIR__) . '/storage/logs/app.log');
try { $database = new Database($config['database'], $logger); $pdo = $database->pdo(); } catch (Throwable $exception) { http_response_code(503); echo 'Aplikacja chwilowo niedostępna. Skonfiguruj bazę danych i spróbuj ponownie.'; exit; }
$view = new View(dirname(__DIR__) . '/templates', $config);
$products = new Product($pdo); $categories = new Category($pdo); $users = new User($pdo); $orders = new Order($pdo); $cart = new CartService($products);
$shop = new ShopController($view, $products, $categories, $cart); $auth = new AuthController($view, $users, $logger); $account = new AccountController($view, $orders); $checkout = new CheckoutController($view, $cart, new CheckoutService($pdo, $products), $logger); $admin = new AdminController($view, $products, $categories, $orders, $users, $logger);
$router = new Router();
$router->get('/health', function () use ($pdo): void {
    try {
        $pdo->query('SELECT 1');

        header('Content-Type: application/json');
        http_response_code(200);

        echo json_encode([
            'status' => 'ok',
            'database' => 'ok',
        ]);
    } catch (Throwable $exception) {
        header('Content-Type: application/json');
        http_response_code(503);

        echo json_encode([
            'status' => 'error',
            'database' => 'error',
        ]);
    }
});
$router->get('/', [$shop, 'home']); $router->get('/products', [$shop, 'products']); $router->get('/products/{id}', [$shop, 'product']); $router->get('/cart', [$shop, 'cart']); $router->post('/cart/add', csrf_handler([$shop, 'addToCart'])); $router->post('/cart/update', csrf_handler([$shop, 'updateCart'])); $router->post('/cart/remove/{id}', csrf_handler([$shop, 'removeFromCart']));
$router->get('/login', [$auth, 'loginForm']); $router->post('/login', csrf_handler([$auth, 'login'])); $router->get('/register', [$auth, 'registerForm']); $router->post('/register', csrf_handler([$auth, 'register'])); $router->post('/logout', csrf_handler([$auth, 'logout']));
$router->get('/account', [$account, 'index']); $router->get('/account/orders', [$account, 'orders']); $router->get('/checkout', [$checkout, 'form']); $router->post('/checkout', csrf_handler([$checkout, 'store']));
$router->get('/admin', [$admin, 'dashboard']); $router->get('/admin/products', [$admin, 'products']); $router->get('/admin/products/edit/{id}', [$admin, 'editProduct']); $router->post('/admin/products/save', csrf_handler([$admin, 'saveProduct'])); $router->post('/admin/products/delete/{id}', csrf_handler([$admin, 'deleteProduct'])); $router->get('/admin/categories', [$admin, 'categories']); $router->get('/admin/categories/edit/{id}', [$admin, 'editCategory']); $router->post('/admin/categories/save', csrf_handler([$admin, 'saveCategory'])); $router->post('/admin/categories/delete/{id}', csrf_handler([$admin, 'deleteCategory'])); $router->get('/admin/orders', [$admin, 'orders']); $router->post('/admin/orders/{id}', csrf_handler([$admin, 'updateOrder'])); $router->get('/admin/users', [$admin, 'users']); $router->post('/admin/users/{id}', csrf_handler([$admin, 'updateUser']));
try { $router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']); } catch (Throwable $exception) { $logger->error('Unhandled application error', ['exception' => $exception->getMessage()]); http_response_code(500); echo $config['debug'] ? e($exception->getMessage()) : 'Wystąpił błąd aplikacji.'; }
