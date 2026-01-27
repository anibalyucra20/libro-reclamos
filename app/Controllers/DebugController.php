<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

final class DebugController extends Controller
{
  public function index(): void
  {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
      'host' => $_SERVER['HTTP_HOST'] ?? null,
      'path' => $_SERVER['REQUEST_URI'] ?? null,
      'tenant' => $this->request->tenant,
      'params' => $this->request->params ?? [],
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
  }
}
