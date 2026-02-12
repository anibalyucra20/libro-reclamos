<?php $users = $users ?? []; ?>
<?php
$__panelPrefix = rtrim((string)($tenant['panel_prefix'] ?? '/panel'), '/');
if ($__panelPrefix === '') $__panelPrefix = '/panel';
?>
<div class="d-flex align-items-center justify-content-between mb-3">
  <div>
    <h1 class="h3 fw-bold mb-1">Usuarios</h1>
    <div class="text-body-secondary">Gestión de usuarios por empresa (máx. 200).</div>
  </div>
  <a class="btn btn-primary" href="<?= $__panelPrefix ?>/usuarios/nuevo">
    <i class="bi bi-person-plus me-1"></i> Nuevo
  </a>
</div>

<div class="card border-0 shadow-sm">
  <div class="table-responsive">
    <table class="table align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th class="ps-3">Nombre</th>
          <th>Email</th>
          <th>Estado</th>
          <th>Permisos</th>
          <th class="text-end pe-3">Acción</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$users): ?>
          <tr><td colspan="5" class="text-center text-body-secondary py-5">No hay usuarios.</td></tr>
        <?php else: ?>
          <?php foreach ($users as $u): ?>
            <tr>
              <td class="ps-3 fw-semibold"><?= htmlspecialchars($u['nombres'] . ' ' . ($u['apellidos'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
              <td><?= htmlspecialchars($u['email'], ENT_QUOTES, 'UTF-8') ?></td>
              <td>
                <span class="badge <?= $u['estado']==='ACTIVO'?'text-bg-success':'text-bg-secondary' ?>">
                  <?= htmlspecialchars($u['estado'], ENT_QUOTES, 'UTF-8') ?>
                </span>
              </td>
              <td><?= (int)$u['scopes'] ?></td>
              <td class="text-end pe-3">
                <a class="btn btn-sm btn-outline-primary" href="<?= $__panelPrefix ?>/usuarios/<?= (int)$u['id'] ?>">
                  <i class="bi bi-gear me-1"></i> Gestionar
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
