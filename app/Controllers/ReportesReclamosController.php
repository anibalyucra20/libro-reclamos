<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Services\Auth;
use App\Services\ACL;
use App\Services\ReportesReclamosService;

final class ReportesReclamosController extends Controller
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

    $user = Auth::user();
    $empresaId = (int)($this->request->tenant['empresa_id'] ?? 0);

    if (!$user || $empresaId <= 0) {
      http_response_code(403);
      $_SESSION['flash_error'] = "Forbidden";
      header("Location: /");
      exit;
    }

    if (!ACL::can((int)$user['id'], $perm, $empresaId, null)) {
      http_response_code(403);
      $_SESSION['flash_error'] = "Sin permiso";
      header("Location: /");
      exit;
    }
  }

  public function index(): void
  {
    $this->guard('reclamos.reportes');

    $this->view('panel/reportes/index', [
      'tenant' => $this->request->tenant,
      'user' => Auth::user(),
      'desde' => $_GET['desde'] ?? date('Y-m-01'),
      'hasta' => $_GET['hasta'] ?? date('Y-m-d'),
      'estado' => $_GET['estado'] ?? '',
      'tipo' => $_GET['tipo'] ?? '',
    ], 'panel');
  }

  public function data(): void
  {
    $this->guard('reclamos.reportes');

    $empresaId = (int)($this->request->tenant['empresa_id'] ?? 0);

    $desde  = (string)($_GET['desde'] ?? date('Y-m-01'));
    $hasta  = (string)($_GET['hasta'] ?? date('Y-m-d'));
    $estado = (string)($_GET['estado'] ?? '');
    $tipo   = (string)($_GET['tipo'] ?? '');
    $establecimientoId = isset($_GET['establecimiento_id']) ? (int)$_GET['establecimiento_id'] : null;

    $svc = new ReportesReclamosService();
    $out = $svc->dashboard($empresaId, [
      'desde' => $desde,
      'hasta' => $hasta,
      'estado' => $estado,
      'tipo' => $tipo,
      'establecimiento_id' => $establecimientoId && $establecimientoId > 0 ? $establecimientoId : null,
    ]);

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($out, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  }
}
