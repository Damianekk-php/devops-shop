<?php

declare(strict_types=1);

namespace ShopStack\Core;

use PDO;

final class Database
{
    private ?PDO $connection = null;

    public function __construct(private readonly array $config, private readonly Logger $logger)
    {
    }

    public function pdo(): PDO
    {
        if ($this->connection === null) {
            try {
                $this->connection = new PDO(
                    sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $this->config['host'], $this->config['port'], $this->config['name']),
                    $this->config['user'],
                    $this->config['password'],
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES => false],
                );
            } catch (\Throwable $exception) {
                $this->logger->error('Database connection failed', ['exception' => $exception->getMessage()]);
                throw $exception;
            }
        }

        return $this->connection;
    }
}
