<?php

declare(strict_types=1);

namespace ShopStack\Tests\Unit;

use PHPUnit\Framework\TestCase;
use ShopStack\Core\Router;

final class RouterTest extends TestCase
{
    public function testDispatchesStaticAndParameterizedRoutes(): void
    {
        $router = new Router();
        $router->get('/products/{id}', static fn (string $id): string => 'product:' . $id);
        $router->get('/login', static fn (): string => 'login');

        self::assertSame('product:12', $router->dispatch('GET', '/sklepik/public/products/12'));
        self::assertSame('login', $router->dispatch('GET', '/sklepik/public/login'));
    }

    public function testDoesNotDispatchWrongHttpMethod(): void
    {
        $router = new Router();
        $router->post('/login', static fn (): string => 'posted');

        self::assertSame('Not found', $router->dispatch('GET', '/sklepik/public/login'));
        self::assertSame(404, http_response_code());
    }
}
