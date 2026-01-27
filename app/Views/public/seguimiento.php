<h1>Seguimiento de Reclamo</h1>

<p><strong>Código:</strong> <?= htmlspecialchars($reclamo['codigo_reclamo']) ?></p>
<p><strong>Estado:</strong> <?= htmlspecialchars($reclamo['estado']) ?></p>
<p><strong>Registrado:</strong> <?= htmlspecialchars($reclamo['fecha_registro']) ?></p>
<p><strong>Vence:</strong> <?= htmlspecialchars($reclamo['fecha_vencimiento_respuesta']) ?></p>

<hr>

<h3>Respuestas</h3>
<?php if (empty($respuestas)): ?>
  <p>Aún no hay respuesta registrada.</p>
<?php else: ?>
  <?php foreach ($respuestas as $r): ?>
    <div style="border:1px solid #ddd;padding:8px;margin:8px 0;">
      <div><small><?= htmlspecialchars($r['fecha_respuesta']) ?></small></div>
      <div><?= nl2br(htmlspecialchars($r['respuesta'])) ?></div>
    </div>
  <?php endforeach; ?>
<?php endif; ?>

<hr>

<h3>Historial</h3>
<?php if (empty($eventos)): ?>
  <p>Sin eventos.</p>
<?php else: ?>
  <ul>
    <?php foreach ($eventos as $ev): ?>
      <li><?= htmlspecialchars($ev['created_at']) ?> — <?= htmlspecialchars($ev['evento']) ?></li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>
