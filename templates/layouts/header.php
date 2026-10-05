<!doctype html>
<html lang="pl">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title><?= e($title ?? 'ShopStack') ?></title><link rel="stylesheet" href="<?= e($config['url']) ?>/assets/css/app.css"></head>
<body>
<header class="topbar"><a class="brand" href="<?= e($config['url']) ?>/">Shop<span>Stack</span></a><nav><a href="<?= e($config['url']) ?>/products">Produkty</a><a href="<?= e($config['url']) ?>/cart">Koszyk</a><?php if (current_user()): ?><a href="<?= e($config['url']) ?>/account">Konto</a><?php if (current_user()['role'] === 'admin'): ?><a href="<?= e($config['url']) ?>/admin">Admin</a><?php endif; ?><form class="inline" method="post" action="<?= e($config['url']) ?>/logout"><input type="hidden" name="_csrf" value="<?= e(csrf_token()) ?>"><button>Wyloguj</button></form><?php else: ?><a href="<?= e($config['url']) ?>/login">Zaloguj</a><?php endif; ?></nav></header>
<main class="container"><?php if ($message = flash('success')): ?><div class="alert success"><?= e($message) ?></div><?php endif; ?><?php if ($message = flash('error')): ?><div class="alert error"><?= e($message) ?></div><?php endif; ?>
