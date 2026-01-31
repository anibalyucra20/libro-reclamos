<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Services\Csrf;
use App\Models\Establecimiento;
use App\Models\Reclamo;
use App\Services\Pdf;
use App\Core\View;

final class PublicLibroController extends Controller
{
  public function home(): void
  {
    $this->assertTenant();

    $this->view('public.home', [
      'tenant' => $this->request->tenant,
      'csrf' => Csrf::token(),
    ], 'public');
  }

  public function nuevo(): void
  {
    $this->assertTenant();

    $empresaId = (int)$this->request->tenant['empresa_id'];
    $establecimientos = Establecimiento::allByEmpresa($empresaId);

    $this->view('public.reclamo_nuevo', [
      'tenant' => $this->request->tenant,
      'establecimientos' => $establecimientos,
      'csrf' => Csrf::token(),
      'errors' => [],
      'old' => [],
    ], 'public');
  }

  public function crear(): void
  {
    $this->assertTenant();

    $empresaId = (int)$this->request->tenant['empresa_id'];
    $establecimientos = Establecimiento::allByEmpresa($empresaId);

    // ====== Captura de inputs (old) ======
    $old = [
      'tipo' => strtoupper((string)$this->request->input('tipo', 'RECLAMO')),
      'establecimiento_id' => (string)$this->request->input('establecimiento_id', ''),
      'consumidor_nombres' => (string)$this->request->input('consumidor_nombres', ''),
      'consumidor_apellidos' => (string)$this->request->input('consumidor_apellidos', ''),
      'consumidor_doc_tipo' => strtoupper((string)$this->request->input('consumidor_doc_tipo', 'DNI')),
      'consumidor_doc_num' => (string)$this->request->input('consumidor_doc_num', ''),
      'consumidor_email' => (string)$this->request->input('consumidor_email', ''),
      'consumidor_telefono' => (string)$this->request->input('consumidor_telefono', ''),
      'consumidor_direccion' => (string)$this->request->input('consumidor_direccion', ''),
      'bien_contratado' => (string)$this->request->input('bien_contratado', ''),
      'monto_reclamado' => (string)$this->request->input('monto_reclamado', ''),
      'detalle' => (string)$this->request->input('detalle', ''),
      'pedido' => (string)$this->request->input('pedido', ''),
      'acepta' => (string)$this->request->input('acepta', ''),
    ];

    // ====== Validación ======
    $errors = [];

    $tipo = $old['tipo'];
    if (!in_array($tipo, ['RECLAMO', 'QUEJA'], true)) {
      $errors['tipo'] = 'Tipo inválido.';
      $tipo = 'RECLAMO';
    }

    $establecimientoId = (int)$old['establecimiento_id'];
    if ($establecimientoId <= 0) {
      $errors['establecimiento_id'] = 'Selecciona un establecimiento.';
    }

    $consumidorNombres = trim($old['consumidor_nombres']);
    if ($consumidorNombres === '') {
      $errors['consumidor_nombres'] = 'Ingresa tus nombres.';
    }

    $docTipo = $old['consumidor_doc_tipo'];
    if (!in_array($docTipo, ['DNI', 'CE', 'PAS', 'RUC', 'OTRO'], true)) {
      $errors['consumidor_doc_tipo'] = 'Tipo de documento inválido.';
    }

    $docNum = trim($old['consumidor_doc_num']);
    if ($docNum === '') {
      $errors['consumidor_doc_num'] = 'Ingresa el número de documento.';
    } elseif (mb_strlen($docNum) > 20) {
      $errors['consumidor_doc_num'] = 'El número de documento es muy largo.';
    }

    $email = trim($old['consumidor_email']);
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $errors['consumidor_email'] = 'Email inválido.';
    }

    $bien = trim($old['bien_contratado']);
    if ($bien === '') {
      $errors['bien_contratado'] = 'Indica el bien contratado.';
    }

    $detalle = trim($old['detalle']);
    if ($detalle === '') {
      $errors['detalle'] = 'Escribe el detalle del reclamo/queja.';
    }

