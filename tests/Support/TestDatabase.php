<?php

declare(strict_types=1);

namespace ShopStack\Tests\Support;

use PDO;
use RuntimeException;

final class TestDatabase
{
    private static ?PDO $connection = null;

    public static function connection(): PDO
    {
        if (self::$connection instanceof PDO) return self::$connection;
        $database = self::databaseName();
        if ($database === 'shopstack' || !str_ends_with($database, '_test')) {
            throw new RuntimeException('Integration tests require a database name ending in _test.');
        }
        $server = new PDO(sprintf('mysql:host=%s;port=%s;charset=utf8mb4', self::host(), self::port()), self::username(), self::password(), [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        $server->exec('DROP DATABASE IF EXISTS `' . $database . '`');
        $server->exec('CREATE DATABASE `' . $database . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        $pdo = new PDO(self::dsn(), self::username(), self::password(), [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        self::prepareSchema($pdo, $database);
        self::$connection = $pdo;
        return self::$connection;
    }

    public static function reset(PDO $pdo): void
    {
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
        foreach (['order_items', 'orders', 'products', 'categories', 'users'] as $table) {
            $pdo->exec('TRUNCATE TABLE ' . $table);
        }
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
        $pdo->exec("INSERT INTO users (id, name, email, password, role) VALUES (1, 'Test User', 'test@example.test', '" . password_hash('password', PASSWORD_DEFAULT) . "', 'user'), (2, 'Test Admin', 'admin@example.test', '" . password_hash('password', PASSWORD_DEFAULT) . "', 'admin')");
        $pdo->exec("INSERT INTO categories (id, name, slug, description) VALUES (1, 'Office', 'office', 'Office products'), (2, 'Tech', 'tech', 'Technology products')");
        $pdo->exec("INSERT INTO products (id, category_id, name, slug, description, price, stock) VALUES (1, 1, 'Notebook', 'notebook', 'A test notebook', 10.00, 5), (2, 2, 'Lamp', 'lamp', 'A test lamp', 25.50, 2), (3, 1, 'Pen', 'pen', 'A test pen', 3.25, 0)");
    }

    private static function prepareSchema(PDO $pdo, string $database): void
    {
        static $prepared = false;
        if ($prepared) return;
        $schema = file_get_contents(dirname(__DIR__, 2) . '/database/schema.sql');
        if ($schema === false) throw new RuntimeException('Cannot read database schema.');
        $schema = preg_replace('/\bshopstack\b/i', $database, $schema);
        $pdo->exec($schema);
        $prepared = true;
    }

    private static function databaseName(): string { return getenv('TEST_DB_DATABASE') ?: 'shopstack_test'; }
    private static function host(): string { return getenv('TEST_DB_HOST') ?: '127.0.0.1'; }
    private static function port(): string { return getenv('TEST_DB_PORT') ?: '3306'; }
    private static function username(): string { return getenv('TEST_DB_USERNAME') ?: 'root'; }
    private static function password(): string { return getenv('TEST_DB_PASSWORD') ?: ''; }
    private static function dsn(): string { return sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', self::host(), self::port(), self::databaseName()); }
}
