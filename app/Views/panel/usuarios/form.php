<?php
$roles = $roles ?? [];
$establecimientos = $establecimientos ?? [];
?>

<div class="d-flex align-items-center justify-content-between mb-3">
  <div>
    <h1 class="h3 fw-bold mb-1">Nuevo usuario</h1>
    <div class="text-body-secondary">Crea usuario y asigna rol/scope.</div>
  </div>
  <a class="btn btn-outline-secondary" href="/usuarios"><i class="bi bi-arrow-left me-1"></i> Volver</a>
</div>

<?php if (!empty($error)): ?>
  <div class="alert alert-danger"><?= htmlspecialchars((string)$error, ENT_QUOTES, 'UTF-8') ?></div>
<?php endif; ?>

<div class="card border-0 shadow-sm">
  <div class="card-body">
    <form method="POST" action="/usuarios" class="row g-3">
      <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">

      <div class="col-12 col-lg-6">
        <label class="form-label">Email</label>
        <input class="form-control" type="email" name="email" required>
      </div>

      <div class="col-12 col-lg-6">
        <label class="form-label">Password (inicial)</label>
        <input class="form-control" type="text" name="password" required>
      </div>

      <div class="col-12 col-lg-6">
        <label class="form-label">Nombres</label>
        <input class="form-control" type="text" name="nombres" required>
      </div>

      <div class="col-12 col-lg-6">
        <label class="form-label">Apellidos</label>
        <input class="form-control" type="text" name="apellidos">
      </div>

      <div class="col-12 col-lg-6">
        <label class="form-label">Rol</label>
        <select class="form-select" name="rol_id" required>
          <option value="">-- seleccionar --</option>
          <?php foreach ($roles as $r): ?>
            <option value="<?= (int)$r['id'] ?>"><?= htmlspecialchars($r['nombre'], ENT_QUOTES, 'UTF-8') ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-12 col-lg-6">
        <label class="form-label">Establecimiento (opcional)</label>
        <select class="form-select" name="establecimiento_id">
          <option value="">(Nivel empresa)</option>
          <?php foreach ($establecimientos as $e): ?>
            <option value="<?= (int)$e['id'] ?>"><?= htmlspecialchars($e['nombre'], ENT_QUOTES, 'UTF-8') ?></option>
          <?php endforeach; ?>
        </select>
        <div class="form-text">Si seleccionas un establecimiento, el scope queda limitado a ese local.</div>
      </div>

      <div class="col-12 d-flex gap-2">
        <button class="btn btn-primary" type="submit"><i class="bi bi-save me-1"></i> Crear</button>
        <a class="btn btn-outline-secondary" href="/usuarios">Cancelar</a>
      </div>
    </form>
  </div>
</div>
