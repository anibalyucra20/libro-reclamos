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
                   r.consumidor_nombres, r.consumidor_apellidos, r.consumidor_tipo,
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



  public function pdfOficial(): void
  {
    $this->guard('reclamos.ver');

    $empresaId = (int)$this->request->tenant['empresa_id'];
    $id = (int)($this->request->params['id'] ?? 0);
    if ($id <= 0) {
      http_response_code(404);
      echo "No encontrado";
      return;
    }

    // Traer reclamo completo con empresa+establecimiento (para el formato oficial)
    $sql = "SELECT
            r.id, r.codigo_reclamo, r.correlativo_num, r.anio,
            r.tipo, r.estado, r.fecha_registro, r.fecha_vencimiento_respuesta,

            r.consumidor_nombres, r.consumidor_apellidos, r.consumidor_doc_tipo, r.consumidor_doc_num,
            r.consumidor_email, r.consumidor_telefono, r.consumidor_direccion,

            r.consumidor_tipo, r.consumidor_menor,
            r.tutor_nombres, r.tutor_doc_tipo, r.tutor_doc_num,
            r.contacto_nombres, r.contacto_doc_tipo, r.contacto_doc_num,

            r.bien_tipo, r.bien_doc_tipo, r.bien_doc_num,
            r.bien_contratado, r.monto_reclamado, r.detalle, r.pedido,

            r.evidencia_path, r.evidencia_mime, r.evidencia_size, r.evidencia_original, r.evidencia_uploaded_at,

            e.nombre AS establecimiento_nombre,
            e.direccion AS establecimiento_direccion,

            em.razon_social AS empresa_razon_social,
            em.nombre_comercial AS empresa_nombre_comercial,
            em.ruc AS empresa_ruc
          FROM reclamos r
          JOIN establecimientos e ON e.id = r.establecimiento_id
          JOIN empresas em ON em.id = r.empresa_id
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

    // Última respuesta (si existe)
    $st = DB::pdo()->prepare("SELECT rr.respuesta, rr.fecha_respuesta, u.nombres, u.apellidos
                            FROM reclamo_respuestas rr
                            JOIN usuarios u ON u.id = rr.respondido_por_usuario_id
                            WHERE rr.reclamo_id = :rid
                            ORDER BY rr.fecha_respuesta DESC
                            LIMIT 1");
    $st->execute(['rid' => $id]);
    $ultimaRespuesta = $st->fetch() ?: null;

    $html = \App\Core\View::renderToString('public.hoja_oficial_pdf', [
      // OJO: reusamos tu vista oficial
      'tenant' => $this->request->tenant,
      'reclamo' => $reclamo,
      'respuesta' => $ultimaRespuesta,
    ]);

    $pdfBytes = \App\Services\Pdf::fromHtml($html);

    $filename = 'hoja_oficial_' . preg_replace('/[^A-Za-z0-9\-]/', '_', (string)$reclamo['codigo_reclamo']) . '.pdf';

    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . strlen($pdfBytes));
    echo $pdfBytes;
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

    try {
      // 1) Validar que sea de la empresa + LOCK
      $st = $pdo->prepare("
      SELECT id, evidencia_token
      FROM reclamos
      WHERE id=:id AND empresa_id=:eid
      LIMIT 1
      FOR UPDATE
    ");
      $st->execute(['id' => $id, 'eid' => $empresaId]);
      $row = $st->fetch();

      if (!$row) {
        $pdo->rollBack();
        http_response_code(404);
        echo "No encontrado";
        return;
      }

      // 2) Insert respuesta
      $pdo->prepare("
      INSERT INTO reclamo_respuestas (reclamo_id, respondido_por_usuario_id, respuesta)
      VALUES (:rid, :uid, :resp)
    ")->execute([
        'rid' => $id,
        'uid' => (int)$user['id'],
        'resp' => $respuesta
      ]);

      $respuestaId = (int)$pdo->lastInsertId();

      // 3) Update estado
      $pdo->prepare("UPDATE reclamos SET estado='RESPONDIDO', updated_at=NOW() WHERE id=:id")
        ->execute(['id' => $id]);

      // 4) Auditoría base
      $pdo->prepare("
      INSERT INTO reclamo_eventos (reclamo_id, actor_usuario_id, evento, metadata_json)
      VALUES (:rid, :uid, 'RESPONDIDO', JSON_OBJECT('by','panel'))
    ")->execute([
        'rid' => $id,
        'uid' => (int)$user['id']
      ]);

      $pdo->commit();
    } catch (\Throwable $e) {
      $pdo->rollBack();
      throw $e;
    }

    // ========= NOTIFICACIÓN POR EMAIL (fuera de la transacción) =========
    try {
      // Traer datos completos para el correo
      $st = DB::pdo()->prepare("
      SELECT
        r.id, r.codigo_reclamo, r.tipo, r.estado, r.fecha_registro, r.fecha_vencimiento_respuesta,
        r.consumidor_email, r.consumidor_nombres, r.consumidor_apellidos,
        r.evidencia_token,
        em.razon_social AS empresa_razon_social,
        em.nombre_comercial AS empresa_nombre_comercial,
        e.nombre AS establecimiento_nombre
      FROM reclamos r
      JOIN empresas em ON em.id = r.empresa_id
      JOIN establecimientos e ON e.id = r.establecimiento_id
      WHERE r.id = :id AND r.empresa_id = :eid
      LIMIT 1
    ");
      $st->execute(['id' => $id, 'eid' => $empresaId]);
      $rec = $st->fetch();

      $to = trim((string)($rec['consumidor_email'] ?? ''));
      if ($to !== '' && filter_var($to, FILTER_VALIDATE_EMAIL)) {

        $codigo = (string)($rec['codigo_reclamo'] ?? '');
        $token  = (string)($rec['evidencia_token'] ?? '');

        // URL base (misma lógica que ya usas)
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $base   = $scheme . '://' . $host;

        $urlSeguimiento = $base . "/seguimiento/" . rawurlencode($token);

        // ⚠️ AJUSTA ESTA RUTA SI LA TUYA ES DIFERENTE
        $urlPdfOficial  = $base . "/constancia/" . rawurlencode($token) . "/pdf";

        $empresaNombre = trim((string)($rec['empresa_nombre_comercial'] ?? ''));
        if ($empresaNombre === '') $empresaNombre = trim((string)($rec['empresa_razon_social'] ?? ''));

        $nombreCliente = trim((string)($rec['consumidor_nombres'] ?? '') . ' ' . (string)($rec['consumidor_apellidos'] ?? ''));

        $subject = "Respuesta a tu " . strtolower((string)($rec['tipo'] ?? 'reclamo')) . " - {$codigo}";

        $body = "
        <div style='font-family:Arial,sans-serif;font-size:14px;line-height:1.5;color:#222'>
          <p>Hola <strong>" . htmlspecialchars($nombreCliente ?: 'cliente', ENT_QUOTES, 'UTF-8') . "</strong>,</p>

          <p>Se registró una respuesta a tu solicitud en el <strong>Libro de Reclamaciones</strong> de
            <strong>" . htmlspecialchars($empresaNombre ?: 'la empresa', ENT_QUOTES, 'UTF-8') . "</strong>.
          </p>

          <p><strong>Código:</strong> " . htmlspecialchars($codigo, ENT_QUOTES, 'UTF-8') . "<br>
             <strong>Establecimiento:</strong> " . htmlspecialchars((string)($rec['establecimiento_nombre'] ?? ''), ENT_QUOTES, 'UTF-8') . "<br>
             <strong>Estado:</strong> " . htmlspecialchars((string)($rec['estado'] ?? ''), ENT_QUOTES, 'UTF-8') . "</p>

          <hr style='border:none;border-top:1px solid #ddd;margin:16px 0'>

          <p style='margin:0 0 6px'><strong>Respuesta:</strong></p>
          <div style='background:#f6f7f9;border:1px solid #e5e7eb;padding:12px;border-radius:8px;white-space:pre-wrap'>" .
          nl2br(htmlspecialchars($respuesta, ENT_QUOTES, 'UTF-8')) .
          "</div>

          <hr style='border:none;border-top:1px solid #ddd;margin:16px 0'>

          <p style='margin:0 0 10px'>Puedes hacer seguimiento aquí:</p>
          <p style='margin:0 0 12px'>
            <a href='" . htmlspecialchars($urlSeguimiento, ENT_QUOTES, 'UTF-8') . "' target='_blank' rel='noopener'>Ver seguimiento</a>
          </p>

          <p style='margin:0 0 10px'>Y descargar el <strong>PDF oficial</strong> aquí:</p>
          <p style='margin:0 0 12px'>
            <a href='" . htmlspecialchars($urlPdfOficial, ENT_QUOTES, 'UTF-8') . "' target='_blank' rel='noopener'>Descargar PDF oficial</a>
          </p>

          <p style='color:#6b7280;font-size:12px;margin-top:18px'>
            Este es un correo automático. Si no reconoces esta solicitud, ignora este mensaje.
          </p>
        </div>
      ";

        \App\Services\Mailer::sendHtml([$to], $subject, $body);

        // marcar notificado
        DB::pdo()->prepare("UPDATE reclamo_respuestas SET notificado_email_at = NOW() WHERE id = :rid")
          ->execute(['rid' => $respuestaId]);

        // auditoría OK
        DB::pdo()->prepare("
        INSERT INTO reclamo_eventos (reclamo_id, actor_usuario_id, evento, metadata_json)
        VALUES (:rid, :uid, 'RESPUESTA_EMAIL_OK', JSON_OBJECT('to', :to))
      ")->execute([
          'rid' => $id,
          'uid' => (int)$user['id'],
          'to'  => $to,
        ]);
      }
    } catch (\Throwable $e) {
      // auditoría FAIL (no rompemos el flujo)
      try {
        DB::pdo()->prepare("
        INSERT INTO reclamo_eventos (reclamo_id, actor_usuario_id, evento, metadata_json)
        VALUES (:rid, :uid, 'RESPUESTA_EMAIL_FAIL', JSON_OBJECT('err', :err))
      ")->execute([
          'rid' => $id,
          'uid' => (int)$user['id'],
          'err' => mb_substr($e->getMessage(), 0, 500),
        ]);
      } catch (\Throwable $ignored) {
      }

      \App\Services\Logger::error('RESPUESTA_EMAIL_FAIL', [
        'reclamo_id' => $id,
        'empresa_id' => $empresaId,
        'err' => $e->getMessage(),
      ]);
    }

    // redirect igual que antes
    $panel = (string)($this->request->tenant['panel_prefix'] ?? '/panel');
    $this->response->redirect(rtrim($panel, '/') . "/reclamos/{$id}");
  }
}
