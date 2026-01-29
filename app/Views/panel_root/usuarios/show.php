<?php
$u = $user ?? [];
$scopes = $scopes ?? [];

$__panelPrefix = rtrim((string)($tenant['panel_prefix'] ?? '/panel'), '/');
if ($__panelPrefix === '') $__panelPrefix = '/panel';
?>
<div class="d-flex align-items-center justify-content-between mb-3">
  <div>
    <h1 class="h3 fw-bold mb-1">Usuario (Root)</h1>
    <div class="text-body-secondary"><?= htmlspecialchars((string)($u['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?></div>
  </div>
  <a class="btn btn-outline-secondary" href="<?= $__panelPrefix ?>/usuarios"><i class="bi bi-arrow-left me-1"></i> Volver</a>
</div>

<?php if (!empty($created)): ?><div class="alert alert-success">Usuario creado.</div><?php endif; ?>
<?php if (!empty($pwd)): ?><div class="alert alert-warning">Password actualizado.</div><?php endif; ?>

<div class="row g-3">
  <div class="col-12 col-lg-5">
    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div class="fw-bold mb-2">Reset password</div>
        <form method="POST" action="<?= $__panelPrefix ?>/usuarios/<?= (int)$u['id'] ?>/password" class="d-flex gap-2">
          <input type="hidden" name="_csrf" value="<?= htmlspecialchars((string)$csrf, ENT_QUOTES, 'UTF-8') ?>">
          <input class="form-control" name="password" placeholder="Nuevo password (min 8)" minlength="8" required>
          <button class="btn btn-outline-danger" type="submit" onclick="return confirm('¿Cambiar la contraseña de este usuario?');">
            <i class="bi bi-key me-1"></i> Reset
          </button>
        </form>
      </div>
    </div>
  </div>

  <div class="col-12 col-lg-7">
    <div class="card border-0 shadow-sm">
      <div class="card-body">
        <div class="fw-bold mb-2">Scopes (todas las empresas)</div>

        <div class="table-responsive">
          <table class="table align-middle">
            <thead>
              <tr>
                <th>Empresa</th>
                <th>Rol</th>
                <th>Establecimiento</th>
                <th>Estado</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!$scopes): ?>
                <tr><td colspan="4" class="text-body-secondary">Sin scopes.</td></tr>
              <?php else: ?>
                <?php foreach ($scopes as $s): ?>
                  <tr>
                    <td><?= htmlspecialchars(($s['empresa_razon'] ?? '').' ('.($s['empresa_slug'] ?? '').')', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars((string)($s['rol_nombre'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars((string)($s['establecimiento_nombre'] ?? 'Nivel empresa'), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><span class="badge <?= ($s['estado'] ?? '')==='ACTIVO'?'text-bg-success':'text-bg-secondary' ?>"><?= htmlspecialchars((string)$s['estado']) ?></span></td>
                  </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

      </div>
    </div>
  </div>
</div>
