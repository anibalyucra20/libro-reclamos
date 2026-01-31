<?php

/** @var array $errors */
/** @var array $old */
$errors = $errors ?? [];
$old = $old ?? [];

$val = function (string $key, string $default = '') use ($old): string {
  return htmlspecialchars((string)($old[$key] ?? $default), ENT_QUOTES, 'UTF-8');
};
$has = function (string $key) use ($errors): bool {
  return isset($errors[$key]) && $errors[$key] !== '';
};
$err = function (string $key) use ($errors): string {
  return htmlspecialchars((string)($errors[$key] ?? ''), ENT_QUOTES, 'UTF-8');
};

$oldTipo = strtoupper($old['tipo'] ?? 'RECLAMO');
$oldDocTipo = strtoupper($old['consumidor_doc_tipo'] ?? 'DNI');
$oldEstab = (string)($old['establecimiento_id'] ?? '');
$oldAcepta = (string)($old['acepta'] ?? '');

$oldConsumidorTipo = strtoupper($old['consumidor_tipo'] ?? 'NATURAL'); // NATURAL|JURIDICA
$oldMenor = (string)($old['consumidor_menor'] ?? '0'); // 1/0

$oldTutorDocTipo = strtoupper($old['tutor_doc_tipo'] ?? 'DNI');
$oldContactoDocTipo = strtoupper($old['contacto_doc_tipo'] ?? 'DNI');

$oldBienTipo = strtoupper($old['bien_tipo'] ?? '');
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

