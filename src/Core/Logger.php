<?php

declare(strict_types=1);

namespace ShopStack\Core;

final class Logger
{
    public function __construct(private readonly string $file)
    {
        $directory = dirname($file);
        if (!is_dir($directory)) {
            mkdir($directory, 0775, true);
        }
    }

    public function info(string $message, array $context = []): void
    {
        $this->write('INFO', $message, $context);
    }

    public function warning(string $message, array $context = []): void
    {
        $this->write('WARNING', $message, $context);
    }

    public function error(string $message, array $context = []): void
    {
        $this->write('ERROR', $message, $context);
    }

    private function write(string $level, string $message, array $context): void
    {
        $safeContext = array_diff_key($context, array_flip(['password', 'password_confirmation', 'db_password']));
        $line = sprintf("[%s] %s %s%s", date('c'), $level, $message, $safeContext ? ' ' . json_encode($safeContext, JSON_UNESCAPED_SLASHES) : '');
        file_put_contents($this->file, $line . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
}
