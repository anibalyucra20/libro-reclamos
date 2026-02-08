<h1>Buscar seguimiento</h1>
<?php if (!empty($error)): ?>
  <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="POST" action="/seguimiento/buscar">
  <div class="row">
    <div class="col-md-4">
      <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">
      <label>Código del reclamo *</label><br>
      <input name="codigo_reclamo" class="form-control" required placeholder="2026-000123"><br><br>
    </div>
    <div class="col-md-4">
      <label>Tipo de documento *</label><br>
      <select name="doc_tipo" class="form-control" required>
        <option value="DNI">DNI</option>
        <option value="CE">CE</option>
        <option value="PAS">Pasaporte</option>
        <option value="RUC">RUC</option>
        <option value="OTRO">Otro</option>
      </select><br><br>
    </div>
    <div class="col-md-4">
      <label>Número de documento *</label><br>
      <input name="doc_num" required class="form-control"><br><br>
    </div>
    <button type="submit" class="btn btn-success"><i class="bi bi-search me-1"></i> Buscar</button>
  </div>
</form>