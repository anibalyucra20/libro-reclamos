<?php
$__panelPrefix = rtrim((string)($tenant['panel_prefix'] ?? '/panel'), '/');
if ($__panelPrefix === '') $__panelPrefix = '/panel';

$badgeClass = function (string $st): string {
  return match ($st) {
    'REGISTRADO' => 'text-bg-secondary',
    'EN_PROCESO' => 'text-bg-warning',
    'RESPONDIDO' => 'text-bg-success',
    'CERRADO'    => 'text-bg-dark',
    default      => 'text-bg-light',
  };
};

$st = strtoupper((string)($reclamo['estado'] ?? ''));
$canReply = in_array($st, ['REGISTRADO', 'EN_PROCESO'], true);

$id = (int)($reclamo['id'] ?? 0);

$codigo = htmlspecialchars((string)($reclamo['codigo_reclamo'] ?? ''), ENT_QUOTES, 'UTF-8');
$tipo   = htmlspecialchars((string)($reclamo['tipo'] ?? ''), ENT_QUOTES, 'UTF-8');
$estado = htmlspecialchars($st, ENT_QUOTES, 'UTF-8');

$establecimiento = htmlspecialchars((string)($reclamo['establecimiento'] ?? ''), ENT_QUOTES, 'UTF-8');
$vence = htmlspecialchars((string)($reclamo['fecha_vencimiento_respuesta'] ?? ''), ENT_QUOTES, 'UTF-8');
$registrado = htmlspecialchars((string)($reclamo['fecha_registro'] ?? ''), ENT_QUOTES, 'UTF-8');

$consTipo = strtoupper((string)($reclamo['consumidor_tipo'] ?? 'NATURAL'));
$consTipoSafe = htmlspecialchars($consTipo, ENT_QUOTES, 'UTF-8');
$esMenor = ((int)($reclamo['consumidor_menor'] ?? 0) === 1);

$docTipo = (string)($reclamo['consumidor_doc_tipo'] ?? '');
$docNum  = (string)($reclamo['consumidor_doc_num'] ?? '');
$doc = trim($docTipo . ': ' . $docNum);
$doc = htmlspecialchars($doc, ENT_QUOTES, 'UTF-8');

// ✅ Nombre consumidor (jurídica sin apellidos)
$consNombre = trim((string)($reclamo['consumidor_nombres'] ?? ''));
if ($consTipo !== 'JURIDICA') {
  $consNombre = trim($consNombre . ' ' . trim((string)($reclamo['consumidor_apellidos'] ?? '')));
}
$consNombreSafe = htmlspecialchars($consNombre, ENT_QUOTES, 'UTF-8');

$consEmail = htmlspecialchars((string)($reclamo['consumidor_email'] ?? ''), ENT_QUOTES, 'UTF-8');
$consTel   = htmlspecialchars((string)($reclamo['consumidor_telefono'] ?? ''), ENT_QUOTES, 'UTF-8');
$consDir   = htmlspecialchars((string)($reclamo['consumidor_direccion'] ?? ''), ENT_QUOTES, 'UTF-8');

// Tutor (NATURAL + menor)
$tutorNom = htmlspecialchars((string)($reclamo['tutor_nombres'] ?? ''), ENT_QUOTES, 'UTF-8');
$tutorDoc = trim((string)($reclamo['tutor_doc_tipo'] ?? '') . ': ' . (string)($reclamo['tutor_doc_num'] ?? ''));
$tutorDoc = htmlspecialchars($tutorDoc, ENT_QUOTES, 'UTF-8');

// Contacto (JURIDICA)
$contNom = htmlspecialchars((string)($reclamo['contacto_nombres'] ?? ''), ENT_QUOTES, 'UTF-8');
$contDoc = trim((string)($reclamo['contacto_doc_tipo'] ?? '') . ': ' . (string)($reclamo['contacto_doc_num'] ?? ''));
$contDoc = htmlspecialchars($contDoc, ENT_QUOTES, 'UTF-8');

