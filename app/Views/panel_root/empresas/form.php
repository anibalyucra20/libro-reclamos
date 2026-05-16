<?php
$row = $row ?? [];
$mode = $mode ?? 'new';
$id = (int)($row['id'] ?? 0);
$st = (string)($row['estado'] ?? 'ACTIVO');
?>
<?php
$__panelPrefix = rtrim((string)($tenant['panel_prefix'] ?? '/panel'), '/');
if ($__panelPrefix === '') $__panelPrefix = '/panel';
?>
<div class="d-flex align-items-center justify-content-between mb-3">
  <div>
    <h1 class="h3 fw-bold mb-1"><?= $mode==='new' ? 'Nueva empresa' : 'Editar empresa' ?></h1>
    <div class="text-body-secondary">Datos de la empresa.</div>
  </div>
  <a class="btn btn-outline-secondary" href="<?= $__panelPrefix ?>/empresas"><i class="bi bi-arrow-left me-1"></i> Volver</a>
</div>

<?php if (!empty($created)): ?><div class="alert alert-success">Empresa creada.</div><?php endif; ?>
<?php if (!empty($saved)): ?><div class="alert alert-success">Cambios guardados.</div><?php endif; ?>
<?php if (!empty($error)): ?><div class="alert alert-danger"><?= htmlspecialchars((string)$error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>

<div class="card border-0 shadow-sm">
  <div class="card-body">
    <form method="POST" action="<?= $__panelPrefix ?><?= $mode==='new' ? '/empresas' : '/empresas/'.$id ?>" class="row g-3">
      <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">

      <div class="col-12 col-lg-4">
        <label class="form-label">RUC</label>
        <input class="form-control" name="ruc" required maxlength="11"
          value="<?= htmlspecialchars((string)($row['ruc'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
      </div>

      <div class="col-12 col-lg-8">
        <label class="form-label">Razón Social</label>
        <input class="form-control" name="razon_social" required
          value="<?= htmlspecialchars((string)($row['razon_social'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
      </div>

      <div class="col-12 col-lg-6">
        <label class="form-label">Nombre Comercial</label>
        <input class="form-control" name="nombre_comercial"
          value="<?= htmlspecialchars((string)($row['nombre_comercial'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
      </div>

      <div class="col-12 col-lg-6">
        <label class="form-label">Sub Dominio</label>
        <input class="form-control" name="slug" required
          value="<?= htmlspecialchars((string)($row['slug'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
        <div class="form-text">Ej: empresa1</div>
      </div>

      <div class="col-12 col-lg-6">
        <label class="form-label">Email contacto</label>
        <input class="form-control" name="email_contacto" type="email"
          value="<?= htmlspecialchars((string)($row['email_contacto'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
      </div>

      <div class="col-12 col-lg-6">
        <label class="form-label">Teléfono contacto</label>
        <input class="form-control" name="telefono_contacto"
          value="<?= htmlspecialchars((string)($row['telefono_contacto'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
      </div>

      <div class="col-12">
        <label class="form-label">Dirección fiscal</label>
        <input class="form-control" name="direccion_fiscal"
          value="<?= htmlspecialchars((string)($row['direccion_fiscal'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
      </div>

      <div class="col-12 col-lg-4">
        <label class="form-label">Estado</label>
        <select class="form-select" name="estado">
          <option value="ACTIVO" <?= $st==='ACTIVO'?'selected':'' ?>>ACTIVO</option>
          <option value="INACTIVO" <?= $st==='INACTIVO'?'selected':'' ?>>INACTIVO</option>
        </select>
      </div>

      <div class="col-12 d-flex gap-2">
        <button class="btn btn-primary" type="submit"><i class="bi bi-save me-1"></i> Guardar</button>
        <a class="btn btn-outline-secondary" href="<?= $__panelPrefix ?>/empresas">Cancelar</a>
      </div>
    </form>
  </div>
</div>
