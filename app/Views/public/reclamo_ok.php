<?php
$codigoTxt = htmlspecialchars((string)$codigo, ENT_QUOTES, 'UTF-8');
$estabTxt  = htmlspecialchars((string)($establecimiento['nombre'] ?? ''), ENT_QUOTES, 'UTF-8');
$vencTxt   = htmlspecialchars((string)$venc, ENT_QUOTES, 'UTF-8');
?>

<div class="text-center mb-4">
  <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success-subtle text-success"
    style="width:72px;height:72px;">
    <i class="bi bi-check2-circle" style="font-size: 2rem;"></i>
  </div>

  <h1 class="h3 fw-bold mt-3 mb-1">Registro recibido</h1>
  <p class="text-body-secondary mb-0">
    Hemos registrado tu reclamo/queja correctamente. Guarda el código para seguimiento.
  </p>
</div>

<div class="row justify-content-center">
  <div class="col-12 col-lg-9 col-xl-8">
    <div class="card border-0 shadow-sm">
      <div class="card-body p-4 p-md-5">
        <div class="row g-4 align-items-start">
          <div class="col-12 col-md-6">
            <div class="text-uppercase text-body-secondary fw-semibold small mb-1">Código del reclamo</div>
            <div class="d-flex align-items-center gap-2">
              <div class="display-6 fw-bold mb-0" style="letter-spacing:.06em;">
                <?= $codigoTxt ?>
              </div>
              <button type="button" class="btn btn-outline-secondary btn-sm" id="copyCodeBtn">
                <i class="bi bi-clipboard me-1"></i> Copiar
              </button>
            </div>
            <div id="copyHint" class="form-text mt-2">Guárdalo para futuras consultas.</div>
          </div>

          <div class="col-12 col-md-6">
            <div class="text-uppercase text-body-secondary fw-semibold small mb-1">Fecha máxima de respuesta</div>
            <div class="fs-5 fw-semibold">
              <?= $vencTxt ?>
              <span class="text-body-secondary fw-normal">(15 días hábiles)</span>
            </div>

            <div class="mt-3">
              <div class="text-uppercase text-body-secondary fw-semibold small mb-1">Establecimiento</div>
              <div class="fw-semibold"><?= $estabTxt ?></div>
            </div>
          </div>
        </div>

        <hr class="my-4">

        <div class="alert alert-light border d-flex gap-2 align-items-start mb-4" role="alert">
          <i class="bi bi-info-circle mt-1"></i>
          <div class="text-body-secondary">
            Se envió un correo electronico a <?= htmlspecialchars($email) ?> con todos los detalles de tu reclamo/queja.
          </div>
        </div>

        <div class="d-flex flex-column flex-sm-row gap-2 justify-content-end">


          <a class="btn btn-outline-secondary" href="/constancia/<?= htmlspecialchars($token) ?>/pdf">Descargar constancia</a>
          <a class="btn btn-outline-secondary" href="/seguimiento/<?= htmlspecialchars($token) ?>">Seguimiento</a>
          <a class="btn btn-outline-secondary" href="/">
            <i class="bi bi-house me-1"></i> Volver al inicio
          </a>
        </div>

        <!-- Token interno (no mostrar al usuario) -->
        <!-- <small class="text-body-secondary">Token: <?= htmlspecialchars((string)$token, ENT_QUOTES, 'UTF-8') ?></small> -->
      </div>
    </div>
  </div>
</div>

<script>
  (() => {
    const btn = document.getElementById('copyCodeBtn');
    const hint = document.getElementById('copyHint');
    const code = <?= json_encode((string)$codigo) ?>;

    if (!btn) return;

    btn.addEventListener('click', async () => {
      try {
        await navigator.clipboard.writeText(code);
        hint.textContent = 'Código copiado al portapapeles.';
        hint.classList.remove('text-danger');
        hint.classList.add('text-success');
      } catch (e) {
        hint.textContent = 'No se pudo copiar automáticamente. Copia el código manualmente.';
        hint.classList.remove('text-success');
        hint.classList.add('text-danger');
      }
    });
  })();
</script>