// Bien + comprobante
$bienTipo = htmlspecialchars((string)($reclamo['bien_tipo'] ?? ''), ENT_QUOTES, 'UTF-8');
$bienDocTipo = htmlspecialchars((string)($reclamo['bien_doc_tipo'] ?? ''), ENT_QUOTES, 'UTF-8');
$bienDocNum  = htmlspecialchars((string)($reclamo['bien_doc_num'] ?? ''), ENT_QUOTES, 'UTF-8');

$bien = htmlspecialchars((string)($reclamo['bien_contratado'] ?? ''), ENT_QUOTES, 'UTF-8');
$monto = $reclamo['monto_reclamado'] ?? null;
$montoSafe = ($monto === null || $monto === '') ? '—' : 'S/ ' . htmlspecialchars((string)$monto, ENT_QUOTES, 'UTF-8');

$detalle = nl2br(htmlspecialchars((string)($reclamo['detalle'] ?? ''), ENT_QUOTES, 'UTF-8'));
$pedido  = nl2br(htmlspecialchars((string)($reclamo['pedido'] ?? ''), ENT_QUOTES, 'UTF-8'));

// Evidencia
$eviPath = (string)($reclamo['evidencia_path'] ?? '');
$eviOrig = htmlspecialchars((string)($reclamo['evidencia_original'] ?? ''), ENT_QUOTES, 'UTF-8');
$eviMime = htmlspecialchars((string)($reclamo['evidencia_mime'] ?? ''), ENT_QUOTES, 'UTF-8');
$eviSize = $reclamo['evidencia_size'] ?? null;
$eviUpAt = htmlspecialchars((string)($reclamo['evidencia_uploaded_at'] ?? ''), ENT_QUOTES, 'UTF-8');

$eviSizePretty = '—';
if (is_numeric($eviSize)) {
  $bytes = (float)$eviSize;
  if ($bytes < 1024) $eviSizePretty = (string)$bytes . ' B';
  elseif ($bytes < 1024 * 1024) $eviSizePretty = number_format($bytes / 1024, 1) . ' KB';
  else $eviSizePretty = number_format($bytes / (1024 * 1024), 2) . ' MB';
  $eviSizePretty = htmlspecialchars($eviSizePretty, ENT_QUOTES, 'UTF-8');
}

$labelPersona = ($consTipo === 'JURIDICA') ? 'Razón social' : 'Consumidor';


$hasEvidencia = !empty($reclamo['evidencia_path']);
$evidenciaNombre = htmlspecialchars((string)($reclamo['evidencia_original'] ?? 'archivo'), ENT_QUOTES, 'UTF-8');
$evidenciaSize = (int)($reclamo['evidencia_size'] ?? 0);

$evidenciaSizeKb = $evidenciaSize > 0
  ? number_format($evidenciaSize / 1024, 1) . ' KB'
  : '';

?>

<!-- Header -->
<div class="d-flex flex-column flex-lg-row align-items-lg-start justify-content-between gap-3 mb-3">
  <div>
    <nav aria-label="breadcrumb" class="mb-2">
      <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="<?= $__panelPrefix ?>/reclamos">Reclamos</a></li>
        <li class="breadcrumb-item active" aria-current="page"><?= $codigo ?></li>
      </ol>
    </nav>

    <div class="d-flex flex-wrap align-items-center gap-2">
      <h1 class="h3 fw-bold mb-0">Reclamo <?= $codigo ?></h1>
      <span class="badge <?= $badgeClass($st) ?>"><?= $estado ?></span>
      <span class="badge text-bg-primary-subtle text-primary-emphasis"><?= $tipo ?></span>
    </div>

    <div class="text-body-secondary mt-2">
      <i class="bi bi-geo-alt me-1"></i><?= $establecimiento ?>
      <span class="mx-2">•</span>
      <i class="bi bi-calendar-event me-1"></i>Registrado: <?= $registrado ?>
      <span class="mx-2">•</span>
      <i class="bi bi-hourglass-split me-1"></i>Vence: <?= $vence ?>
    </div>
  </div>

  <div class="d-flex flex-wrap gap-2">
    <a class="btn btn-outline-secondary" href="<?= $__panelPrefix ?>/reclamos">
      <i class="bi bi-arrow-left me-1"></i> Volver
    </a>

    <a class="btn btn-outline-danger" href="<?= $__panelPrefix ?>/reclamos/<?= $id ?>/pdf">
      <i class="bi bi-file-earmark-pdf me-1"></i> PDF oficial
    </a>

    <?php if ($canReply): ?>
      <a class="btn btn-primary" href="#responder">
        <i class="bi bi-reply me-1"></i> Responder
      </a>
    <?php endif; ?>
  </div>
