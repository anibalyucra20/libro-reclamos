<?php
  /** @var array $errors */
  /** @var array $old */
  $errors = $errors ?? [];
  $old = $old ?? [];

  $val = function(string $key, string $default = '') use ($old): string {
    return htmlspecialchars((string)($old[$key] ?? $default), ENT_QUOTES, 'UTF-8');
  };
  $has = function(string $key) use ($errors): bool {
    return isset($errors[$key]) && $errors[$key] !== '';
  };
  $err = function(string $key) use ($errors): string {
    return htmlspecialchars((string)($errors[$key] ?? ''), ENT_QUOTES, 'UTF-8');
  };

  $oldTipo = strtoupper($old['tipo'] ?? 'RECLAMO');
  $oldDocTipo = strtoupper($old['consumidor_doc_tipo'] ?? 'DNI');
  $oldEstab = (string)($old['establecimiento_id'] ?? '');
  $oldAcepta = (string)($old['acepta'] ?? '');
?>

<h1 class="h3 fw-bold mb-1">Registrar reclamo / queja</h1>
<p class="text-body-secondary mb-4">
  Completa el formulario. Los campos marcados con <span class="text-danger">*</span> son obligatorios.
</p>

<nav aria-label="breadcrumb" class="mb-3">
  <ol class="breadcrumb mb-0">
    <li class="breadcrumb-item"><a href="/">Inicio</a></li>
    <li class="breadcrumb-item active" aria-current="page">Nuevo reclamo</li>
  </ol>
</nav>

<?php if (!empty($errors)): ?>
  <div class="alert alert-danger border d-flex gap-2 align-items-start" role="alert">
    <i class="bi bi-exclamation-triangle mt-1"></i>
    <div>
      <div class="fw-semibold">Revisa el formulario</div>
      <div class="text-body-secondary">Hay campos con errores. Corrígelos y vuelve a enviar.</div>
    </div>
  </div>
<?php endif; ?>

