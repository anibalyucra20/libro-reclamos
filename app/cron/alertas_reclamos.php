<?php
declare(strict_types=1);

use Dotenv\Dotenv;
use App\Services\AlertasReclamosService;

// Autoload
require __DIR__ . '/../vendor/autoload.php';

// Cargar .env
$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

// Inicializar DB (usa TU clase real)
require_once __DIR__ . '/../app/Core/DB.php';
// o ../app/DB.php según tu estructura real

// Ejecutar servicio
$svc = new AlertasReclamosService();

try {
    $result = $svc->run();
    echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . PHP_EOL;
    exit(0);
} catch (Throwable $e) {
    error_log('[CRON alertas] ' . $e->getMessage());
    exit(1);
}
