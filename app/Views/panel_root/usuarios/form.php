<?php
$row = $row ?? [];
$empresas = $empresas ?? [];
$roles = $roles ?? [];

$__panelPrefix = rtrim((string)($tenant['panel_prefix'] ?? '/panel'), '/');
if ($__panelPrefix === '') $__panelPrefix = '/panel';
?>
<div class="d-flex align-items-center justify-content-between mb-3">
  <div>
    <h1 class="h3 fw-bold mb-1">Crear usuario inicial</h1>
    <div class="text-body-secondary">Crea un usuario con scope a nivel empresa para que administre su panel.</div>
  </div>
  <a class="btn btn-outline-secondary" href="<?= $__panelPrefix ?>/usuarios"><i class="bi bi-arrow-left me-1"></i> Volver</a>
</div>

<?php if (!empty($error)): ?><div class="alert alert-danger"><?= htmlspecialchars((string)$error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>

<div class="card border-0 shadow-sm">
  <div class="card-body">
    <form method="POST" action="<?= $__panelPrefix ?>/usuarios" class="row g-3">
      <input type="hidden" name="_csrf" value="<?= htmlspecialchars((string)$csrf, ENT_QUOTES, 'UTF-8') ?>">

      <div class="col-12 col-lg-6">
        <label class="form-label">Empresa</label>
        <select class="form-select" name="empresa_id" required>
          <option value="">Seleccionar...</option>
          <?php foreach ($empresas as $e): ?>
            <option value="<?= (int)$e['id'] ?>" <?= ((int)($row['empresa_id'] ?? 0) === (int)$e['id'])?'selected':'' ?>>
              <?= htmlspecialchars($e['razon_social'].' ('.$e['slug'].')', ENT_QUOTES, 'UTF-8') ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="col-12 col-lg-6">
        <label class="form-label">Rol</label>
        <select class="form-select" name="rol_id" required>
          <option value="">Seleccionar...</option>
          <?php foreach ($roles as $r): ?>
            <option value="<?= (int)$r['id'] ?>" <?= ((int)($row['rol_id'] ?? 0) === (int)$r['id'])?'selected':'' ?>>
              <?= htmlspecialchars($r['nombre'].' ('.$r['code'].')', ENT_QUOTES, 'UTF-8') ?>
            </option>
          <?php endforeach; ?>
        </select>
        <div class="form-text">Recomendado: rol tipo admin de empresa.</div>
      </div>

      <div class="col-12 col-lg-4">
        <label class="form-label">Email</label>
        <input class="form-control" name="email" value="<?= htmlspecialchars((string)($row['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
      </div>

      <div class="col-12 col-lg-4">
        <label class="form-label">Nombres</label>
        <input class="form-control" name="nombres" value="<?= htmlspecialchars((string)($row['nombres'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
      </div>

      <div class="col-12 col-lg-4">
        <label class="form-label">Apellidos</label>
        <input class="form-control" name="apellidos" value="<?= htmlspecialchars((string)($row['apellidos'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
      </div>

      <div class="col-12 col-lg-6">
        <label class="form-label">Password</label>
        <input class="form-control" name="password" placeholder="Mínimo 8 caracteres" minlength="8" required>
      </div>

      <div class="col-12">
        <button class="btn btn-primary" type="submit"><i class="bi bi-save me-1"></i> Crear usuario</button>
      </div>
    </form>
  </div>
</div>
