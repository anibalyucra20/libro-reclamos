<?php
$u = $user ?? [];
$scopes = $scopes ?? [];
$roles = $roles ?? [];
$establecimientos = $establecimientos ?? [];
?>
<?php
$__panelPrefix = rtrim((string)($tenant['panel_prefix'] ?? '/panel'), '/');
if ($__panelPrefix === '') $__panelPrefix = '/panel';
?>
<div class="d-flex align-items-center justify-content-between mb-3">
  <div>
    <h1 class="h3 fw-bold mb-1">Usuario</h1>
    <div class="text-body-secondary"><?= htmlspecialchars($u['email'] ?? '', ENT_QUOTES, 'UTF-8') ?></div>
  </div>
  <a class="btn btn-outline-secondary" href="<?= $__panelPrefix ?>/usuarios"><i class="bi bi-arrow-left me-1"></i> Volver</a>
</div>

<?php if (!empty($created)): ?><div class="alert alert-success">Usuario creado.</div><?php endif; ?>
<?php if (!empty($saved)): ?><div class="alert alert-success">Cambios guardados.</div><?php endif; ?>
<?php if (!empty($pwd)): ?><div class="alert alert-warning">Password actualizado.</div><?php endif; ?>

<div class="row g-3">
  <div class="col-12 col-lg-6">
    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div class="fw-bold mb-2">Datos</div>
        <form method="POST" action="<?= $__panelPrefix ?>/usuarios/<?= (int)$u['id'] ?>" class="row g-3">
          <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
          <div class="col-12">
            <label class="form-label">Nombres</label>
            <input class="form-control" name="nombres" value="<?= htmlspecialchars((string)($u['nombres'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
          </div>
          <div class="col-12">
            <label class="form-label">Apellidos</label>
            <input class="form-control" name="apellidos" value="<?= htmlspecialchars((string)($u['apellidos'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
          </div>
          <div class="col-12">
            <label class="form-label">Estado</label>
            <select class="form-select" name="estado">
              <option value="ACTIVO" <?= ($u['estado'] ?? '') === 'ACTIVO' ? 'selected' : '' ?>>ACTIVO</option>
              <option value="INACTIVO" <?= ($u['estado'] ?? '') === 'INACTIVO' ? 'selected' : '' ?>>INACTIVO</option>
            </select>
          </div>
          <div class="col-12">
            <button class="btn btn-primary" type="submit"><i class="bi bi-save me-1"></i> Guardar</button>
          </div>
        </form>
        <hr>
        <div class="fw-bold mb-2">Reset password</div>
        <form method="POST" action="<?= $__panelPrefix ?>/usuarios/<?= (int)$u['id'] ?>/password" class="d-flex gap-2">
          <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
          <input class="form-control" name="password" placeholder="Nuevo password (min 8)" required minlength="8">
          <button class="btn btn-outline-danger" type="submit"><i class="bi bi-key me-1"></i> Reset</button>
        </form>

      </div>
    </div>
  </div>

  <div class="col-12 col-lg-6">
    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div class="fw-bold mb-2">Permisos (empresa actual)</div>
        <div class="table-responsive">
          <table class="table align-middle">
            <thead>
              <tr>
                <th>Rol</th>
                <th>Establecimiento</th>
                <th>Estado</th>
                <th class="text-end">Acción</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!$scopes): ?>
                <tr>
                  <td colspan="4" class="text-body-secondary">Sin Permisos.</td>
                </tr>
              <?php else: ?>
                <?php foreach ($scopes as $s): ?>
                  <tr>
                    <td><?= htmlspecialchars((string)$s['rol_nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= $estNombre = trim((string)($s['establecimiento_nombre'] ?? ''));
                        echo htmlspecialchars($estNombre !== '' ? $estNombre : 'Nivel empresa', ENT_QUOTES, 'UTF-8');
                        ?></td>
                    <td><span class="badge <?= $s['estado'] === 'ACTIVO' ? 'text-bg-success' : 'text-bg-secondary' ?>"><?= htmlspecialchars((string)$s['estado']) ?></span></td>
                    <td class="text-end">
                      <?php if (($s['estado'] ?? '') === 'ACTIVO'): ?>
                        <form method="POST" action="<?= $__panelPrefix ?>/usuarios/<?= (int)$u['id'] ?>/scope/<?= (int)$s['id'] ?>/delete" style="display:inline">
                          <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">
                          <button class="btn btn-sm btn-outline-secondary" type="submit" onclick="return confirm('¿Cambiar la contraseña de este usuario?');">
                            <i class="bi bi-slash-circle me-1"></i> Desactivar
                          </button>
                        </form>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
        <hr>
        <div class="fw-bold mb-2">Agregar Permiso</div>
        <form method="POST" action="<?= $__panelPrefix ?>/usuarios/<?= (int)$u['id'] ?>/scope" class="row g-2">
          <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf, ENT_QUOTES, 'UTF-8') ?>">

          <div class="col-12 col-lg-6">
            <select class="form-select" name="rol_id" required>
              <option value="">Rol...</option>
              <?php foreach ($roles as $r): ?>
                <option value="<?= (int)$r['id'] ?>"><?= htmlspecialchars($r['nombre'], ENT_QUOTES, 'UTF-8') ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-12 col-lg-4">
            <select class="form-select" name="establecimiento_id">
              <option value="">(Nivel empresa)</option>
              <?php foreach ($establecimientos as $e): ?>
                <option value="<?= (int)$e['id'] ?>"><?= htmlspecialchars($e['nombre'], ENT_QUOTES, 'UTF-8') ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-12 col-lg-2 d-grid">
            <button class="btn btn-outline-primary" type="submit">
              <i class="bi bi-plus-circle me-1"></i> Agregar
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<script>
  (function() {
    const form = document.querySelector('form[action$="/scope"]');
    if (!form) return;
    const rol = form.querySelector('select[name="rol_id"]');
    const btn = form.querySelector('button[type="submit"]');

    function sync() {
      btn.disabled = !rol.value;
    }
    rol.addEventListener('change', sync);
    sync();
  })();
</script>