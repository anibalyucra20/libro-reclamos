<?php
declare(strict_types=1);

use Dotenv\Dotenv;
use App\Services\AlertasReclamosService;

require __DIR__ . '/../../vendor/autoload.php';

// Carga .env (si en producción usas .env)
if (file_exists(__DIR__ . '/../../.env')) {
    $dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
    $dotenv->load();
}

// Carga config (si tu DB.php la requiere)
require __DIR__ . '/../Config/database.php';

$svc = new AlertasReclamosService();

try {
    $result = $svc->run();
    echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . PHP_EOL;
    exit(0);
} catch (Throwable $e) {
    error_log('[CRON alertas] ' . $e->getMessage());
    exit(1);
}
