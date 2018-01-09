<?php

require_once __DIR__ . '/autoload.php';

// load .env
if (!class_exists(\Symfony\Component\Dotenv\Dotenv::class)) {
    throw new \RuntimeException('Dotenv class does not exist. You need to define environment variables for configuration or add "symfony/dotenv" as a Composer dependency to load variables from a .env file.');
}
(new \Symfony\Component\Dotenv\Dotenv())->load(__DIR__ . '/../.env.test');