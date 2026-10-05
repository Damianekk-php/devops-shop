<?php

declare(strict_types=1);

namespace ShopStack\Tests\Integration;

use ShopStack\Models\User;
use ShopStack\Tests\Support\MySqlTestCase;

final class UserModelTest extends MySqlTestCase
{
    public function testFindsUserAndPasswordIsStoredAsHash(): void
    {
        $user = (new User($this->pdo))->findByEmail('test@example.test');

        self::assertNotNull($user);
        self::assertSame('user', $user['role']);
        self::assertTrue(password_verify('password', $user['password']));
        self::assertNotSame('password', $user['password']);
    }

    public function testCreatesUserWithHashedPassword(): void
    {
        $id = (new User($this->pdo))->create('New User', 'new@example.test', 'secret-password');
        $user = (new User($this->pdo))->findByEmail('new@example.test');

        self::assertGreaterThan(0, $id);
        self::assertNotNull($user);
        self::assertTrue(password_verify('secret-password', $user['password']));
        self::assertSame('user', $user['role']);
    }
}
