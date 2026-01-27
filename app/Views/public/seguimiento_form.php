<h1>Buscar seguimiento</h1>
<?php if (!empty($error)): ?>
  <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>
<form method="POST" action="/seguimiento/buscar">
  <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">
  <label>Código del reclamo *</label><br>
  <input name="codigo_reclamo" required placeholder="2026-000123"><br><br>
  <label>Tipo de documento *</label><br>
  <select name="doc_tipo" required>
    <option value="DNI">DNI</option>
    <option value="CE">CE</option>
    <option value="PAS">Pasaporte</option>
    <option value="RUC">RUC</option>
    <option value="OTRO">Otro</option>
  </select><br><br>
  <label>Número de documento *</label><br>
  <input name="doc_num" required><br><br>
  <button type="submit">Buscar</button>
</form>