    $pedido = trim($old['pedido']);
    if ($pedido === '') {
      $errors['pedido'] = 'Escribe el pedido.';
    }

    $acepta = (int)$old['acepta'];
    if ($acepta !== 1) {
      $errors['acepta'] = 'Debes aceptar la declaración para continuar.';
    }

    // Validación monto (opcional)
    $montoRaw = trim($old['monto_reclamado']);
    $monto = null;
    if ($montoRaw !== '') {
      if (!is_numeric($montoRaw) || (float)$montoRaw < 0) {
        $errors['monto_reclamado'] = 'Monto reclamado inválido.';
      } else {
        $monto = (float)$montoRaw;
      }
    }

    // Verificar establecimiento dentro de empresa (si se eligió)
    $estab = null;
    if (!isset($errors['establecimiento_id'])) {
      $estab = Establecimiento::findInEmpresa($empresaId, $establecimientoId);
      if (!$estab) {
        $errors['establecimiento_id'] = 'Establecimiento inválido.';
      }
    }

    // Si hay errores, re-renderiza formulario con 422
    if ($errors) {
      http_response_code(422);
      $this->view('public.reclamo_nuevo', [
        'tenant' => $this->request->tenant,
        'establecimientos' => $establecimientos,
        'csrf' => Csrf::token(),
        'errors' => $errors,
        'old' => $old,
      ], 'public');
      return;
    }

    // ====== Persistencia ======
    $result = Reclamo::createPublic([
      'empresa_id' => $empresaId,
      'establecimiento_id' => $establecimientoId,
      'tipo' => $tipo,
      'consumidor_nombres' => $consumidorNombres,
      'consumidor_apellidos' => trim($old['consumidor_apellidos']),
      'consumidor_doc_tipo' => $docTipo,
      'consumidor_doc_num' => $docNum,
      'consumidor_email' => $email,
      'consumidor_telefono' => trim($old['consumidor_telefono']),
      'consumidor_direccion' => trim($old['consumidor_direccion']),
      'bien_contratado' => $bien,
      'monto_reclamado' => $monto,
      'detalle' => $detalle,
      'pedido' => $pedido,
      'acepta_declaracion' => 1,
      'created_ip' => $_SERVER['REMOTE_ADDR'] ?? null,
      'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
    ]);

    if ($email !== '') {
      try {
        $reclamoFull = \App\Models\Reclamo::findByTokenInEmpresa($result['evidencia_token'], $empresaId);

        $html = \App\Core\View::renderToString('public.constancia_pdf', [
          'tenant' => $this->request->tenant,
          'reclamo' => $reclamoFull,
          'token' => $result['evidencia_token'],
        ]);
        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
        $pdfBytes = \App\Services\Pdf::fromHtml($html);
        $pdfName = 'constancia_' . $result['codigo_reclamo'] . '.pdf';
        $body = "<p>Hola,</p>
             <p>Adjuntamos la constancia de tu reclamo: <strong>{$result['codigo_reclamo']}</strong> .</p>
             <p>También puedes hacer seguimiento haciendo click  <a href='" . $url . "/seguimiento/{$result['evidencia_token']}'>Aquí<a></p>";

        \App\Services\Mailer::sendWithPdf($email, 'Constancia - Libro de Reclamaciones', $body, $pdfBytes, $pdfName);
      } catch (\Throwable $e) {
        \App\Services\Logger::error('EMAIL_FAIL', [
          'to' => $email,
          'codigo' => $result['codigo_reclamo'] ?? null,
          'err' => $e->getMessage(),
        ]);
      }
    }

