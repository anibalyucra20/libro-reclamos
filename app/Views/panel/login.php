<h1>Login Panel</h1>

<?php if (!empty($error)): ?>
  <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<form method="POST" action="/login">
  <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">
  <label>Email</label><br>
  <input type="email" name="email" required><br><br>

  <label>Password</label><br>
  <input type="password" name="password" required><br><br>

  <button type="submit">Ingresar</button>
</form>
