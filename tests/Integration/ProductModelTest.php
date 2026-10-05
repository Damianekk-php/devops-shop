<?php

declare(strict_types=1);

namespace ShopStack\Tests\Integration;

use ShopStack\Models\Product;
use ShopStack\Tests\Support\MySqlTestCase;

final class ProductModelTest extends MySqlTestCase
{
    public function testFindsProductAndReturnsNullForUnknownId(): void
    {
        $products = new Product($this->pdo);

        self::assertSame('Notebook', $products->find(1)['name']);
        self::assertNull($products->find(999));
    }

    public function testSearchCategoryAndSortAreApplied(): void
    {
        $products = new Product($this->pdo);

        self::assertCount(1, $products->all('lamp'));
        self::assertCount(2, $products->all(null, 1));
        $sorted = $products->all(null, null, 'price_desc');
        self::assertSame('Lamp', $sorted[0]['name']);
    }

    public function testSavesUpdatesAndDeletesProduct(): void
    {
        $products = new Product($this->pdo);
        $id = $products->save(['category_id' => 1, 'name' => 'Marker', 'slug' => 'marker', 'description' => 'Test marker', 'price' => 4.50, 'stock' => 7, 'image' => null]);
        self::assertSame('Marker', $products->find($id)['name']);
        $products->save(['category_id' => 1, 'name' => 'Updated Marker', 'slug' => 'marker', 'description' => 'Updated', 'price' => 5.00, 'stock' => 6, 'image' => null], $id);
        self::assertSame('Updated Marker', $products->find($id)['name']);
        $products->delete($id);
        self::assertNull($products->find($id));
    }
}