    // Confirmación con código
    $this->view('public.reclamo_ok', [
      'tenant' => $this->request->tenant,
      'codigo' => $result['codigo_reclamo'],
      'venc' => $result['fecha_vencimiento_respuesta'],
      'token' => $result['evidencia_token'],
      'establecimiento' => $estab,
    ], 'public');
  }

  private function assertTenant(): void
  {
    if (($this->request->tenant['mode'] ?? '') !== 'public') {
      http_response_code(400);
      echo "Acceso público requiere subdominio de empresa.";
      exit;
    }
  }


  public function constancia(): void
  {
    $this->assertTenant();

    $empresaId = (int)$this->request->tenant['empresa_id'];
    $token = (string)($this->request->params['token'] ?? '');

    if ($token === '') {
      http_response_code(404);
      echo "No encontrado";
      return;
    }

    $reclamo = Reclamo::findByTokenInEmpresa($token, $empresaId);
    if (!$reclamo) {
      http_response_code(404);
      echo "No encontrado";
      return;
    }

    $this->view('public.constancia', [
      'tenant' => $this->request->tenant,
      'reclamo' => $reclamo,
      'token' => $token,
    ], 'public');
  }

  public function seguimiento(): void
  {
    $this->assertTenant();

    $empresaId = (int)$this->request->tenant['empresa_id'];
    $token = (string)($this->request->params['token'] ?? '');

    if ($token === '') {
      http_response_code(404);
      echo "No encontrado";
      return;
    }

    $reclamo = Reclamo::findByTokenInEmpresa($token, $empresaId);
    if (!$reclamo) {
      http_response_code(404);
      echo "No encontrado";
      return;
    }

    $respuestas = Reclamo::respuestasByReclamoId((int)$reclamo['id']);
    $eventos = Reclamo::eventosByReclamoId((int)$reclamo['id']);

    $this->view('public.seguimiento', [
      'tenant' => $this->request->tenant,
      'reclamo' => $reclamo,
      'respuestas' => $respuestas,
      'eventos' => $eventos,
    ], 'public');
  }

  public function seguimientoForm(): void
  {
    $this->assertTenant();

    $this->view('public.seguimiento_form', [
      'tenant' => $this->request->tenant,
      'csrf' => Csrf::token(),
    ], 'public');
  }

  public function seguimientoBuscar(): void
  {
    \App\Services\RateLimit::check('seguimiento_buscar', $_SERVER['REMOTE_ADDR'] ?? null, 10, 300);
    $this->assertTenant();
    $empresaId = (int)$this->request->tenant['empresa_id'];
    $codigo = trim((string)$this->request->input('codigo_reclamo'));
    $docTipo = strtoupper(trim((string)$this->request->input('doc_tipo', 'DNI')));
    $docNum  = trim((string)$this->request->input('doc_num'));

    if ($codigo === '' || $docNum === '') {
      http_response_code(422);
      $this->view('public.seguimiento_form', [
        'tenant' => $this->request->tenant,
        'csrf' => Csrf::token(),
        'error' => 'Completa el código y documento.',
      ], 'public');
      return;
    }

    $token = \App\Models\Reclamo::tokenByCodigoAndDocInEmpresa($codigo, $docTipo, $docNum, $empresaId);
    if (!$token) {
      http_response_code(404);
      $this->view('public.seguimiento_form', [
        'tenant' => $this->request->tenant,
        'csrf' => Csrf::token(),
        'error' => 'No se encontró un reclamo con esos datos.',
      ], 'public');
      return;
    }
    $this->response->redirect('/seguimiento/' . $token);
  }


  public function constanciaPdf(): void
  {
    $this->assertTenant();

    $empresaId = (int)$this->request->tenant['empresa_id'];
    $token = (string)($this->request->params['token'] ?? '');
    if ($token === '') {
      http_response_code(404);
      echo "No encontrado";
      return;
    }

    $reclamo = Reclamo::findByTokenInEmpresa($token, $empresaId);
    if (!$reclamo) {
      http_response_code(404);
      echo "No encontrado";
      return;
    }

    $html = View::renderToString('public.constancia_pdf', [
      'tenant' => $this->request->tenant,
      'reclamo' => $reclamo,
      'token' => $token,
    ]);

    $pdfBytes = Pdf::fromHtml($html);

    $filename = 'constancia_' . preg_replace('/[^A-Za-z0-9\-]/', '_', (string)$reclamo['codigo_reclamo']) . '.pdf';

    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . strlen($pdfBytes));
    echo $pdfBytes;
  }
}
