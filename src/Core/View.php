<?php

declare(strict_types=1);

namespace ShopStack\Core;

final class View
{
    public function __construct(private readonly string $templatesPath, private readonly array $config)
    {
    }

    public function render(string $template, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        $config = $this->config;
        require $this->templatesPath . '/' . $template . '.php';
    }

    public function redirect(string $path): never
    {
        header('Location: ' . $this->config['url'] . '/' . ltrim($path, '/'));
        exit;
    }
}
