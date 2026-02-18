<?php
$__panelPrefix = rtrim((string)($tenant['panel_prefix'] ?? '/panel'), '/');
if ($__panelPrefix === '') $__panelPrefix = '/panel';
?>
<h1>Login Panel</h1>

<?php if (!empty($error)): ?>
  <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="POST" action="<?= $__panelPrefix ?>/login">
  <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">
  <div class="form-group">
    <label>Email</label><br>
    <input type="email" name="email" class="form-control col-md-4" required><br><br>
  </div>
  <div class="form-group">
    <label>Password</label><br>
    <input type="password" name="password" class="form-control col-md-4" required><br><br>
  </div>
  <div class="form-group">
    <button type="submit" class="btn btn-success">Ingresar</button>
  </div>
</form>
