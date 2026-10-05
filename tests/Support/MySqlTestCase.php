<?php

declare(strict_types=1);

namespace ShopStack\Tests\Support;

use PDO;
use PHPUnit\Framework\TestCase;

abstract class MySqlTestCase extends TestCase
{
    protected PDO $pdo;

    protected function setUp(): void
    {
        $this->pdo = TestDatabase::connection();
        TestDatabase::reset($this->pdo);
    }

    protected function tearDown(): void
    {
        if ($this->pdo->inTransaction()) $this->pdo->rollBack();
    }
}
