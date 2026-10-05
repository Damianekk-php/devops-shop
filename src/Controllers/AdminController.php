<?php

declare(strict_types=1);

namespace ShopStack\Controllers;

use ShopStack\Core\Logger;
use ShopStack\Core\View;
use ShopStack\Models\Category;
use ShopStack\Models\Order;
use ShopStack\Models\Product;
use ShopStack\Models\User;

final class AdminController
{
    private const STATUSES = ['pending', 'paid', 'processing', 'shipped', 'completed', 'cancelled'];
    public function __construct(private readonly View $view, private readonly Product $products, private readonly Category $categories, private readonly Order $orders, private readonly User $users, private readonly Logger $logger) {}
    public function dashboard(): void { require_admin(); $this->view->render('admin/dashboard', ['userCount' => count($this->users->all()), 'productCount' => $this->products->count(), 'orderCount' => $this->orders->count(), 'pendingCount' => $this->orders->pendingCount(), 'revenue' => $this->orders->revenue(), 'lowStock' => $this->products->lowStock()]); }
    public function products(): void { require_admin(); $this->view->render('admin/products', ['products' => $this->products->all(), 'categories' => $this->categories->all()]); }
    public function editProduct(string $id): void { require_admin(); $product = $this->products->find((int) $id); if (!$product) { http_response_code(404); exit('Produkt nie istnieje.'); } $this->view->render('admin/product-edit', ['product' => $product, 'categories' => $this->categories->all()]); }
    public function saveProduct(): never { require_admin(); $data = ['category_id' => (int) $_POST['category_id'], 'name' => trim((string) $_POST['name']), 'slug' => trim((string) $_POST['slug']), 'description' => trim((string) $_POST['description']), 'price' => max(0, (float) $_POST['price']), 'stock' => max(0, (int) $_POST['stock']), 'image' => trim((string) ($_POST['image'] ?? ''))]; if (!empty($_FILES['image_upload']['tmp_name'])) { $data['image'] = $this->storeImage($_FILES['image_upload']); } $this->products->save($data, !empty($_POST['id']) ? (int) $_POST['id'] : null); flash('success', 'Produkt zapisany.'); redirect('admin/products'); }
    public function deleteProduct(string $id): never { require_admin(); $this->products->delete((int) $id); flash('success', 'Produkt usunięty.'); redirect('admin/products'); }
    public function categories(): void { require_admin(); $this->view->render('admin/categories', ['categories' => $this->categories->all()]); }
    public function editCategory(string $id): void { require_admin(); $category = $this->categories->find((int) $id); if (!$category) { http_response_code(404); exit('Kategoria nie istnieje.'); } $this->view->render('admin/category-edit', ['category' => $category]); }
    public function saveCategory(): never { require_admin(); $data = ['name' => trim((string) $_POST['name']), 'slug' => trim((string) $_POST['slug']), 'description' => trim((string) $_POST['description'])]; $this->categories->save($data, !empty($_POST['id']) ? (int) $_POST['id'] : null); redirect('admin/categories'); }
    public function deleteCategory(string $id): never { require_admin(); try { $this->categories->delete((int) $id); flash('success', 'Kategoria usunięta.'); } catch (\Throwable $e) { flash('error', 'Nie można usunąć kategorii używanej przez produkty.'); } redirect('admin/categories'); }
    public function orders(): void { require_admin(); $this->view->render('admin/orders', ['orders' => $this->orders->all(), 'statuses' => self::STATUSES]); }
    public function updateOrder(string $id): never { require_admin(); $status = (string) ($_POST['status'] ?? ''); if (in_array($status, self::STATUSES, true)) { $this->orders->updateStatus((int) $id, $status); $this->logger->info('Admin changed order status', ['admin_id' => current_user()['id'], 'order_id' => $id, 'status' => $status]); } redirect('admin/orders'); }
    public function users(): void { require_admin(); $this->view->render('admin/users', ['users' => $this->users->all()]); }
    public function updateUser(string $id): never { require_admin(); $role = (string) ($_POST['role'] ?? 'user'); if (in_array($role, ['user', 'admin'], true)) $this->users->updateRole((int) $id, $role); redirect('admin/users'); }
    private function storeImage(array $upload): string { if (($upload['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK || (int) ($upload['size'] ?? 0) > 2 * 1024 * 1024) throw new \InvalidArgumentException('Zdjęcie jest za duże lub niepoprawne.'); $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp']; $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($upload['tmp_name']); if (!isset($allowed[$mime]) || @getimagesize($upload['tmp_name']) === false) throw new \InvalidArgumentException('Dozwolone są tylko obrazy JPG, PNG i WebP.'); $name = bin2hex(random_bytes(16)) . '.' . $allowed[$mime]; $directory = dirname(__DIR__, 2) . '/public/uploads'; if (!is_dir($directory)) mkdir($directory, 0755, true); if (!move_uploaded_file($upload['tmp_name'], $directory . '/' . $name)) throw new \RuntimeException('Nie udało się zapisać zdjęcia.'); return $name; }
}
