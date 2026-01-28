<?php $rows = $rows ?? []; ?>
<?php
$__panelPrefix = rtrim((string)($tenant['panel_prefix'] ?? '/panel'), '/');
if ($__panelPrefix === '') $__panelPrefix = '/panel';
?>
<div class="d-flex align-items-center justify-content-between mb-3">
  <div>
    <h1 class="h3 fw-bold mb-1">Establecimientos</h1>
    <div class="text-body-secondary">Locales registrados en esta empresa.</div>
  </div>
  <a class="btn btn-primary" href="<?= $__panelPrefix ?>/establecimientos/nuevo">
    <i class="bi bi-plus-circle me-1"></i> Nuevo
  </a>
</div>

<div class="card border-0 shadow-sm">
  <div class="table-responsive">
    <table class="table align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th class="ps-3">Código</th>
          <th>Nombre</th>
          <th>Dirección</th>
          <th>Estado</th>
          <th class="text-end pe-3">Acción</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$rows): ?>
          <tr><td colspan="5" class="text-center text-body-secondary py-5">No hay establecimientos.</td></tr>
        <?php else: ?>
          <?php foreach ($rows as $r): ?>
            <tr>
              <td class="ps-3 fw-semibold"><?= htmlspecialchars($r['codigo_identificacion'], ENT_QUOTES, 'UTF-8') ?></td>
              <td><?= htmlspecialchars($r['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
              <td class="text-body-secondary"><?= htmlspecialchars($r['direccion'], ENT_QUOTES, 'UTF-8') ?></td>
              <td>
                <span class="badge <?= $r['estado']==='ACTIVO'?'text-bg-success':'text-bg-secondary' ?>">
                  <?= htmlspecialchars($r['estado'], ENT_QUOTES, 'UTF-8') ?>
                </span>
              </td>
              <td class="text-end pe-3">
                <a class="btn btn-sm btn-outline-primary" href="<?= $__panelPrefix ?>/establecimientos/<?= (int)$r['id'] ?>">
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
