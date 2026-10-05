<?php

declare(strict_types=1);

header('Content-Type: text/plain; version=0.0.4');

$config = require dirname(__DIR__) . '/config/config.php';
require dirname(__DIR__) . '/src/Helpers/functions.php';

foreach (glob(dirname(__DIR__) . '/src/Core/*.php') as $file) {
    require_once $file;
}

$logger = new \ShopStack\Core\Logger(
    dirname(__DIR__) . '/storage/logs/app.log'
);

$databaseStatus = 0;
$productsTotal = 0;
$ordersTotal = 0;

try {
    $database = new \ShopStack\Core\Database(
        $config['database'],
        $logger
    );

    $pdo = $database->pdo();

    $databaseStatus = 1;

    $productsTotal = (int) $pdo
        ->query('SELECT COUNT(*) FROM products')
        ->fetchColumn();

    $ordersTotal = (int) $pdo
        ->query('SELECT COUNT(*) FROM orders')
        ->fetchColumn();

} catch (\Throwable $exception) {
    $logger->error('Metrics collection failed', [
        'exception' => $exception->getMessage(),
    ]);
}

echo "# HELP shop_database_status Database connection status.\n";
echo "# TYPE shop_database_status gauge\n";
echo "shop_database_status {$databaseStatus}\n";

echo "# HELP shop_products_total Total number of products.\n";
echo "# TYPE shop_products_total gauge\n";
echo "shop_products_total {$productsTotal}\n";

echo "# HELP shop_orders_total Total number of orders.\n";
echo "# TYPE shop_orders_total gauge\n";
echo "shop_orders_total {$ordersTotal}\n";