<?php

declare(strict_types=1);

$config = require __DIR__ . '/config.php';

return new PDO(
    sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $config['database']['host'], $config['database']['port'], $config['database']['name']),
    $config['database']['user'],
    $config['database']['password'],
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ],
);
