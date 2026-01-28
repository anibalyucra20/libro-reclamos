<?php
$row = $row ?? [];
$mode = $mode ?? 'new';
$id = (int)($row['id'] ?? 0);
?>

<div class="d-flex align-items-center justify-content-between mb-3">
  <div>
    <h1 class="h3 fw-bold mb-1"><?= $mode==='new' ? 'Nuevo establecimiento' : 'Editar establecimiento' ?></h1>
    <div class="text-body-secondary">Datos del local.</div>
  </div>
  <a class="btn btn-outline-secondary" href="/establecimientos"><i class="bi bi-arrow-left me-1"></i> Volver</a>
</div>

<?php if (!empty($created)): ?><div class="alert alert-success">Establecimiento creado.</div><?php endif; ?>
<?php if (!empty($saved)): ?><div class="alert alert-success">Cambios guardados.</div><?php endif; ?>
<?php if (!empty($error)): ?><div class="alert alert-danger"><?= htmlspecialchars((string)$error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>

<div class="card border-0 shadow-sm">
  <div class="card-body">
    <form method="POST" action="<?= $mode==='new' ? '/establecimientos' : '/establecimientos/'.$id ?>" class="row g-3">
      <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">

      <div class="col-12 col-lg-4">
        <label class="form-label">Código identificación</label>
        <input class="form-control" name="codigo_identificacion" required
          value="<?= htmlspecialchars((string)($row['codigo_identificacion'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
      </div>

      <div class="col-12 col-lg-8">
        <label class="form-label">Nombre</label>
        <input class="form-control" name="nombre" required
          value="<?= htmlspecialchars((string)($row['nombre'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
      </div>

      <div class="col-12">
        <label class="form-label">Dirección</label>
        <input class="form-control" name="direccion" required
          value="<?= htmlspecialchars((string)($row['direccion'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
      </div>

      <div class="col-12 col-lg-3">
        <label class="form-label">Ubigeo</label>
        <input class="form-control" name="ubigeo"
          value="<?= htmlspecialchars((string)($row['ubigeo'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
      </div>

      <div class="col-12 col-lg-3">
        <label class="form-label">Departamento</label>
        <input class="form-control" name="departamento"
          value="<?= htmlspecialchars((string)($row['departamento'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
      </div>

      <div class="col-12 col-lg-3">
        <label class="form-label">Provincia</label>
        <input class="form-control" name="provincia"
          value="<?= htmlspecialchars((string)($row['provincia'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
      </div>

      <div class="col-12 col-lg-3">
        <label class="form-label">Distrito</label>
        <input class="form-control" name="distrito"
          value="<?= htmlspecialchars((string)($row['distrito'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
      </div>

      <div class="col-12 col-lg-6">
        <label class="form-label">Teléfono</label>
        <input class="form-control" name="telefono"
          value="<?= htmlspecialchars((string)($row['telefono'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
      </div>

      <div class="col-12 col-lg-6">
        <label class="form-label">Email</label>
        <input class="form-control" name="email" type="email"
          value="<?= htmlspecialchars((string)($row['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
      </div>

      <div class="col-12 col-lg-4">
        <label class="form-label">Estado</label>
        <?php $st = (string)($row['estado'] ?? 'ACTIVO'); ?>
        <select class="form-select" name="estado">
          <option value="ACTIVO" <?= $st==='ACTIVO'?'selected':'' ?>>ACTIVO</option>
          <option value="INACTIVO" <?= $st==='INACTIVO'?'selected':'' ?>>INACTIVO</option>
        </select>
      </div>

      <div class="col-12 d-flex gap-2">
        <button class="btn btn-primary" type="submit"><i class="bi bi-save me-1"></i> Guardar</button>
        <a class="btn btn-outline-secondary" href="/establecimientos">Cancelar</a>
      </div>
    </form>
  </div>
</div>
