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

      'consumidor_tipo' => strtoupper((string)$this->request->input('consumidor_tipo', 'NATURAL')), // NATURAL|JURIDICA
      'consumidor_menor' => (string)$this->request->input('consumidor_menor', '0'),               // 1/0

      'tutor_nombres' => (string)$this->request->input('tutor_nombres', ''),
      'tutor_doc_tipo' => strtoupper((string)$this->request->input('tutor_doc_tipo', 'DNI')),
      'tutor_doc_num' => (string)$this->request->input('tutor_doc_num', ''),

      'contacto_nombres' => (string)$this->request->input('contacto_nombres', ''),
      'contacto_doc_tipo' => strtoupper((string)$this->request->input('contacto_doc_tipo', 'DNI')),
      'contacto_doc_num' => (string)$this->request->input('contacto_doc_num', ''),

      'bien_tipo' => strtoupper((string)$this->request->input('bien_tipo', '')), // PRODUCTO|SERVICIO
      'bien_doc_tipo' => (string)$this->request->input('bien_doc_tipo', ''),     // "Sin comprobante", "Boleta", etc (texto)
      'bien_doc_num' => (string)$this->request->input('bien_doc_num', ''),

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


    // ====== Validación adicional: tipo consumidor / menor / jurídica ======
    $consumidorTipo = $old['consumidor_tipo'];
    if (!in_array($consumidorTipo, ['NATURAL', 'JURIDICA'], true)) {
      $errors['consumidor_tipo'] = 'Tipo de consumidor inválido.';
      $consumidorTipo = 'NATURAL';
    }

    $esMenor = ((int)$old['consumidor_menor'] === 1);

    // Si es jurídica: doc debe ser RUC y apellidos puede ir vacío (razón social en nombres)
    if ($consumidorTipo === 'JURIDICA') {
      if ($docTipo !== 'RUC') {
        $errors['consumidor_doc_tipo'] = 'Para persona jurídica el documento debe ser RUC.';
      }
      if (mb_strlen($docNum) !== 11 || !ctype_digit($docNum)) {
        $errors['consumidor_doc_num'] = 'RUC inválido (11 dígitos).';
      }

      // Contacto obligatorio (mínimo nombres + doc num)
      $contactoNom = trim((string)$old['contacto_nombres']);
      $contactoDocTipo = $old['contacto_doc_tipo'];
      $contactoDocNum = trim((string)$old['contacto_doc_num']);

      if ($contactoNom === '') $errors['contacto_nombres'] = 'Ingresa nombres del contacto.';
      if (!in_array($contactoDocTipo, ['DNI', 'CE', 'PAS', 'RUC', 'OTRO'], true)) {
        $errors['contacto_doc_tipo'] = 'Tipo de documento del contacto inválido.';
      }
      if ($contactoDocNum === '') $errors['contacto_doc_num'] = 'Ingresa documento del contacto.';
    }

    // Si es natural y menor: tutor obligatorio
    if ($consumidorTipo === 'NATURAL' && $esMenor) {
      $tutorNom = trim((string)$old['tutor_nombres']);
      $tutorDocTipo = $old['tutor_doc_tipo'];
      $tutorDocNum = trim((string)$old['tutor_doc_num']);

      if ($tutorNom === '') $errors['tutor_nombres'] = 'Ingresa nombres del padre/madre/tutor.';
      if (!in_array($tutorDocTipo, ['DNI', 'CE', 'PAS', 'RUC', 'OTRO'], true)) {
        $errors['tutor_doc_tipo'] = 'Tipo de documento del tutor inválido.';
      }
      if ($tutorDocNum === '') $errors['tutor_doc_num'] = 'Ingresa documento del tutor.';
    }

    // ====== Validación adicional: bien tipo (producto/servicio) ======
    $bienTipo = $old['bien_tipo'];
    if (!in_array($bienTipo, ['PRODUCTO', 'SERVICIO'], true)) {
      $errors['bien_tipo'] = 'Selecciona si es Producto o Servicio.';
    }
    // Evidencia opcional (si viene)
    if (!empty($_FILES['evidencia']) && ($_FILES['evidencia']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
      $f = $_FILES['evidencia'];
      if ((int)$f['error'] !== UPLOAD_ERR_OK) {
        $errors['evidencia'] = 'Error al subir el archivo.';
      } else {
        $max = 10 * 1024 * 1024;
        if ((int)$f['size'] > $max) {
          $errors['evidencia'] = 'El archivo excede 10 MB.';
        }
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

      'consumidor_tipo' => $consumidorTipo,
      'consumidor_menor' => $esMenor ? 1 : 0,

      'tutor_nombres' => trim((string)$old['tutor_nombres']),
      'tutor_doc_tipo' => $old['tutor_doc_tipo'],
      'tutor_doc_num' => trim((string)$old['tutor_doc_num']),

      'contacto_nombres' => trim((string)$old['contacto_nombres']),
      'contacto_doc_tipo' => $old['contacto_doc_tipo'],
      'contacto_doc_num' => trim((string)$old['contacto_doc_num']),

      'bien_tipo' => $bienTipo,
      'bien_doc_tipo' => trim((string)$old['bien_doc_tipo']),
      'bien_doc_num' => trim((string)$old['bien_doc_num']),


      'acepta_declaracion' => 1,
      'created_ip' => $_SERVER['REMOTE_ADDR'] ?? null,
      'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
    ]);


    // ====== Evidencia (archivo opcional) ======
    try {
      if (!empty($_FILES['evidencia']) && isset($_FILES['evidencia']['error']) && $_FILES['evidencia']['error'] !== UPLOAD_ERR_NO_FILE) {

        $f = $_FILES['evidencia'];

        if ((int)$f['error'] !== UPLOAD_ERR_OK) {
          throw new \RuntimeException('No se pudo subir el archivo (error ' . (int)$f['error'] . ').');
        }

        $max = 10 * 1024 * 1024; // 10MB
        if ((int)$f['size'] > $max) {
          throw new \RuntimeException('El archivo excede 10 MB.');
        }

        $tmp = (string)$f['tmp_name'];
        if ($tmp === '' || !is_uploaded_file($tmp)) {
          throw new \RuntimeException('Archivo temporal inválido.');
        }

        $original = (string)($f['name'] ?? 'archivo');
        $originalBase = preg_replace('/[^\pL\pN\.\-\_\s]/u', '', $original);
        $originalBase = trim((string)$originalBase);
        if ($originalBase === '') $originalBase = 'archivo';

        // Detectar MIME real (más confiable)
        $mime = 'application/octet-stream';
        if (class_exists(\finfo::class)) {
          $fi = new \finfo(FILEINFO_MIME_TYPE);
          $det = $fi->file($tmp);
          if (is_string($det) && $det !== '') $mime = $det;
        }

        // Carpeta por empresa
        $empresaId = (int)$this->request->tenant['empresa_id'];
        $baseDir = dirname(__DIR__, 2) . '/storage/evidencias'; // ajusta si tu storage está en otra ruta
        $empresaDir = $baseDir . '/' . $empresaId;

        if (!is_dir($empresaDir)) {
          if (!@mkdir($empresaDir, 0775, true) && !is_dir($empresaDir)) {
            throw new \RuntimeException('No se pudo crear directorio de evidencias.');
          }
        }

        // Nombre basado en número de registro + fecha/hora (y un random corto para evitar colisiones)
        $codigo = (string)$result['codigo_reclamo']; // ej 2026-000011
        $stamp  = date('Ymd_His');
        $ext = strtolower(pathinfo($originalBase, PATHINFO_EXTENSION));
        $ext = preg_replace('/[^a-z0-9]/', '', $ext);

        // si no hay extensión, intenta inferir por mime (muy básico)
        if ($ext === '') {
          $map = [
            'application/pdf' => 'pdf',
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
          ];
          $ext = $map[$mime] ?? 'bin';
        }

        $rand = substr(bin2hex(random_bytes(4)), 0, 8);
        $fileName = $codigo . '_' . $stamp . '_' . $rand . '.' . $ext;

        $destAbs = $empresaDir . '/' . $fileName;

        if (!@move_uploaded_file($tmp, $destAbs)) {
          throw new \RuntimeException('No se pudo guardar el archivo subido.');
        }

        // Ruta relativa (lo que guardas en BD)
        $relPath = 'storage/evidencias/' . $empresaId . '/' . $fileName;

        Reclamo::attachEvidencia((int)$result['id'], $empresaId, [
          'path' => $relPath,
          'mime' => $mime,
          'size' => (int)$f['size'],
          'original' => $original,
          'uploaded_at' => date('Y-m-d H:i:s'),
        ]);
      }
    } catch (\Throwable $e) {
      // Si falla la evidencia, no tumba el reclamo (pero lo registras)
      \App\Services\Logger::error('EVIDENCIA_FAIL', [
        'reclamo_id' => $result['id'] ?? null,
        'codigo' => $result['codigo_reclamo'] ?? null,
        'err' => $e->getMessage(),
      ]);
    }




    if ($email !== '') {
      try {
        $reclamoFull = \App\Models\Reclamo::findByTokenInEmpresa($result['evidencia_token'], $empresaId);

        $html = \App\Core\View::renderToString('public.hoja_oficial_pdf', [
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
      'token_reclamo' => $token,
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
        'csrf' => \App\Services\Csrf::token(),
        'error' => 'Completa el código y documento.',
      ], 'public');
      return;
    }

    // Normaliza "2026-00001" -> "2026-000001"
    if (preg_match('/^(\d{4})-(\d{1,6})$/', $codigo, $m)) {
      $anio = $m[1];
      $n = (int)$m[2];
      $codigo = sprintf('%s-%06d', $anio, $n);
    }

    $token = \App\Models\Reclamo::tokenByCodigoAndDocInEmpresa($codigo, $docTipo, $docNum, $empresaId);

    if (!$token) {
      http_response_code(404);
      $this->view('public.seguimiento_form', [
        'tenant' => $this->request->tenant,
        'csrf' => \App\Services\Csrf::token(),
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

  public function hojaOficialPdf(): void
  {
    $this->assertTenant();

    $empresaId = (int)$this->request->tenant['empresa_id'];
    $token = (string)($this->request->params['token'] ?? '');
    if ($token === '') {
      http_response_code(404);
      echo "No encontrado";
      return;
    }

    // ideal: que findByToken traiga empresa + establecimiento (si ya lo hace ok; si no, lo ajustamos luego)
    $reclamo = Reclamo::findByTokenInEmpresa($token, $empresaId);
    if (!$reclamo) {
      http_response_code(404);
      echo "No encontrado";
      return;
    }

    // Traer última respuesta si existe (bloque 4)
    $respuestas = Reclamo::respuestasByReclamoId((int)$reclamo['id']);
    $ultimaRespuesta = $respuestas ? $respuestas[0] : null;

    $html = View::renderToString('public.hoja_oficial_pdf', [
      'tenant' => $this->request->tenant,
      'reclamo' => $reclamo,
      'respuesta' => $ultimaRespuesta,
    ]);

    $pdfBytes = Pdf::fromHtml($html);

    $filename = 'hoja_oficial_' . preg_replace('/[^A-Za-z0-9\-]/', '_', (string)$reclamo['codigo_reclamo']) . '.pdf';

    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . strlen($pdfBytes));
    echo $pdfBytes;
  }


  
}