<form method="POST" action="/reclamo" class="needs-validation" novalidate enctype="multipart/form-data">
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
              <label class="form-label">Tipo de consumidor <span class="text-danger">*</span></label>
              <select
                class="form-select <?= $has('consumidor_tipo') ? 'is-invalid' : '' ?>"
                name="consumidor_tipo"
                id="consumidor_tipo"
                required>
                <option value="NATURAL" <?= $oldConsumidorTipo === 'NATURAL' ? 'selected' : '' ?>>Persona Natural</option>
                <option value="JURIDICA" <?= $oldConsumidorTipo === 'JURIDICA' ? 'selected' : '' ?>>Persona Jurídica</option>
              </select>
              <div class="invalid-feedback">
                <?= $has('consumidor_tipo') ? $err('consumidor_tipo') : 'Selecciona el tipo de consumidor.' ?>
              </div>
            </div>

            <div class="col-12 col-md-6 d-flex align-items-end" id="wrap_menor">
              <div class="form-check mt-2">
                <input
                  class="form-check-input"
                  type="checkbox"
                  name="consumidor_menor"
                  value="1"
                  id="consumidor_menor"
                  <?= ($oldMenor === '1') ? 'checked' : '' ?>>
                <label class="form-check-label" for="consumidor_menor">
                  El consumidor es menor de edad
                </label>
              </div>
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label" id="label_nombre_rs">Nombres <span class="text-danger">*</span></label>
              <input
                class="form-control <?= $has('consumidor_nombres') ? 'is-invalid' : '' ?>"
                name="consumidor_nombres"
                id="consumidor_nombres"
                required
                maxlength="160"
                autocomplete="given-name"
                value="<?= $val('consumidor_nombres') ?>">
              <div class="invalid-feedback">
                <?= $has('consumidor_nombres') ? $err('consumidor_nombres') : 'Ingresa tus nombres.' ?>
              </div>
            </div>

            <div class="col-12 col-md-6" id="wrap_apellidos">
              <label class="form-label">Apellidos</label>
              <input
                class="form-control"
                name="consumidor_apellidos"
                id="input_apellidos"
                maxlength="160"
                autocomplete="family-name"
                value="<?= $val('consumidor_apellidos') ?>">
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label">Documento <span class="text-danger">*</span></label>
              <div class="input-group has-validation">
                <select
                  class="form-select <?= ($has('consumidor_doc_tipo') || $has('consumidor_doc_num')) ? 'is-invalid' : '' ?>"
                  name="consumidor_doc_tipo"
                  id="consumidor_doc_tipo"
                  required
                  style="max-width: 160px;">
                  <option value="DNI" <?= $oldDocTipo === 'DNI'  ? 'selected' : '' ?>>DNI</option>
                  <option value="CE" <?= $oldDocTipo === 'CE'   ? 'selected' : '' ?>>CE</option>
                  <option value="PAS" <?= $oldDocTipo === 'PAS'  ? 'selected' : '' ?>>Pasaporte</option>
                  <option value="RUC" <?= $oldDocTipo === 'RUC'  ? 'selected' : '' ?>>RUC</option>
                  <option value="OTRO" <?= $oldDocTipo === 'OTRO' ? 'selected' : '' ?>>Otro</option>
                </select>

                <input
                  class="form-control <?= ($has('consumidor_doc_tipo') || $has('consumidor_doc_num')) ? 'is-invalid' : '' ?>"
                  name="consumidor_doc_num"
                  id="consumidor_doc_num"
                  required
                  maxlength="20"
                  placeholder="Número"
                  value="<?= $val('consumidor_doc_num') ?>">

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
                value="<?= $val('consumidor_email') ?>">
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
                value="<?= $val('consumidor_telefono') ?>">
            </div>

            <div class="col-12 col-md-6">
              <label class="form-label">Dirección</label>
              <input
                class="form-control"
                name="consumidor_direccion"
                maxlength="255"
                autocomplete="street-address"
                value="<?= $val('consumidor_direccion') ?>">
            </div>

            <!-- Tutor -->
            <div id="block_tutor" class="mt-4" style="display:none; width:100%;">
              <hr class="my-3">
              <div class="d-flex align-items-center justify-content-between">
                <div class="fw-semibold">Padre / Madre / Tutor (solo si el consumidor es menor)</div>
                <span class="badge text-bg-light border"><i class="bi bi-person-hearts me-1"></i> Tutor</span>
              </div>
              <div class="text-body-secondary small mb-3">Estos campos serán obligatorios si marcas “menor de edad”.</div>

              <div class="row g-3">
                <div class="col-12 col-md-6">
                  <label class="form-label">Nombres del tutor <span class="text-danger">*</span></label>
                  <input
                    class="form-control <?= $has('tutor_nombres') ? 'is-invalid' : '' ?>"
                    name="tutor_nombres"
                    id="tutor_nombres"
                    maxlength="160"
                    value="<?= $val('tutor_nombres') ?>">
                  <div class="invalid-feedback"><?= $has('tutor_nombres') ? $err('tutor_nombres') : 'Ingresa nombres del tutor.' ?></div>
                </div>

                <div class="col-12 col-md-6">
                  <label class="form-label">Documento del tutor <span class="text-danger">*</span></label>
                  <div class="input-group has-validation">
                    <select
                      class="form-select <?= ($has('tutor_doc_tipo') || $has('tutor_doc_num')) ? 'is-invalid' : '' ?>"
                      name="tutor_doc_tipo"
                      id="tutor_doc_tipo"
                      style="max-width:160px;">
                      <option value="DNI" <?= $oldTutorDocTipo === 'DNI' ? 'selected' : '' ?>>DNI</option>
                      <option value="CE" <?= $oldTutorDocTipo === 'CE'  ? 'selected' : '' ?>>CE</option>
                      <option value="PAS" <?= $oldTutorDocTipo === 'PAS' ? 'selected' : '' ?>>Pasaporte</option>
                      <option value="RUC" <?= $oldTutorDocTipo === 'RUC' ? 'selected' : '' ?>>RUC</option>
                      <option value="OTRO" <?= $oldTutorDocTipo === 'OTRO' ? 'selected' : '' ?>>Otro</option>
                    </select>

                    <input
                      class="form-control <?= ($has('tutor_doc_tipo') || $has('tutor_doc_num')) ? 'is-invalid' : '' ?>"
                      name="tutor_doc_num"
                      id="tutor_doc_num"
                      maxlength="20"
                      placeholder="Número"
                      value="<?= $val('tutor_doc_num') ?>">
                    <div class="invalid-feedback">
                      <?php if ($has('tutor_doc_tipo')): ?><?= $err('tutor_doc_tipo') ?>
                      <?php elseif ($has('tutor_doc_num')): ?><?= $err('tutor_doc_num') ?>
                      <?php else: ?>Completa tipo y número del tutor.<?php endif; ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Contacto -->
            <div id="block_contacto" class="mt-4" style="display:none; width:100%;">
              <hr class="my-3">
              <div class="d-flex align-items-center justify-content-between">
                <div class="fw-semibold">Persona de contacto (solo si es persona jurídica)</div>
                <span class="badge text-bg-light border"><i class="bi bi-person-badge me-1"></i> Contacto</span>
              </div>
              <div class="text-body-secondary small mb-3">Estos campos serán obligatorios si seleccionas “Persona Jurídica”.</div>

              <div class="row g-3">
                <div class="col-12 col-md-6">
                  <label class="form-label">Nombres del contacto <span class="text-danger">*</span></label>
                  <input
                    class="form-control <?= $has('contacto_nombres') ? 'is-invalid' : '' ?>"
                    name="contacto_nombres"
                    id="contacto_nombres"
                    maxlength="160"
                    value="<?= $val('contacto_nombres') ?>">
                  <div class="invalid-feedback"><?= $has('contacto_nombres') ? $err('contacto_nombres') : 'Ingresa nombres del contacto.' ?></div>
                </div>

                <div class="col-12 col-md-6">
                  <label class="form-label">Documento del contacto <span class="text-danger">*</span></label>
                  <div class="input-group has-validation">
                    <select
                      class="form-select <?= ($has('contacto_doc_tipo') || $has('contacto_doc_num')) ? 'is-invalid' : '' ?>"
                      name="contacto_doc_tipo"
                      id="contacto_doc_tipo"
                      style="max-width:160px;">
                      <option value="DNI" <?= $oldContactoDocTipo === 'DNI' ? 'selected' : '' ?>>DNI</option>
                      <option value="CE" <?= $oldContactoDocTipo === 'CE'  ? 'selected' : '' ?>>CE</option>
                      <option value="PAS" <?= $oldContactoDocTipo === 'PAS' ? 'selected' : '' ?>>Pasaporte</option>
                      <option value="RUC" <?= $oldContactoDocTipo === 'RUC' ? 'selected' : '' ?>>RUC</option>
                      <option value="OTRO" <?= $oldContactoDocTipo === 'OTRO' ? 'selected' : '' ?>>Otro</option>
                    </select>

                    <input
                      class="form-control <?= ($has('contacto_doc_tipo') || $has('contacto_doc_num')) ? 'is-invalid' : '' ?>"
                      name="contacto_doc_num"
                      id="contacto_doc_num"
                      maxlength="20"
                      placeholder="Número"
                      value="<?= $val('contacto_doc_num') ?>">
                    <div class="invalid-feedback">
                      <?php if ($has('contacto_doc_tipo')): ?><?= $err('contacto_doc_tipo') ?>
                      <?php elseif ($has('contacto_doc_num')): ?><?= $err('contacto_doc_num') ?>
                      <?php else: ?>Completa tipo y número del contacto.<?php endif; ?>
                    </div>
                  </div>
                </div>
              </div>
            </div>

          </div><!-- /row g-3 -->
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

            <div class="col-12 col-lg-4">
              <label class="form-label">Tipo de bien <span class="text-danger">*</span></label>
              <select class="form-select <?= $has('bien_tipo') ? 'is-invalid' : '' ?>" name="bien_tipo" id="bien_tipo" required>
                <option value="">-- Selecciona --</option>
                <option value="PRODUCTO" <?= $oldBienTipo === 'PRODUCTO' ? 'selected' : '' ?>>Producto</option>
                <option value="SERVICIO" <?= $oldBienTipo === 'SERVICIO' ? 'selected' : '' ?>>Servicio</option>
              </select>
              <div class="invalid-feedback">
                <?= $has('bien_tipo') ? $err('bien_tipo') : 'Selecciona si es Producto o Servicio.' ?>
              </div>
            </div>

            <div class="col-12 col-lg-4">
              <label class="form-label">Documento (boleta/factura/etc.)</label>
              <input
                class="form-control"
                name="bien_doc_tipo"
                id="bien_doc_tipo"
                maxlength="60"
                placeholder="Ej.: Boleta / Factura / Sin comprobante"
                value="<?= $val('bien_doc_tipo') ?>">
            </div>

            <div class="col-12 col-lg-4">
              <label class="form-label">N° de documento</label>
              <input
                class="form-control"
                name="bien_doc_num"
                id="bien_doc_num"
                maxlength="60"
                placeholder="Ej.: B001-12345"
                value="<?= $val('bien_doc_num') ?>">
            </div>

            <div class="col-12 col-lg-8">
              <label class="form-label">Bien contratado (producto/servicio) <span class="text-danger">*</span></label>
              <input
                class="form-control <?= $has('bien_contratado') ? 'is-invalid' : '' ?>"
                name="bien_contratado"
                required
                maxlength="255"
                placeholder="Ej.: Servicio de internet / Producto X"
                value="<?= $val('bien_contratado') ?>">
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
                  value="<?= $val('monto_reclamado') ?>">
                <div class="invalid-feedback">
                  <?= $has('monto_reclamado') ? $err('monto_reclamado') : 'Monto inválido.' ?>
                </div>
              </div>
              <div class="form-text">Opcional</div>
            </div>

            <!-- Evidencia / archivo -->
            <div class="col-12">
              <label class="form-label">Adjuntar archivo (opcional)</label>
              <input
                class="form-control <?= $has('evidencia') ? 'is-invalid' : '' ?>"
                type="file"
                name="evidencia"
                id="evidencia"
                accept=".pdf,.jpg,.jpeg,.png,.txt">
              <div class="invalid-feedback">
                <?= $has('evidencia') ? $err('evidencia') : 'Archivo inválido.' ?>
              </div>
              <div class="form-text">
                Máximo 10 MB. Formatos sugeridos: PDF, JPG/PNG, TXT.
              </div>
            </div>

            <div class="col-12">
              <label class="form-label">Detalle <span class="text-danger">*</span></label>
              <textarea
                class="form-control <?= $has('detalle') ? 'is-invalid' : '' ?>"
                name="detalle"
                required
                rows="5"
                placeholder="Describe lo sucedido..."><?= $val('detalle') ?></textarea>
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
                placeholder="¿Qué solución solicitas?"><?= $val('pedido') ?></textarea>
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
                  <?= ((string)$oldAcepta === '1') ? 'checked' : '' ?>>
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
  (() => {
    'use strict';

    // ✅ Validación Bootstrap
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(form => {
      form.addEventListener('submit', event => {
        if (!form.checkValidity()) {
          event.preventDefault();
          event.stopPropagation();
        }
        form.classList.add('was-validated');
      }, false);
    });

    // ✅ Dinámico: NATURAL/JURIDICA + menor + apellidos + required
    const tipoSel = document.getElementById('consumidor_tipo');
    const menorWrap = document.getElementById('wrap_menor');
    const menorChk = document.getElementById('consumidor_menor');
    const labelNombre = document.getElementById('label_nombre_rs');

    const wrapApellidos = document.getElementById('wrap_apellidos');
    const inputApellidos = document.getElementById('input_apellidos');

    const blockTutor = document.getElementById('block_tutor');
    const tutorN = document.getElementById('tutor_nombres');
    const tutorDT = document.getElementById('tutor_doc_tipo');
    const tutorDN = document.getElementById('tutor_doc_num');

    const blockContacto = document.getElementById('block_contacto');
    const contN = document.getElementById('contacto_nombres');
    const contDT = document.getElementById('contacto_doc_tipo');
    const contDN = document.getElementById('contacto_doc_num');

    const consDocTipo = document.getElementById('consumidor_doc_tipo');

    function setRequired(el, on) {
      if (!el) return;
      if (on) el.setAttribute('required', 'required');
      else el.removeAttribute('required');
    }

    function show(el, on) {
      if (!el) return;
      el.style.display = on ? '' : 'none';
    }

    function apply() {
      const tipo = (tipoSel?.value || 'NATURAL').toUpperCase();
      const menor = !!(menorChk && menorChk.checked);

      const isNat = (tipo === 'NATURAL');
      const isJur = (tipo === 'JURIDICA');

      if (labelNombre) {
        labelNombre.innerHTML = isJur ?
          'Razón social <span class="text-danger">*</span>' :
          'Nombres <span class="text-danger">*</span>';
      }

      // Apellidos SOLO NATURAL
      show(wrapApellidos, isNat);
      if (isJur && inputApellidos) inputApellidos.value = '';

      // Menor SOLO NATURAL
      show(menorWrap, isNat);
      if (!isNat && menorChk) menorChk.checked = false;

      // Tutor SOLO NATURAL + menor
      show(blockTutor, isNat && menor);
      setRequired(tutorN, isNat && menor);
      setRequired(tutorDT, isNat && menor);
      setRequired(tutorDN, isNat && menor);

      // Contacto SOLO JURIDICA
      show(blockContacto, isJur);
      setRequired(contN, isJur);
      setRequired(contDT, isJur);
      setRequired(contDN, isJur);

      // sugerir RUC si jurídica
      if (isJur && consDocTipo) consDocTipo.value = 'RUC';
    }

    if (tipoSel) tipoSel.addEventListener('change', apply);
    if (menorChk) menorChk.addEventListener('change', apply);

    apply();
  })();
</script>
<script>
  (() => {
    'use strict';

    const MAX = 10 * 1024 * 1024; // 10MB
    const file = document.getElementById('evidencia');

    if (file) {
      file.addEventListener('change', () => {
        const f = file.files && file.files[0] ? file.files[0] : null;
        if (f && f.size > MAX) {
          alert('El archivo excede 10 MB. Por favor sube uno más pequeño.');
          file.value = '';
        }
      });
    }
  })();
</script>