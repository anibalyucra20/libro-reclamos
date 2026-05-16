<?php $rows = $rows ?? []; ?>
<?php
$__panelPrefix = rtrim((string)($tenant['panel_prefix'] ?? '/panel'), '/');
if ($__panelPrefix === '') $__panelPrefix = '/panel';
?>
<div class="d-flex align-items-center justify-content-between mb-3">
  <div>
    <h1 class="h3 fw-bold mb-1">Empresas</h1>
    <div class="text-body-secondary">Gestión global (solo superadmin).</div>
  </div>
  <a class="btn btn-primary" href="<?= $__panelPrefix ?>/empresas/nuevo">
    <i class="bi bi-plus-circle me-1"></i> Nueva
  </a>
</div>

<div class="card border-0 shadow-sm">
  <div class="table-responsive">
    <table class="table align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th class="ps-3">ID</th>
          <th>RUC</th>
          <th>Razón Social</th>
          <th>Sub Dominio</th>
          <th>Estado</th>
          <th class="text-end pe-3">Acción</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$rows): ?>
          <tr><td colspan="6" class="text-center text-body-secondary py-5">No hay empresas.</td></tr>
        <?php else: ?>
          <?php foreach ($rows as $r): ?>
            <tr>
              <td class="ps-3"><?= (int)$r['id'] ?></td>
              <td><?= htmlspecialchars((string)$r['ruc'], ENT_QUOTES, 'UTF-8') ?></td>
              <td class="fw-semibold"><?= htmlspecialchars((string)$r['razon_social'], ENT_QUOTES, 'UTF-8') ?></td>
              <td><span class="badge text-bg-light border"><?= htmlspecialchars((string)$r['slug'], ENT_QUOTES, 'UTF-8') ?></span></td>
              <td>
                <span class="badge <?= $r['estado']==='ACTIVO'?'text-bg-success':'text-bg-secondary' ?>">
                  <?= htmlspecialchars((string)$r['estado'], ENT_QUOTES, 'UTF-8') ?>
                </span>
              </td>
              <td class="text-end pe-3">
                <a class="btn btn-sm btn-outline-primary" href="<?= $__panelPrefix ?>/empresas/<?= (int)$r['id'] ?>">
                  <i class="bi bi-pencil-square me-1"></i> Editar
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
