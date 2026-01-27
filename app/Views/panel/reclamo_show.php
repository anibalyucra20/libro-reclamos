<?php
  $badgeClass = function(string $st): string {
    return match ($st) {
      'REGISTRADO' => 'text-bg-secondary',
      'EN_PROCESO' => 'text-bg-warning',
      'RESPONDIDO' => 'text-bg-success',
      'CERRADO'    => 'text-bg-dark',
      default      => 'text-bg-light',
    };
  };

  $st = (string)($reclamo['estado'] ?? '');
  $canReply = in_array($st, ['REGISTRADO','EN_PROCESO'], true);

  $codigo = htmlspecialchars((string)($reclamo['codigo_reclamo'] ?? ''), ENT_QUOTES, 'UTF-8');
  $tipo   = htmlspecialchars((string)($reclamo['tipo'] ?? ''), ENT_QUOTES, 'UTF-8');
  $estado = htmlspecialchars($st, ENT_QUOTES, 'UTF-8');

  $establecimiento = htmlspecialchars((string)($reclamo['establecimiento'] ?? ''), ENT_QUOTES, 'UTF-8');
  $vence = htmlspecialchars((string)($reclamo['fecha_vencimiento_respuesta'] ?? ''), ENT_QUOTES, 'UTF-8');

  $consumidor = trim((string)($reclamo['consumidor_nombres'] ?? '') . ' ' . (string)($reclamo['consumidor_apellidos'] ?? ''));
  $consumidor = htmlspecialchars($consumidor, ENT_QUOTES, 'UTF-8');

  $doc = htmlspecialchars((string)($reclamo['consumidor_doc_tipo'] ?? '') . ': ' . (string)($reclamo['consumidor_doc_num'] ?? ''), ENT_QUOTES, 'UTF-8');

  $bien = htmlspecialchars((string)($reclamo['bien_contratado'] ?? ''), ENT_QUOTES, 'UTF-8');
  $detalle = nl2br(htmlspecialchars((string)($reclamo['detalle'] ?? ''), ENT_QUOTES, 'UTF-8'));
  $pedido  = nl2br(htmlspecialchars((string)($reclamo['pedido'] ?? ''), ENT_QUOTES, 'UTF-8'));

  $id = (int)($reclamo['id'] ?? 0);
?>

<!-- Header -->
<div class="d-flex flex-column flex-lg-row align-items-lg-start justify-content-between gap-3 mb-3">
  <div>
    <nav aria-label="breadcrumb" class="mb-2">
      <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="/reclamos">Reclamos</a></li>
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
      <i class="bi bi-calendar-event me-1"></i>Vence: <?= $vence ?>
    </div>
  </div>

  <div class="d-flex flex-wrap gap-2">
    <a class="btn btn-outline-secondary" href="/reclamos">
      <i class="bi bi-arrow-left me-1"></i> Volver
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
    <div class="card border-0 shadow-sm">
      <div class="card-body p-4">
        <h2 class="h5 fw-semibold mb-3">Detalle del reclamo</h2>

        <div class="row g-3 mb-3">
          <div class="col-12 col-md-6">
            <div class="text-uppercase text-body-secondary fw-semibold small mb-1">Consumidor</div>
            <div class="fw-semibold"><?= $consumidor !== '' ? $consumidor : '—' ?></div>
            <div class="text-body-secondary"><?= $doc !== ':' ? $doc : '—' ?></div>
          </div>

          <div class="col-12 col-md-6">
            <div class="text-uppercase text-body-secondary fw-semibold small mb-1">Bien contratado</div>
            <div class="fw-semibold"><?= $bien !== '' ? $bien : '—' ?></div>
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

          <form method="POST" action="/reclamos/<?= $id ?>/responder" class="needs-validation" novalidate>
            <input type="hidden" name="_csrf" value="<?= htmlspecialchars(\App\Services\Csrf::token(), ENT_QUOTES, 'UTF-8') ?>">

            <label class="form-label">Respuesta <span class="text-danger">*</span></label>
            <textarea class="form-control" name="respuesta" required rows="5" placeholder="Escribe la respuesta para el consumidor..."></textarea>
            <div class="invalid-feedback">La respuesta es requerida.</div>

            <div class="d-flex flex-column flex-sm-row gap-2 justify-content-end mt-3">
              <a class="btn btn-outline-secondary" href="/reclamos/<?= $id ?>">
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
