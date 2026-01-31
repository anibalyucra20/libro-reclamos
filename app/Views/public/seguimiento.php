<?php
/**
 * Vista: public/seguimiento.php (o como la tengas nombrada)
 * Requiere:
 *  - $tenant
 *  - $reclamo
 *  - $respuestas (orden DESC en tu modelo)
 *  - $eventos
 */

$h = fn($v) => htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');

$empresaNombre = $tenant['razon_social'] ?? $tenant['nombre_comercial'] ?? 'Empresa';
$empresaRuc = $tenant['ruc'] ?? ($reclamo['empresa_ruc'] ?? '');
$empresaLinea = trim($empresaNombre . ($empresaRuc ? " RUC: {$empresaRuc}" : ''));

$establecimientoNombre = $reclamo['establecimiento_nombre'] ?? '';
$establecimientoDireccion = $reclamo['establecimiento_direccion'] ?? '';

$fechaReg = $reclamo['fecha_registro'] ?? '';
$fechaVenc = $reclamo['fecha_vencimiento_respuesta'] ?? '';

$estado = strtoupper((string)($reclamo['estado'] ?? ''));
$tipo = strtoupper((string)($reclamo['tipo'] ?? ''));

$token = $reclamo['evidencia_token'] ?? ''; // si no viene, igual funcionará sin usarlo
// Si en tu modelo no seleccionas evidencia_token, no pasa nada: usa el token del route y pásalo a la vista.
// Pero como pediste "sin romper nada", lo dejo opcional.

$badgeClass = 'text-bg-secondary';
$badgeDot = 'bg-secondary';
$estadoLabel = $estado ?: 'REGISTRADO';

if (in_array($estado, ['RESPONDIDO','CERRADO'], true)) {
  $badgeClass = 'text-bg-success';
  $badgeDot = 'bg-success';
} elseif (in_array($estado, ['EN_PROCESO'], true)) {
  $badgeClass = 'text-bg-warning';
  $badgeDot = 'bg-warning';
} elseif (in_array($estado, ['REGISTRADO'], true)) {
  $badgeClass = 'text-bg-primary';
  $badgeDot = 'bg-primary';
}

$respondidoAt = '';
if (!empty($respuestas) && isset($respuestas[0]['fecha_respuesta'])) {
  $respondidoAt = (string)$respuestas[0]['fecha_respuesta'];
}

// URL descarga en PDF oficial (AJUSTA si tu ruta real es otra)
$downloadUrl = "/constancia/". htmlspecialchars($token_reclamo) ."/pdf"; // fallback
// Si tu route usa /hoja-oficial-pdf/{token} o /hoja-oficial/{token}/pdf, cambia aquí.
// Recomendación: que tu route sea /seguimiento/{token}/pdf-oficial o /hoja-oficial-pdf/{token}