<form method="POST" action="/reclamo" class="needs-validation" novalidate>
  <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">

  <div class="row g-3">
    <!-- Datos del reclamo -->
    <div class="col-12">
      <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
          <div class="d-flex align-items-start justify-content-between gap-3 mb-3">
            <div>
              <h2 class="h5 fw-semibold mb-1">Datos del reclamo</h2>
              <div class="text-body-secondary">Selecciona el tipo y el establecimiento.</div>
            </div>
            <span class="badge text-bg-primary">
              <i class="bi bi-journal-text me-1"></i> Registro
            </span>
          </div>

          <div class="row g-3">
            <div class="col-12 col-md-4">
              <label class="form-label">Tipo <span class="text-danger">*</span></label>
              <select class="form-select <?= $has('tipo') ? 'is-invalid' : '' ?>" name="tipo" required>
                <option value="RECLAMO" <?= $oldTipo === 'RECLAMO' ? 'selected' : '' ?>>Reclamo</option>
                <option value="QUEJA" <?= $oldTipo === 'QUEJA' ? 'selected' : '' ?>>Queja</option>
              </select>
              <div class="invalid-feedback">
                <?= $has('tipo') ? $err('tipo') : 'Selecciona un tipo.' ?>
              </div>
            </div>

            <div class="col-12 col-md-8">
              <label class="form-label">Establecimiento <span class="text-danger">*</span></label>
              <select class="form-select <?= $has('establecimiento_id') ? 'is-invalid' : '' ?>" name="establecimiento_id" required>
                <option value="">-- Selecciona --</option>
                <?php foreach ($establecimientos as $e): ?>
                  <?php $id = (string)(int)$e['id']; ?>
                  <option value="<?= (int)$e['id'] ?>" <?= $oldEstab === $id ? 'selected' : '' ?>>
                    <?= htmlspecialchars($e['nombre']) ?> — <?= htmlspecialchars($e['direccion']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <div class="invalid-feedback">
                <?= $has('establecimiento_id') ? $err('establecimiento_id') : 'Selecciona un establecimiento.' ?>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>

    <!-- Datos del consumidor -->
    <div class="col-12">
      <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
          <h2 class="h5 fw-semibold mb-1">Datos del consumidor</h2>
          <div class="text-body-secondary mb-3">Información de contacto para atender el caso.</div>

          <div class="row g-3">
            <div class="col-12 col-md-6">
              <label class="form-label">Nombres <span class="text-danger">*</span></label>
              <input
                class="form-control <?= $has('consumidor_nombres') ? 'is-invalid' : '' ?>"
                name="consumidor_nombres"
                required
                maxlength="160"
                autocomplete="given-name"
                value="<?= $val('consumidor_nombres') ?>"
              >
              <div class="invalid-feedback">
                <?= $has('consumidor_nombres') ? $err('consumidor_nombres') : 'Ingresa tus nombres.' ?>
              </div>
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label">Apellidos</label>
              <input
                class="form-control"
                name="consumidor_apellidos"
                maxlength="160"
                autocomplete="family-name"
                value="<?= $val('consumidor_apellidos') ?>"
              >
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label">Documento <span class="text-danger">*</span></label>
              <div class="input-group has-validation">
                <select
                  class="form-select <?= ($has('consumidor_doc_tipo') || $has('consumidor_doc_num')) ? 'is-invalid' : '' ?>"
                  name="consumidor_doc_tipo"
                  required
                  style="max-width: 160px;"
                >
                  <option value="DNI"  <?= $oldDocTipo === 'DNI'  ? 'selected' : '' ?>>DNI</option>
                  <option value="CE"   <?= $oldDocTipo === 'CE'   ? 'selected' : '' ?>>CE</option>
                  <option value="PAS"  <?= $oldDocTipo === 'PAS'  ? 'selected' : '' ?>>Pasaporte</option>
                  <option value="RUC"  <?= $oldDocTipo === 'RUC'  ? 'selected' : '' ?>>RUC</option>
                  <option value="OTRO" <?= $oldDocTipo === 'OTRO' ? 'selected' : '' ?>>Otro</option>
                </select>

                <input
                  class="form-control <?= ($has('consumidor_doc_tipo') || $has('consumidor_doc_num')) ? 'is-invalid' : '' ?>"
                  name="consumidor_doc_num"
                  required
                  maxlength="20"
                  placeholder="Número"
                  value="<?= $val('consumidor_doc_num') ?>"
                >

                <div class="invalid-feedback">
                  <?php if ($has('consumidor_doc_tipo')): ?>
                    <?= $err('consumidor_doc_tipo') ?>
                  <?php elseif ($has('consumidor_doc_num')): ?>
                    <?= $err('consumidor_doc_num') ?>
                  <?php else: ?>
                    Completa tipo y número de documento.
                  <?php endif; ?>
                </div>
              </div>
              <div class="form-text">Ej.: DNI 12345678</div>
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label">Email</label>
              <input
                class="form-control <?= $has('consumidor_email') ? 'is-invalid' : '' ?>"
                name="consumidor_email"
                type="email"
                maxlength="190"
                autocomplete="email"
                placeholder="correo@ejemplo.com"
                value="<?= $val('consumidor_email') ?>"
              >
              <div class="invalid-feedback">
                <?= $has('consumidor_email') ? $err('consumidor_email') : 'Ingresa un email válido.' ?>
              </div>
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label">Teléfono</label>
              <input
                class="form-control"
                name="consumidor_telefono"
                maxlength="50"
                autocomplete="tel"
                placeholder="+51 ..."
                value="<?= $val('consumidor_telefono') ?>"
              >
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label">Dirección</label>
              <input
                class="form-control"
                name="consumidor_direccion"
                maxlength="255"
                autocomplete="street-address"
                value="<?= $val('consumidor_direccion') ?>"
              >
            </div>
          </div>

        </div>
      </div>
    </div>

    <!-- Detalle -->
    <div class="col-12">
      <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
          <h2 class="h5 fw-semibold mb-1">Detalle del incidente</h2>
          <div class="text-body-secondary mb-3">Describe lo ocurrido y lo que solicitas.</div>

          <div class="row g-3">
            <div class="col-12 col-lg-8">
              <label class="form-label">Bien contratado (producto/servicio) <span class="text-danger">*</span></label>
              <input
                class="form-control <?= $has('bien_contratado') ? 'is-invalid' : '' ?>"
                name="bien_contratado"
                required
                maxlength="255"
                placeholder="Ej.: Servicio de internet / Producto X"
                value="<?= $val('bien_contratado') ?>"
              >
              <div class="invalid-feedback">
                <?= $has('bien_contratado') ? $err('bien_contratado') : 'Indica el bien contratado.' ?>
              </div>
            </div>

            <div class="col-12 col-lg-4">
              <label class="form-label">Monto reclamado</label>
              <div class="input-group has-validation">
                <span class="input-group-text">S/</span>
                <input
                  class="form-control <?= $has('monto_reclamado') ? 'is-invalid' : '' ?>"
                  name="monto_reclamado"
                  type="number"
                  step="0.01"
                  min="0"
                  placeholder="0.00"
                  value="<?= $val('monto_reclamado') ?>"
                >
                <div class="invalid-feedback">
                  <?= $has('monto_reclamado') ? $err('monto_reclamado') : 'Monto inválido.' ?>
                </div>
              </div>
              <div class="form-text">Opcional</div>
            </div>

            <div class="col-12">
              <label class="form-label">Detalle <span class="text-danger">*</span></label>
              <textarea
                class="form-control <?= $has('detalle') ? 'is-invalid' : '' ?>"
                name="detalle"
                required
                rows="5"
                placeholder="Describe lo sucedido..."
              ><?= $val('detalle') ?></textarea>
              <div class="invalid-feedback">
                <?= $has('detalle') ? $err('detalle') : 'Escribe el detalle del reclamo/queja.' ?>
              </div>
            </div>

            <div class="col-12">
              <label class="form-label">Pedido <span class="text-danger">*</span></label>
              <textarea
                class="form-control <?= $has('pedido') ? 'is-invalid' : '' ?>"
                name="pedido"
                required
                rows="4"
                placeholder="¿Qué solución solicitas?"
              ><?= $val('pedido') ?></textarea>
              <div class="invalid-feedback">
                <?= $has('pedido') ? $err('pedido') : 'Escribe tu pedido.' ?>
              </div>
            </div>

            <div class="col-12">
              <div class="alert alert-light border d-flex gap-2 align-items-start mb-0">
                <i class="bi bi-info-circle mt-1"></i>
                <div class="text-body-secondary">
                  Al enviar este formulario, recibirás un código/constancia para seguimiento del caso.
                </div>
              </div>
            </div>

            <div class="col-12">
              <div class="form-check">
                <input
                  class="form-check-input <?= $has('acepta') ? 'is-invalid' : '' ?>"
                  type="checkbox"
                  name="acepta"
                  value="1"
                  id="acepta"
                  required
                  <?= ((string)$oldAcepta === '1') ? 'checked' : '' ?>
                >
                <label class="form-check-label" for="acepta">
                  Declaro que la información es correcta. <span class="text-danger">*</span>
                </label>
                <div class="invalid-feedback">
                  <?= $has('acepta') ? $err('acepta') : 'Debes aceptar la declaración para continuar.' ?>
                </div>
              </div>
            </div>

            <div class="col-12 d-flex flex-column flex-sm-row gap-2 justify-content-end">
              <a class="btn btn-outline-secondary" href="/">
                <i class="bi bi-arrow-left me-1"></i> Cancelar
              </a>
              <button type="submit" class="btn btn-primary">
                <i class="bi bi-send me-1"></i> Enviar
              </button>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</form>

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
