<?php
$rows = $rows ?? [];
$empresas = $empresas ?? [];
$filters = $filters ?? ['empresa_id'=>0,'q'=>''];

$__panelPrefix = rtrim((string)($tenant['panel_prefix'] ?? '/panel'), '/');
if ($__panelPrefix === '') $__panelPrefix = '/panel';
?>
<div class="d-flex align-items-center justify-content-between mb-3">
  <div>
    <h1 class="h3 fw-bold mb-1">Usuarios (Root)</h1>
    <div class="text-body-secondary">Usuarios y empresas donde tienen scopes activos.</div>
  </div>
  <a class="btn btn-primary" href="<?= $__panelPrefix ?>/usuarios/nuevo">
    <i class="bi bi-person-plus me-1"></i> Crear usuario inicial
  </a>
</div>

<form class="row g-2 mb-3" method="GET" action="<?= $__panelPrefix ?>/usuarios">
  <div class="col-12 col-lg-4">
    <select class="form-select" name="empresa_id">
      <option value="0">(Todas las empresas)</option>
      <?php foreach ($empresas as $e): ?>
        <option value="<?= (int)$e['id'] ?>" <?= ((int)($filters['empresa_id'] ?? 0) === (int)$e['id'])?'selected':'' ?>>
          <?= htmlspecialchars($e['razon_social'].' ('.$e['slug'].')', ENT_QUOTES, 'UTF-8') ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="col-12 col-lg-6">
    <input class="form-control" name="q" placeholder="Buscar por email / nombres / apellidos" value="<?= htmlspecialchars((string)($filters['q'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
  </div>
  <div class="col-12 col-lg-2 d-grid">
    <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search me-1"></i> Filtrar</button>
  </div>
</form>

<div class="card border-0 shadow-sm">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table align-middle">
        <thead>
          <tr>
            <th>Usuario</th>
            <th>Email</th>
            <th>Estado</th>
            <th>Permisos</th>
            <th>Empresas</th>
            <th class="text-end">Acción</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!$rows): ?>
            <tr><td colspan="6" class="text-body-secondary">Sin resultados.</td></tr>
          <?php else: ?>
            <?php foreach ($rows as $r): ?>
              <tr>
                <td class="fw-semibold"><?= htmlspecialchars(trim(($r['nombres'] ?? '').' '.($r['apellidos'] ?? '')), ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars((string)($r['email'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                <td><span class="badge <?= ($r['estado'] ?? '')==='ACTIVO'?'text-bg-success':'text-bg-secondary' ?>"><?= htmlspecialchars((string)$r['estado']) ?></span></td>
                <td><?= (int)($r['scopes_activos'] ?? 0) ?></td>
                <td class="text-body-secondary"><?= htmlspecialchars((string)($r['empresas'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                <td class="text-end">
                  <a class="btn btn-sm btn-outline-primary" href="<?= $__panelPrefix ?>/usuarios/<?= (int)$r['id'] ?>">
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
