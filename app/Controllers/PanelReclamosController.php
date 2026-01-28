<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\DB;
use App\Middlewares\AuthMiddleware;
use App\Services\Auth;
use App\Services\ACL;

final class PanelReclamosController extends Controller
{
  private function guard(string $perm): void
  {
    (new \App\Middlewares\AuthMiddleware())->handle($this->request, $this->response);

    if (($this->request->tenant['mode'] ?? '') !== 'panel') {
      http_response_code(400);
      echo "Panel inválido";
      exit;
    }

    $user = \App\Services\Auth::user();
    $empresaId = (int)$this->request->tenant['empresa_id'];

    if (!$user || $empresaId <= 0) {
      http_response_code(403);
      echo "Forbidden";
      exit;
    }

    // ACL por empresa (nivel empresa)
    if (!\App\Services\ACL::can((int)$user['id'], $perm, $empresaId, null)) {
      http_response_code(403);
      echo "Sin permiso";
      exit;
    }
  }


  public function index(): void
  {
    $this->guard('reclamos.ver');

    $empresaId = (int)$this->request->tenant['empresa_id'];

    $estado = $this->request->input('estado', '');
    $params = ['eid' => $empresaId];
    $where = " WHERE r.empresa_id = :eid ";
    $tipo  = $this->request->input('tipo', '');
    $desde = $this->request->input('desde', date('Y-m-01'));
    $hasta = $this->request->input('hasta', date('Y-m-d'));


    if (in_array($estado, ['REGISTRADO', 'EN_PROCESO', 'RESPONDIDO', 'CERRADO'], true)) {
      $where .= " AND r.estado = :estado ";
      $params['estado'] = $estado;
    }
    if (in_array($tipo, ['RECLAMO', 'QUEJA'], true)) {
      $where .= " AND r.tipo = :tipo ";
      $params['tipo'] = $tipo;
    }

    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', (string)$desde) && preg_match('/^\d{4}-\d{2}-\d{2}$/', (string)$hasta)) {
      $where .= " AND DATE(r.fecha_registro) BETWEEN :desde AND :hasta ";
      $params['desde'] = $desde;
      $params['hasta'] = $hasta;
    }


    $sql = "SELECT r.id, r.codigo_reclamo, r.tipo, r.estado, r.fecha_registro, r.fecha_vencimiento_respuesta,
                   e.nombre AS establecimiento
            FROM reclamos r
            JOIN establecimientos e ON e.id = r.establecimiento_id
            $where
            ORDER BY r.fecha_registro DESC
            LIMIT 100";

    $st = DB::pdo()->prepare($sql);
    $st->execute($params);
    $reclamos = $st->fetchAll() ?: [];

    $this->view('panel.reclamos_list', [
      'tenant' => $this->request->tenant,
      'reclamos' => $reclamos,
      'estado' => $estado,
      'tipo' => $tipo,
      'desde' => $desde,
      'hasta' => $hasta,

    ], 'panel');
  }

  public function show(): void
  {
    $this->guard('reclamos.ver');

    $empresaId = (int)$this->request->tenant['empresa_id'];
    $id = (int)($this->request->params['id'] ?? 0);

    $sql = "SELECT r.*, e.nombre AS establecimiento
            FROM reclamos r
            JOIN establecimientos e ON e.id = r.establecimiento_id
            WHERE r.empresa_id = :eid AND r.id = :id
            LIMIT 1";
    $st = DB::pdo()->prepare($sql);
    $st->execute(['eid' => $empresaId, 'id' => $id]);
    $reclamo = $st->fetch();

    if (!$reclamo) {
      http_response_code(404);
      echo "No encontrado";
      return;
    }

    // Respuestas
    $st = DB::pdo()->prepare("SELECT rr.id, rr.respuesta, rr.fecha_respuesta, u.nombres, u.apellidos
                          FROM reclamo_respuestas rr
                          JOIN usuarios u ON u.id = rr.respondido_por_usuario_id
                          WHERE rr.reclamo_id = :rid
                          ORDER BY rr.fecha_respuesta ASC");
    $st->execute(['rid' => $id]);
    $respuestas = $st->fetchAll() ?: [];

    // Eventos
    $st = DB::pdo()->prepare("SELECT evento, created_at
                          FROM reclamo_eventos
                          WHERE reclamo_id = :rid
                          ORDER BY created_at ASC");
    $st->execute(['rid' => $id]);
    $eventos = $st->fetchAll() ?: [];


    $this->view('panel.reclamo_show', [
      'tenant' => $this->request->tenant,
      'reclamo' => $reclamo,
      'respuestas' => $respuestas,
      'eventos' => $eventos,
    ], 'panel');
  }

  public function responder(): void
  {
    $this->guard('reclamos.responder');

    $user = Auth::user();
    $empresaId = (int)$this->request->tenant['empresa_id'];
    $id = (int)($this->request->params['id'] ?? 0);
    $respuesta = trim((string)$this->request->input('respuesta'));

    if ($respuesta === '') {
      http_response_code(422);
      echo "Respuesta requerida";
      return;
    }

    $pdo = DB::pdo();
    $pdo->beginTransaction();

    // Validar que sea de la empresa
    $st = $pdo->prepare("SELECT id FROM reclamos WHERE id=:id AND empresa_id=:eid LIMIT 1 FOR UPDATE");
    $st->execute(['id' => $id, 'eid' => $empresaId]);
    $row = $st->fetch();
    if (!$row) {
      $pdo->rollBack();
      http_response_code(404);
      echo "No encontrado";
      return;
    }

    $pdo->prepare("INSERT INTO reclamo_respuestas (reclamo_id, respondido_por_usuario_id, respuesta)
                   VALUES (:rid, :uid, :resp)")
      ->execute(['rid' => $id, 'uid' => (int)$user['id'], 'resp' => $respuesta]);

    $pdo->prepare("UPDATE reclamos SET estado='RESPONDIDO', updated_at=NOW() WHERE id=:id")
      ->execute(['id' => $id]);

    $pdo->prepare("INSERT INTO reclamo_eventos (reclamo_id, actor_usuario_id, evento, metadata_json)
                   VALUES (:rid, :uid, 'RESPONDIDO', JSON_OBJECT('by','panel'))")
      ->execute(['rid' => $id, 'uid' => (int)$user['id']]);

    $pdo->commit();
    $panel = (string)($this->request->tenant['panel_prefix'] ?? '/panel');
    $this->response->redirect(rtrim($panel, '/') . "/reclamos/{$id}");
  }
}
