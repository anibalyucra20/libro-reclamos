<?php
$cfg = $cfg ?? [];
$saved = isset($_GET['saved']);
$tested = isset($_GET['test']);
?>
<?php
$__panelPrefix = rtrim((string)($tenant['panel_prefix'] ?? '/panel'), '/');
if ($__panelPrefix === '') $__panelPrefix = '/panel';
?>
<div class="d-flex align-items-center justify-content-between mb-3">
  <div>
    <h1 class="h3 fw-bold mb-1">Alertas</h1>
    <div class="text-body-secondary">Configura envíos por reclamos por vencer y vencidos.</div>
  </div>
</div>

<?php if ($saved): ?>
  <div class="alert alert-success">Configuración guardada.</div>
<?php endif; ?>

<?php if ($tested): ?>
  <div class="alert alert-info">Prueba ejecutada. Si había reclamos aplicables, se enviaron correos.</div>
<?php endif; ?>

<div class="card border-0 shadow-sm">
  <div class="card-body">
    <form method="POST" action="<?= $__panelPrefix ?>/alertas" class="row g-3">
      <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">

      <div class="col-12">
        <label class="form-label">Emails destino</label>
        <textarea class="form-control" name="emails" rows="2"
          placeholder="correo1@dominio.com, correo2@dominio.com"><?= htmlspecialchars((string)($cfg['emails'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
        <div class="form-text">Separar por coma, espacio o punto y coma.</div>
      </div>

      <div class="col-12 col-lg-3">
        <label class="form-label">Días antes (por vencer)</label>
        <input class="form-control" type="number" min="0" max="30" name="dias_antes"
          value="<?= (int)($cfg['dias_antes'] ?? 3) ?>">
      </div>

      <div class="col-12 col-lg-3">
        <label class="form-label">Hora envío</label>
        <input class="form-control" type="time" name="hora_envio"
          value="<?= htmlspecialchars((string)($cfg['hora_envio'] ?? '09:00'), ENT_QUOTES, 'UTF-8') ?>">
      </div>

      <div class="col-12 col-lg-3">
        <label class="form-label">Estado</label>
        <select class="form-select" name="estado">
          <?php $st = (string)($cfg['estado'] ?? 'ACTIVO'); ?>
          <option value="ACTIVO" <?= $st==='ACTIVO'?'selected':'' ?>>ACTIVO</option>
          <option value="INACTIVO" <?= $st==='INACTIVO'?'selected':'' ?>>INACTIVO</option>
        </select>
      </div>

      <div class="col-12 col-lg-3 d-flex align-items-end">
        <button class="btn btn-primary w-100" type="submit">
          <i class="bi bi-save me-1"></i> Guardar
        </button>
      </div>

      <div class="col-12">
        <div class="form-check form-switch">
          <input class="form-check-input" type="checkbox" role="switch" id="pv"
                 name="alertar_por_vencer" value="1" <?= ((int)($cfg['alertar_por_vencer'] ?? 1)===1)?'checked':'' ?>>
          <label class="form-check-label" for="pv">Alertar por vencer</label>
        </div>

        <div class="form-check form-switch mt-2">
          <input class="form-check-input" type="checkbox" role="switch" id="vn"
                 name="alertar_vencidos" value="1" <?= ((int)($cfg['alertar_vencidos'] ?? 1)===1)?'checked':'' ?>>
          <label class="form-check-label" for="vn">Alertar vencidos</label>
        </div>
      </div>
    </form>

    <hr>

    <form method="POST" action="<?= $__panelPrefix ?>/alertas/probar" class="d-flex gap-2">
      <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
      <button class="btn btn-outline-secondary" type="submit">
        <i class="bi bi-send me-1"></i> Enviar prueba
      </button>
      <div class="text-body-secondary small d-flex align-items-center">
        Envía correo real si hay reclamos por vencer/vencidos para alguna empresa.
      </div>
    </form>
  </div>
</div>
