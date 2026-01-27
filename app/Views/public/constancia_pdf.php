<?php
$empresa = $reclamo['empresa_nombre_comercial'] ?: $reclamo['empresa_razon_social'];
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
    h1 { font-size: 18px; margin: 0 0 8px; }
    h3 { margin: 14px 0 6px; }
    .box { border: 1px solid #333; padding: 10px; }
    .muted { color: #444; }
    hr { border: 0; border-top: 1px solid #aaa; margin: 10px 0; }
  </style>
</head>
<body>
  <h1>Constancia - Libro de Reclamaciones</h1>
  <div class="box">
    <p><strong>Empresa:</strong> <?= htmlspecialchars($empresa) ?></p>
    <p><strong>RUC:</strong> <?= htmlspecialchars($reclamo['empresa_ruc']) ?></p>
    <hr>
    <p><strong>Establecimiento:</strong> <?= htmlspecialchars($reclamo['establecimiento_nombre']) ?></p>
    <p><strong>Dirección:</strong> <?= htmlspecialchars($reclamo['establecimiento_direccion']) ?></p>
    <hr>
    <p><strong>Código:</strong> <?= htmlspecialchars($reclamo['codigo_reclamo']) ?></p>
    <p><strong>Tipo:</strong> <?= htmlspecialchars($reclamo['tipo']) ?></p>
    <p><strong>Fecha de registro:</strong> <?= htmlspecialchars($reclamo['fecha_registro']) ?></p>
    <p><strong>Fecha máxima de respuesta:</strong> <?= htmlspecialchars($reclamo['fecha_vencimiento_respuesta']) ?> (15 días hábiles)</p>
  </div>

  <h3>Consumidor</h3>
  <p><?= htmlspecialchars(trim($reclamo['consumidor_nombres'].' '.$reclamo['consumidor_apellidos'])) ?></p>
  <p><?= htmlspecialchars($reclamo['consumidor_doc_tipo'].': '.$reclamo['consumidor_doc_num']) ?></p>

  <h3>Bien contratado</h3>
  <p><?= htmlspecialchars($reclamo['bien_contratado']) ?></p>

  <h3>Detalle</h3>
  <p><?= nl2br(htmlspecialchars($reclamo['detalle'])) ?></p>

  <h3>Pedido</h3>
  <p><?= nl2br(htmlspecialchars($reclamo['pedido'])) ?></p>

  <hr>
  <p class="muted">
    Para seguimiento ingrese a la opción "Seguimiento" de este mismo sitio, con su código y documento.
  </p>
</body>
</html>
