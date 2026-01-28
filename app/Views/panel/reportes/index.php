<?php
$desde = (string)($desde ?? date('Y-m-01'));
$hasta = (string)($hasta ?? date('Y-m-d'));
$estado = (string)($estado ?? '');
$tipo = (string)($tipo ?? '');
?>

<div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-3">
  <div>
    <h1 class="h3 fw-bold mb-1">Reportes</h1>
    <div class="text-body-secondary">Resumen y métricas por rango.</div>
  </div>

  <form method="GET" action="/reportes" class="d-flex flex-column flex-sm-row gap-2 align-items-sm-end">
    <div>
      <label class="form-label mb-1">Desde</label>
      <input class="form-control" type="date" name="desde" value="<?= htmlspecialchars($desde, ENT_QUOTES, 'UTF-8') ?>">
    </div>
    <div>
      <label class="form-label mb-1">Hasta</label>
      <input class="form-control" type="date" name="hasta" value="<?= htmlspecialchars($hasta, ENT_QUOTES, 'UTF-8') ?>">
    </div>
    <div>
      <label class="form-label mb-1">Estado</label>
      <select class="form-select" name="estado" style="min-width: 180px;">
        <option value="" <?= $estado===''?'selected':'' ?>>Todos</option>
        <?php foreach (['REGISTRADO','EN_PROCESO','RESPONDIDO','CERRADO'] as $st): ?>
          <option value="<?= $st ?>" <?= $estado===$st?'selected':'' ?>><?= $st ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div>
      <label class="form-label mb-1">Tipo</label>
      <select class="form-select" name="tipo" style="min-width: 160px;">
        <option value="" <?= $tipo===''?'selected':'' ?>>Todos</option>
        <option value="RECLAMO" <?= $tipo==='RECLAMO'?'selected':'' ?>>RECLAMO</option>
        <option value="QUEJA" <?= $tipo==='QUEJA'?'selected':'' ?>>QUEJA</option>
      </select>
    </div>

    <div class="d-flex gap-2">
      <button class="btn btn-primary" type="submit">
        <i class="bi bi-funnel me-1"></i> Aplicar
      </button>
      <a class="btn btn-outline-secondary" href="/reportes">
        <i class="bi bi-x-circle me-1"></i> Limpiar
      </a>
    </div>
  </form>
</div>

<div class="row g-3" id="cards">
  <div class="col-12">
    <div class="text-body-secondary">Cargando...</div>
  </div>
</div>

<script>
(async () => {
  const params = new URLSearchParams(window.location.search);
  const res = await fetch('/reportes/data?' + params.toString(), {headers: {'Accept':'application/json'}});
  const data = await res.json();

  const k = data.kpis || {};
  const op = data.operativo_hoy || {};

  const cards = `
    <div class="col-12 col-lg-3"><div class="card border-0 shadow-sm"><div class="card-body">
      <div class="text-body-secondary small">Total (rango)</div><div class="h3 fw-bold mb-0">${k.total ?? 0}</div>
    </div></div></div>

    <div class="col-12 col-lg-3"><div class="card border-0 shadow-sm"><div class="card-body">
      <div class="text-body-secondary small">Abiertos (rango)</div><div class="h3 fw-bold mb-0">${k.abiertos ?? 0}</div>
    </div></div></div>

    <div class="col-12 col-lg-3"><div class="card border-0 shadow-sm"><div class="card-body">
      <div class="text-body-secondary small">Respondidos (rango)</div><div class="h3 fw-bold mb-0">${k.respondidos ?? 0}</div>
    </div></div></div>

    <div class="col-12 col-lg-3"><div class="card border-0 shadow-sm"><div class="card-body">
      <div class="text-body-secondary small">Cerrados (rango)</div><div class="h3 fw-bold mb-0">${k.cerrados ?? 0}</div>
    </div></div></div>

    <div class="col-12"><div class="card border-0 shadow-sm"><div class="card-body">
      <div class="fw-bold mb-2">Operativo (hoy)</div>
      <div class="d-flex flex-wrap gap-2">
        <span class="badge text-bg-danger">Vencidos: ${op.vencidos ?? 0}</span>
        <span class="badge text-bg-warning">Vencen hoy: ${op.vencen_hoy ?? 0}</span>
        <span class="badge text-bg-primary">Por vencer (3d): ${op.por_vencer_3 ?? 0}</span>
      </div>
    </div></div></div>
  `;

  document.getElementById('cards').innerHTML = cards;
})();
</script>
