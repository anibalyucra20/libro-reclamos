<?php

use App\Services\Auth;
use App\Services\ACL;

$__user = Auth::user();
$__empresaId = (int)($tenant['empresa_id'] ?? 0);

$__canExport = $__user && $__empresaId > 0
  ? ACL::can((int)$__user['id'], 'reclamos.exportar', $__empresaId, null)
  : false;

$estado = (string)($estado ?? '');
$tipo  = (string)($tipo ?? '');
$desde = (string)($desde ?? date('Y-m-01'));
$hasta = (string)($hasta ?? date('Y-m-d'));

$badgeClass = function (string $st): string {
  return match ($st) {
    'REGISTRADO' => 'text-bg-secondary',
    'EN_PROCESO' => 'text-bg-warning',
    'RESPONDIDO' => 'text-bg-success',
    'CERRADO'    => 'text-bg-dark',
    default      => 'text-bg-light',
  };
};

$fmt = function ($v): string {
  $s = (string)$v;
  if ($s === '') return '-';
  // Si viene como "YYYY-MM-DD HH:MM:SS", lo dejamos legible sin romper nada
  return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
};
?>
<?php
$__panelPrefix = rtrim((string)($tenant['panel_prefix'] ?? '/panel'), '/');
if ($__panelPrefix === '') $__panelPrefix = '/panel';
?>
<div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-3">
  <div>
    <h1 class="h3 fw-bold mb-1">Reclamos</h1>
    <div class="text-body-secondary">
      Lista de reclamos registrados (máx. 100, ordenados por fecha).
    </div>
  </div>

  <form method="GET" action="<?= $__panelPrefix ?>/reclamos" class="d-flex flex-column flex-sm-row gap-2 align-items-sm-end">
    <div>
      <label class="form-label mb-1">Estado</label>
      <select class="form-select" name="estado" style="min-width: 220px;">
        <option value="" <?= $estado === '' ? 'selected' : '' ?>>Todos</option>
        <?php foreach (['REGISTRADO', 'EN_PROCESO', 'RESPONDIDO', 'CERRADO'] as $st): ?>
          <option value="<?= $st ?>" <?= ($estado === $st ? 'selected' : '') ?>>
            <?= htmlspecialchars($st) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div>
      <label class="form-label mb-1">Tipo</label>
      <select class="form-select" name="tipo" style="min-width: 180px;">
        <option value="" <?= $tipo === '' ? 'selected' : '' ?>>Todos</option>
        <option value="RECLAMO" <?= $tipo === 'RECLAMO' ? 'selected' : '' ?>>RECLAMO</option>
        <option value="QUEJA" <?= $tipo === 'QUEJA' ? 'selected' : '' ?>>QUEJA</option>
      </select>
    </div>

    <div>
      <label class="form-label mb-1">Desde</label>
      <input class="form-control" type="date" name="desde" value="<?= htmlspecialchars($desde, ENT_QUOTES, 'UTF-8') ?>">
    </div>

    <div>
      <label class="form-label mb-1">Hasta</label>
      <input class="form-control" type="date" name="hasta" value="<?= htmlspecialchars($hasta, ENT_QUOTES, 'UTF-8') ?>">
    </div>


    <div class="d-flex gap-2">
      <button type="submit" class="btn btn-primary">
        <i class="bi bi-funnel me-1"></i> Filtrar
      </button>
      <a class="btn btn-outline-secondary" href="<?= $__panelPrefix ?>/reclamos">
        <i class="bi bi-x-circle me-1"></i> Limpiar
      </a>

      <?php if ($__canExport): ?>
        <?php
        $qs = [];
        if ($estado !== '') $qs['estado'] = $estado;
        if ($tipo !== '') $qs['tipo'] = $tipo;
        if ($desde !== '') $qs['desde'] = $desde;
        if ($hasta !== '') $qs['hasta'] = $hasta;
        $exportUrl = '/reclamos/exportar?' . http_build_query($qs);

        ?>
        <a class="btn btn-outline-success" href="<?= $__panelPrefix ?><?= htmlspecialchars($exportUrl, ENT_QUOTES, 'UTF-8') ?>">
          <i class="bi bi-file-earmark-excel me-1"></i> Exportar Excel
        </a>
      <?php endif; ?>
    </div>

  </form>
</div>

<div class="card border-0 shadow-sm">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th class="ps-3">Código</th>
            <th>Tipo</th>
            <th>Estado</th>
            <th>Establecimiento</th>
            <th>Registro</th>
            <th>Vence</th>
            <th class="text-end pe-3" style="width: 1%;">Acción</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($reclamos)): ?>
            <tr>
              <td colspan="7" class="py-5 text-center text-body-secondary">
                <div class="mb-2">
                  <i class="bi bi-inbox" style="font-size: 1.5rem;"></i>
                </div>
                No hay reclamos para el filtro seleccionado.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($reclamos as $r): ?>
              <?php
              $id = (int)$r['id'];
              $st = (string)$r['estado'];
              ?>
              <tr>
                <td class="ps-3 fw-semibold">
                  <?= htmlspecialchars((string)$r['codigo_reclamo']) ?>
                </td>

                <td>
                  <span class="badge rounded-pill text-bg-primary-subtle text-primary-emphasis">
                    <?= htmlspecialchars((string)$r['tipo']) ?>
                  </span>
                </td>

                <td>
                  <span class="badge <?= $badgeClass($st) ?>">
                    <?= htmlspecialchars($st) ?>
                  </span>
                </td>

                <td class="text-body-secondary">
                  <?= htmlspecialchars((string)$r['establecimiento']) ?>
                </td>

                <td class="text-body-secondary">
                  <?= $fmt($r['fecha_registro']) ?>
                </td>

                <td class="text-body-secondary">
                  <?= $fmt($r['fecha_vencimiento_respuesta']) ?>
                </td>

                <td class="text-end pe-3">
                  <a class="btn btn-sm btn-outline-primary" href="<?= $__panelPrefix ?>/reclamos/<?= $id ?>">
                    <i class="bi bi-eye me-1"></i> Ver
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>