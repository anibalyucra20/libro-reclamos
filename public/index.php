<?php
declare(strict_types=1);
require dirname(__DIR__) . '/vendor/autoload.php';
use App\Core\App;

$dotenvPath = dirname(__DIR__);
if (file_exists($dotenvPath . '/.env')) {
  Dotenv\Dotenv::createImmutable($dotenvPath)->load();
}

$app = new App();
$app->run();