$printJs = "window.print()";
?>
<style>
  .lr-hero {
    background: #f2a51a;
    color: #fff;
    border-radius: 14px;
    padding: 22px 24px;
    position: relative;
    overflow: hidden;
  }
  .lr-hero h1 { margin: 0; font-size: 26px; font-weight: 800; letter-spacing: .2px; }
  .lr-hero .sub { opacity: .95; margin-top: 4px; line-height: 1.2; }
  .lr-hero .sub small { opacity: .95; }
  .lr-hero .actions {
    position: absolute;
    right: 18px;
    top: 18px;
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
  }
  .lr-card {
    border: 1px solid rgba(0,0,0,.08);
    border-radius: 12px;
    background: #fff;
    box-shadow: 0 10px 25px rgba(0,0,0,.06);
    overflow: hidden;
  }
  .lr-card-header {
    padding: 14px 18px;
    border-bottom: 1px solid rgba(0,0,0,.06);
    background: #fbfbfb;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
  }
  .lr-card-header .title {
    font-weight: 700;
    color: #1f2a37;
  }
  .lr-kv {
    padding: 16px 18px;
  }
  .lr-kv .row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px 16px; }
  @media (max-width: 992px) {
    .lr-kv .row { grid-template-columns: 1fr; }
    .lr-hero .actions { position: static; margin-top: 14px; }
  }
  .lr-field {
    border: 1px solid rgba(0,0,0,.08);
    border-radius: 10px;
    padding: 12px 14px;
    background: #fff;
  }
  .lr-field .k { font-size: 12px; color: #6b7280; margin-bottom: 2px; }
  .lr-field .v { font-weight: 650; color: #111827; }
  .lr-section-title {
    margin: 0;
    padding: 14px 18px;
    font-weight: 800;
    color: #1f2a37;
    border-top: 1px solid rgba(0,0,0,.06);
    background: #fbfbfb;
  }
  .lr-body {
    padding: 16px 18px;
  }
  .lr-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 6px 10px;
    border-radius: 999px;
    font-weight: 700;
    font-size: 12px;
    white-space: nowrap;
  }
  .lr-dot { width: 9px; height: 9px; border-radius: 999px; display: inline-block; }
  .lr-answer {
    border: 1px solid rgba(0,0,0,.08);
    border-radius: 12px;
    padding: 14px 14px;
    background: #fff;
    margin-bottom: 10px;
  }
  .lr-answer .meta { font-size: 12px; color: #6b7280; margin-bottom: 8px; }
  .lr-answer .text { color: #111827; line-height: 1.45; }
  .lr-timeline { list-style: none; padding-left: 0; margin: 0; }
  .lr-timeline li {
    display: grid;
    grid-template-columns: 160px 1fr;
    gap: 10px;
    padding: 10px 0;
    border-bottom: 1px dashed rgba(0,0,0,.12);
  }
  .lr-timeline li:last-child { border-bottom: 0; }
  .lr-timeline .t { color: #6b7280; font-size: 12px; }
  .lr-timeline .e { font-weight: 650; color: #111827; }
  .lr-footer-note {
    margin-top: 18px;
    background: #7a7a7a;
    color: #fff;
    padding: 16px 18px;
    border-radius: 12px;
    text-align: center;
    font-size: 13px;
    line-height: 1.35;
  }

  /* botones alineados con tu look */
  .btn-outline-white {
    border: 1px solid rgba(255,255,255,.65);
    color: #fff;
    background: transparent;
  }
  .btn-outline-white:hover { background: rgba(255,255,255,.12); color: #fff; }

  /* impresión: ocultar botones */
  @media print {
    .no-print { display:none !important; }
    .lr-card { box-shadow: none; }
    .lr-hero { border-radius: 0; }
    body { background:#fff; }
  }
</style>

<div class="lr-hero mb-4">
  <h1><?= $h($empresaNombre) ?></h1>
  <div class="sub">
    <div style="font-size:18px; font-weight:700;">Libro de Reclamaciones</div>
    <small>
      Conforme a lo establecido en el Código de Protección y Defensa del Consumidor, esta institución cuenta con un Libro de Reclamaciones Virtual a su disposición.
    </small>
  </div>

  <div class="actions no-print">
    <a class="btn btn-outline-white btn-sm" href="/seguimiento">
      Consultar Reclamo
    </a>
  </div>
</div>

<div class="lr-card mb-4">
  <div class="lr-card-header">
    <div class="title">
      Hoja de Reclamación o Queja: <?= $h($empresaLinea) ?>
    </div>
    <div class="text-body-secondary">
      F. de Reclamo: <strong><?= $h($fechaReg) ?></strong>
    </div>
  </div>

  <div class="lr-kv">
    <div class="row">
      <div class="lr-field">
        <div class="k">Código / Número</div>
        <div class="v">
          Código: <span class="text-primary"><?= $h($reclamo['evidencia_token'] ?? '') ?></span>
          <span class="text-body-secondary">|</span>
          Número: <span class="text-primary"><?= $h($reclamo['correlativo_num'] ?? '') ?></span>
        </div>
      </div>

      <div class="lr-field d-flex align-items-center justify-content-between">
        <div>
          <div class="k">Estado</div>
          <div class="v">
            <span class="lr-badge <?= $badgeClass ?>">
              <span class="lr-dot <?= $badgeDot ?>"></span>
              <?= $h(ucwords(strtolower($estadoLabel))) ?>
            </span>
          </div>
        </div>
        <div class="text-end">
          <div class="k">Respondido</div>
          <div class="v"><?= $h($respondidoAt ?: '—') ?></div>
        </div>
      </div>

      <div class="lr-field">
        <div class="k">Sede / Establecimiento</div>
        <div class="v"><?= $h($establecimientoNombre ?: '—') ?></div>
      </div>

      <div class="lr-field">
        <div class="k">Dirección</div>
        <div class="v"><?= $h($establecimientoDireccion ?: '—') ?></div>
      </div>
    </div>
  </div>

  <h3 class="lr-section-title">1. IDENTIFICACIÓN DEL CONSUMIDOR RECLAMANTE</h3>
  <div class="lr-body">
    <?php
      $tipoPersona = strtoupper((string)($reclamo['consumidor_tipo'] ?? 'NATURAL'));
      $isJur = ($tipoPersona === 'JURIDICA');
      $isMenor = ((int)($reclamo['consumidor_menor'] ?? 0) === 1);

      $nombre = \App\Models\Reclamo::nombreImprimible($reclamo);
      $docTipo = $reclamo['consumidor_doc_tipo'] ?? '';
      $docNum  = $reclamo['consumidor_doc_num'] ?? '';
      $email   = $reclamo['consumidor_email'] ?? '';
      $tel     = $reclamo['consumidor_telefono'] ?? '';
      $dir     = $reclamo['consumidor_direccion'] ?? '';

      $tutorNom = $reclamo['tutor_nombres'] ?? '';
      $tutorDT  = $reclamo['tutor_doc_tipo'] ?? '';
      $tutorDN  = $reclamo['tutor_doc_num'] ?? '';

      $contNom = $reclamo['contacto_nombres'] ?? '';
      $contDT  = $reclamo['contacto_doc_tipo'] ?? '';
      $contDN  = $reclamo['contacto_doc_num'] ?? '';
    ?>

    <div class="row g-3">
      <div class="col-12 col-lg-4">
        <div class="lr-field">
          <div class="k">Tipo de Persona</div>
          <div class="v"><?= $h($isJur ? 'Persona Jurídica' : 'Persona Natural') ?></div>
        </div>
      </div>

      <div class="col-12 col-lg-4">
        <div class="lr-field">
          <div class="k">Tipo de Documento</div>
          <div class="v"><?= $h($docTipo) ?></div>
        </div>
      </div>

      <div class="col-12 col-lg-4">
        <div class="lr-field">
          <div class="k">Número de Documento</div>
          <div class="v"><?= $h($docNum) ?></div>
        </div>
      </div>

      <div class="col-12 col-lg-6">
        <div class="lr-field">
          <div class="k"><?= $h($isJur ? 'Razón Social' : 'Nombres y Apellidos') ?></div>
          <div class="v"><?= $h($nombre ?: '—') ?></div>
        </div>
      </div>

      <?php if ($isJur): ?>
        <div class="col-12 col-lg-6">
          <div class="lr-field">
            <div class="k">Nombres y Apellidos de Contacto</div>
            <div class="v"><?= $h($contNom ?: '—') ?></div>
          </div>
        </div>

        <div class="col-12 col-lg-4">
          <div class="lr-field">
            <div class="k">Tipo de Documento del Contacto</div>
            <div class="v"><?= $h($contDT ?: '—') ?></div>
          </div>
        </div>

        <div class="col-12 col-lg-4">
          <div class="lr-field">
            <div class="k">Número de Documento de Contacto</div>
            <div class="v"><?= $h($contDN ?: '—') ?></div>
          </div>
        </div>
      <?php endif; ?>

      <?php if (!$isJur && $isMenor): ?>
        <div class="col-12 col-lg-6">
          <div class="lr-field">
            <div class="k">Padre / Madre / Tutor</div>
            <div class="v"><?= $h($tutorNom ?: '—') ?></div>
          </div>
        </div>

        <div class="col-12 col-lg-3">
          <div class="lr-field">
            <div class="k">Tipo Doc. Tutor</div>
            <div class="v"><?= $h($tutorDT ?: '—') ?></div>
          </div>
        </div>

        <div class="col-12 col-lg-3">
          <div class="lr-field">
            <div class="k">N° Doc. Tutor</div>
            <div class="v"><?= $h($tutorDN ?: '—') ?></div>
          </div>
        </div>
      <?php endif; ?>

      <div class="col-12 col-lg-4">
        <div class="lr-field">
          <div class="k">Teléfono</div>
          <div class="v"><?= $h($tel ?: '—') ?></div>
        </div>
      </div>

      <div class="col-12 col-lg-4">
        <div class="lr-field">
          <div class="k">Correo Electrónico</div>
          <div class="v"><?= $h($email ?: '—') ?></div>
        </div>
      </div>

      <div class="col-12 col-lg-4">
        <div class="lr-field">
          <div class="k">Dirección de Domicilio</div>
          <div class="v"><?= $h($dir ?: '—') ?></div>
        </div>
      </div>
    </div>
  </div>

  <h3 class="lr-section-title">2. IDENTIFICACIÓN DEL BIEN CONTRATADO</h3>
  <div class="lr-body">
    <div class="row g-3">
      <div class="col-12 col-lg-4">
        <div class="lr-field">
          <div class="k">Bien Contratado</div>
          <div class="v"><?= $h(($reclamo['bien_tipo'] ?? '') === 'SERVICIO' ? 'Servicio' : 'Producto') ?></div>
        </div>
      </div>

      <div class="col-12 col-lg-4">
        <div class="lr-field">
          <div class="k">Tipo de Comprobante de Pago</div>
          <div class="v"><?= $h($reclamo['bien_doc_tipo'] ?? '—') ?></div>
        </div>
      </div>

      <div class="col-12 col-lg-4">
        <div class="lr-field">
          <div class="k">Número de Comprobante</div>
          <div class="v"><?= $h($reclamo['bien_doc_num'] ?? '—') ?></div>
        </div>
      </div>

      <div class="col-12 col-lg-4">
        <div class="lr-field">
          <div class="k">Monto Reclamado</div>
          <div class="v">
            <?= $h($reclamo['monto_reclamado'] !== null && $reclamo['monto_reclamado'] !== '' ? $reclamo['monto_reclamado'] : '—') ?>
          </div>
        </div>
      </div>

      <div class="col-12 col-lg-8">
        <div class="lr-field">
          <div class="k">Descripción del Producto o Servicio</div>
          <div class="v"><?= $h($reclamo['bien_contratado'] ?? '—') ?></div>
        </div>
      </div>

      <?php if (!empty($reclamo['evidencia_original'])): ?>
        <div class="col-12">
          <div class="lr-field">
            <div class="k">Archivo adjunto</div>
            <div class="v">
              <?= $h($reclamo['evidencia_original']) ?>
              <?php if (!empty($reclamo['evidencia_size'])): ?>
                <span class="text-body-secondary">(<?= $h($reclamo['evidencia_size']) ?> bytes)</span>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <h3 class="lr-section-title">3. DETALLE DE LA RECLAMACIÓN Y PEDIDO DEL CONSUMIDOR</h3>
  <div class="lr-body">
    <div class="row g-3">
      <div class="col-12 col-lg-4">
        <div class="lr-field">
          <div class="k">Tipo</div>
          <div class="v"><?= $h($tipo === 'QUEJA' ? 'Queja' : 'Reclamo') ?></div>
        </div>
      </div>

      <div class="col-12">
        <div class="lr-field">
          <div class="k">Detalle de la Reclamación o Queja</div>
          <div class="v" style="white-space: pre-wrap; font-weight:500;">
            <?= $h($reclamo['detalle'] ?? '') ?>
          </div>
        </div>
      </div>

      <div class="col-12">
        <div class="lr-field">
          <div class="k">Detalle del Pedido</div>
          <div class="v" style="white-space: pre-wrap; font-weight:500;">
            <?= $h($reclamo['pedido'] ?? '') ?>
          </div>
        </div>
      </div>
    </div>

    <div class="mt-3 lr-field">
      <div class="k">Respuesta a la solicitud</div>

      <?php if (empty($respuestas)): ?>
        <div class="v text-body-secondary" style="font-weight:500;">Aún no hay respuesta registrada.</div>
      <?php else: ?>
        <?php foreach ($respuestas as $r): ?>
          <div class="lr-answer">
            <div class="meta">
              <?= $h($r['fecha_respuesta'] ?? '') ?>
            </div>
            <div class="text"><?= nl2br($h($r['respuesta'] ?? '')) ?></div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <div class="mt-3 lr-field">
      <div class="k">Información relevante</div>
      <div class="v" style="font-weight:500; color:#374151;">
        La respuesta a la presente se envió mediante correo electrónico a la dirección que usted ha consignado en la hoja de reclamación.
      </div>
    </div>
  </div>

  <div class="lr-card-header no-print" style="border-top:1px solid rgba(0,0,0,.06); border-bottom:0; background:#fff;">
    <div class="d-flex gap-2">
      <button class="btn btn-outline-success" type="button" onclick="<?= $printJs ?>">
        <i class="bi bi-printer me-1"></i> Imprimir
      </button>

      <!-- Descarga PDF oficial -->
      <a class="btn btn-outline-success" href="<?= $downloadUrl ?>">
        <i class="bi bi-download me-1"></i> Descargar
      </a>
    </div>

    <a class="btn btn-outline-secondary" href="/seguimiento">
      <i class="bi bi-arrow-left me-1"></i> Regresar
    </a>
  </div>
</div>

<div class="lr-footer-note">
  Una vez registrada tu reclamación, la empresa la recibirá y dará inicio al proceso de atención. Te enviaremos un correo de confirmación con un código de seguimiento, el cual te permitirá conocer el estado de tu solicitud en todo momento.
  <br>
  Recibirás una respuesta en un plazo máximo de 15 días hábiles, conforme a lo establecido por la Ley N.° 29571 - Código de Protección y Defensa del Consumidor.
</div>