</div>

<div class="row g-3">
  <!-- Left: main detail -->
  <div class="col-12 col-lg-8">

    <!-- Datos del consumidor -->
    <div class="card border-0 shadow-sm">
      <div class="card-body p-4">
        <h2 class="h5 fw-semibold mb-3">Datos del consumidor</h2>

        <div class="row g-3">
          <div class="col-12 col-md-6">
            <div class="text-uppercase text-body-secondary fw-semibold small mb-1">Tipo</div>
            <div class="fw-semibold">
              <?= $consTipoSafe !== '' ? $consTipoSafe : '—' ?>
              <?php if ($consTipo === 'NATURAL' && $esMenor): ?>
                <span class="badge text-bg-warning ms-2">MENOR</span>
              <?php endif; ?>
            </div>
          </div>

          <div class="col-12 col-md-6">
            <div class="text-uppercase text-body-secondary fw-semibold small mb-1"><?= htmlspecialchars($labelPersona, ENT_QUOTES, 'UTF-8') ?></div>
            <div class="fw-semibold"><?= $consNombreSafe !== '' ? $consNombreSafe : '—' ?></div>
            <div class="text-body-secondary"><?= $doc !== ':' ? $doc : '—' ?></div>
          </div>

          <div class="col-12 col-md-6">
            <div class="text-uppercase text-body-secondary fw-semibold small mb-1">Email</div>
            <div class="fw-semibold"><?= $consEmail !== '' ? $consEmail : '—' ?></div>
          </div>

          <div class="col-12 col-md-6">
            <div class="text-uppercase text-body-secondary fw-semibold small mb-1">Teléfono</div>
            <div class="fw-semibold"><?= $consTel !== '' ? $consTel : '—' ?></div>
          </div>

          <div class="col-12">
            <div class="text-uppercase text-body-secondary fw-semibold small mb-1">Dirección</div>
            <div class="fw-semibold"><?= $consDir !== '' ? $consDir : '—' ?></div>
          </div>
        </div>

        <?php if ($consTipo === 'NATURAL' && $esMenor): ?>
          <hr class="my-4">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <div class="fw-semibold">Padre / Madre / Tutor</div>
            <span class="badge text-bg-light border"><i class="bi bi-person-hearts me-1"></i> Tutor</span>
          </div>
          <div class="row g-3">
            <div class="col-12 col-md-6">
              <div class="text-uppercase text-body-secondary fw-semibold small mb-1">Nombres</div>
              <div class="fw-semibold"><?= $tutorNom !== '' ? $tutorNom : '—' ?></div>
            </div>
            <div class="col-12 col-md-6">
              <div class="text-uppercase text-body-secondary fw-semibold small mb-1">Documento</div>
              <div class="fw-semibold"><?= $tutorDoc !== ':' ? $tutorDoc : '—' ?></div>
            </div>
          </div>
        <?php endif; ?>

        <?php if ($consTipo === 'JURIDICA'): ?>
          <hr class="my-4">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <div class="fw-semibold">Persona de contacto</div>
            <span class="badge text-bg-light border"><i class="bi bi-person-badge me-1"></i> Contacto</span>
          </div>
          <div class="row g-3">
            <div class="col-12 col-md-6">
              <div class="text-uppercase text-body-secondary fw-semibold small mb-1">Nombres</div>
              <div class="fw-semibold"><?= $contNom !== '' ? $contNom : '—' ?></div>
            </div>
            <div class="col-12 col-md-6">
              <div class="text-uppercase text-body-secondary fw-semibold small mb-1">Documento</div>
              <div class="fw-semibold"><?= $contDoc !== ':' ? $contDoc : '—' ?></div>
            </div>
          </div>
        <?php endif; ?>

      </div>
    </div>

    <!-- Detalle del reclamo -->
    <div class="card border-0 shadow-sm mt-3">
      <div class="card-body p-4">
        <h2 class="h5 fw-semibold mb-3">Detalle del reclamo</h2>

        <div class="row g-3 mb-3">
          <div class="col-12 col-md-6">
            <div class="text-uppercase text-body-secondary fw-semibold small mb-1">Tipo de bien</div>
            <div class="fw-semibold"><?= $bienTipo !== '' ? $bienTipo : '—' ?></div>
            <div class="text-body-secondary">
              <?= ($bienDocTipo !== '' ? $bienDocTipo : '—') ?>
              <?= ($bienDocNum !== '' ? ' • ' . $bienDocNum : '') ?>
            </div>
          </div>

          <div class="col-12 col-md-6">
            <div class="text-uppercase text-body-secondary fw-semibold small mb-1">Bien contratado</div>
            <div class="fw-semibold"><?= $bien !== '' ? $bien : '—' ?></div>
            <div class="text-body-secondary">Monto: <?= $montoSafe ?></div>
          </div>
        </div>

        <hr class="my-3">

        <div class="mb-3">
          <div class="text-uppercase text-body-secondary fw-semibold small mb-1">Detalle</div>
          <div class="bg-light border rounded-3 p-3"><?= $detalle !== '' ? $detalle : '—' ?></div>
        </div>

        <div class="mb-0">
          <div class="text-uppercase text-body-secondary fw-semibold small mb-1">Pedido</div>
          <div class="bg-light border rounded-3 p-3"><?= $pedido !== '' ? $pedido : '—' ?></div>
        </div>

        <!-- Evidencia -->
        <hr class="my-4">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <div class="fw-semibold"><i class="bi bi-paperclip me-1"></i> Evidencia</div>
          <?php if ($eviPath !== ''): ?>
            <span class="badge text-bg-success">Adjunta</span>
          <?php else: ?>
            <span class="badge text-bg-light border">Sin archivo</span>
          <?php endif; ?>
        </div>

        <?php if ($eviPath === ''): ?>
          <div class="text-body-secondary">No se adjuntó ningún archivo.</div>
        <?php else: ?>
          <div class="row g-2">
            <div class="col-12 col-md-6">
              <div class="text-uppercase text-body-secondary fw-semibold small mb-1">Nombre</div>
              <div class="fw-semibold"><?= $eviOrig !== '' ? $eviOrig : '—' ?></div>
            </div>
            <div class="col-12 col-md-3">
              <div class="text-uppercase text-body-secondary fw-semibold small mb-1">Tipo</div>
              <div class="fw-semibold"><?= $eviMime !== '' ? $eviMime : '—' ?></div>
            </div>
            <div class="col-12 col-md-3">
              <div class="text-uppercase text-body-secondary fw-semibold small mb-1">Tamaño</div>
              <div class="fw-semibold"><?= $eviSizePretty ?></div>
            </div>
            <div class="col-12">
              <div class="text-uppercase text-body-secondary fw-semibold small mb-1">Subido</div>
              <div class="fw-semibold"><?= $eviUpAt !== '' ? $eviUpAt : '—' ?></div>
            </div>
            <!-- Si tienes endpoint de descarga/preview, lo conectas aquí -->
            <?php if ($hasEvidencia): ?>
              <a
                class="btn btn-outline-primary"
                href="<?= $__panelPrefix ?>/reclamos/<?= $id ?>/evidencia"
                target="_blank">
                <i class="bi bi-paperclip me-1"></i>
                Descargar evidencia
                <?php if ($evidenciaSizeKb): ?>
                  <small class="opacity-75">(<?= $evidenciaSizeKb ?>)</small>
                <?php endif; ?>
              </a>
            <?php endif; ?>
          </div>
        <?php endif; ?>

      </div>
    </div>

    <!-- Responder -->
    <div class="card border-0 shadow-sm mt-3" id="responder">
      <div class="card-body p-4">
        <h2 class="h5 fw-semibold mb-1">Responder</h2>
        <?php if ($canReply): ?>
          <div class="text-body-secondary mb-3">
            Al enviar una respuesta, el estado pasará a <span class="fw-semibold">RESPONDIDO</span>.
          </div>

          <form method="POST" action="<?= $__panelPrefix ?>/reclamos/<?= $id ?>/responder" class="needs-validation" novalidate>
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars(\App\Services\Csrf::token(), ENT_QUOTES, 'UTF-8') ?>">

            <label class="form-label">Respuesta <span class="text-danger">*</span></label>
            <textarea class="form-control" name="respuesta" required rows="5" placeholder="Escribe la respuesta para el consumidor..."></textarea>
            <div class="invalid-feedback">La respuesta es requerida.</div>

            <div class="d-flex flex-column flex-sm-row gap-2 justify-content-end mt-3">
              <a class="btn btn-outline-secondary" href="<?= $__panelPrefix ?>/reclamos/<?= $id ?>">
                <i class="bi bi-x-circle me-1"></i> Cancelar
              </a>
              <button type="submit" class="btn btn-primary">
                <i class="bi bi-send me-1"></i> Enviar respuesta
              </button>
            </div>
          </form>
        <?php else: ?>
          <div class="alert alert-light border d-flex gap-2 align-items-start mb-0">
            <i class="bi bi-info-circle mt-1"></i>
            <div class="text-body-secondary">
              Este reclamo ya fue respondido o cerrado.
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Right: side panels -->
  <div class="col-12 col-lg-4">
    <!-- Respuestas -->
    <div class="card border-0 shadow-sm">
      <div class="card-body p-4">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <h3 class="h6 text-uppercase text-body-secondary fw-bold mb-0">Respuestas</h3>
          <span class="badge text-bg-light border"><?= (int)count($respuestas) ?></span>
        </div>

        <?php if (empty($respuestas)): ?>
          <div class="text-body-secondary">
            <i class="bi bi-chat-left-dots me-1"></i>
            No hay respuestas registradas.
          </div>
        <?php else: ?>
          <div class="d-grid gap-2">
            <?php foreach ($respuestas as $rr): ?>
              <?php
              $autor = trim((string)($rr['nombres'] ?? '') . ' ' . (string)($rr['apellidos'] ?? ''));
              $autor = htmlspecialchars($autor, ENT_QUOTES, 'UTF-8');
              $fecha = htmlspecialchars((string)($rr['fecha_respuesta'] ?? ''), ENT_QUOTES, 'UTF-8');
              $resp  = nl2br(htmlspecialchars((string)($rr['respuesta'] ?? ''), ENT_QUOTES, 'UTF-8'));
              ?>
              <div class="border rounded-3 p-3 bg-light">
                <div class="d-flex justify-content-between gap-2">
                  <div class="fw-semibold"><?= $autor !== '' ? $autor : '—' ?></div>
                  <small class="text-body-secondary"><?= $fecha ?></small>
                </div>
                <div class="text-body-secondary mt-2"><?= $resp ?></div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Auditoría -->
    <div class="card border-0 shadow-sm mt-3">
      <div class="card-body p-4">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <h3 class="h6 text-uppercase text-body-secondary fw-bold mb-0">Historial</h3>
          <span class="badge text-bg-light border"><?= (int)count($eventos) ?></span>
        </div>

        <?php if (empty($eventos)): ?>
          <div class="text-body-secondary">
            <i class="bi bi-clock-history me-1"></i>
            Sin eventos.
          </div>
        <?php else: ?>
          <ul class="list-group list-group-flush">
            <?php foreach ($eventos as $ev): ?>
              <li class="list-group-item px-0">
                <div class="small text-body-secondary"><?= htmlspecialchars((string)($ev['created_at'] ?? ''), ENT_QUOTES, 'UTF-8') ?></div>
                <div class="fw-semibold"><?= htmlspecialchars((string)($ev['evento'] ?? ''), ENT_QUOTES, 'UTF-8') ?></div>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<script>
  // Validación Bootstrap (frontend)
  (() => {
    'use strict'
    const forms = document.querySelectorAll('.needs-validation')
    Array.from(forms).forEach(form => {
      form.addEventListener('submit', event => {
        if (!form.checkValidity()) {
          event.preventDefault()
          event.stopPropagation()
        }
        form.classList.add('was-validated')
      }, false)
    })
  })()
</script>