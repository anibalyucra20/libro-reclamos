<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Services\Auth;
use App\Core\DB;
use App\Services\ExportReclamosService;

final class ExportReclamosController extends Controller
{
  private function guard(string $perm): void
  {
    (new \App\Middlewares\AuthMiddleware())->handle($this->request, $this->response);

    if (($this->request->tenant['mode'] ?? '') !== 'panel') {
      http_response_code(400);
      $_SESSION['flash_error'] = "Panel inválido";
      header("Location: /");
      exit;
    }

    $user = \App\Services\Auth::user();
    $empresaId = (int)$this->request->tenant['empresa_id'];

    if (!$user || $empresaId <= 0) {
      http_response_code(403);
      $_SESSION['flash_error'] = "Forbidden";
      header("Location: /");
      exit;
    }

    if (!\App\Services\ACL::can((int)$user['id'], $perm, $empresaId, null)) {
      http_response_code(403);
      $_SESSION['flash_error'] = "Sin permiso";
      header("Location: /");
      exit;
    }
  }

  public function xlsx(): void
  {
    $this->guard('reclamos.exportar');

    $empresaId = (int)$this->request->tenant['empresa_id'];
    $user = \App\Services\Auth::user();

    $estado = (string)$this->request->input('estado', '');
    $tipo   = (string)$this->request->input('tipo', '');
    $desde  = (string)$this->request->input('desde', date('Y-m-01'));
    $hasta  = (string)$this->request->input('hasta', date('Y-m-d'));

    $filters = ['estado' => $estado, 'tipo' => $tipo, 'desde' => $desde, 'hasta' => $hasta];

    $svc = new \App\Services\ExportReclamosService();
    $slug = $this->request->tenant['empresa_slug'] ?? 'empresa';
    $filename = "reclamos_{$slug}_" . date('Ymd_His') . ".xlsx";

    // headers
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');

    $svc->outputXlsx($empresaId, $filters, (int)$user['id']);
  }
}
