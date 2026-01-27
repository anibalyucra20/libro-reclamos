<?php
$empresa = $reclamo['empresa_nombre_comercial'] ?: $reclamo['empresa_razon_social'];
?>
<div style="max-width:900px;margin:0 auto;">
    <h1>Constancia - Libro de Reclamaciones</h1>
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

    <hr>

    <h3>Consumidor</h3>
    <p><?= htmlspecialchars(trim($reclamo['consumidor_nombres'] . ' ' . $reclamo['consumidor_apellidos'])) ?></p>
    <p><?= htmlspecialchars($reclamo['consumidor_doc_tipo'] . ': ' . $reclamo['consumidor_doc_num']) ?></p>

    <h3>Bien contratado</h3>
    <p><?= htmlspecialchars($reclamo['bien_contratado']) ?></p>

    <h3>Detalle</h3>
    <p><?= nl2br(htmlspecialchars($reclamo['detalle'])) ?></p>

    <h3>Pedido</h3>
    <p><?= nl2br(htmlspecialchars($reclamo['pedido'])) ?></p>

    <hr>
    <h3>¿Cómo hacer seguimiento?</h3>
    <p>
        <?php $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]"; ?>
        Ingresa a: <strong><?= $url ?>/seguimiento</strong> y coloca:
        <strong>código del reclamo</strong> y <strong>tu documento</strong>.
    </p>
    <p>
        También puedes usar este enlace directo:
        
        <strong><?= $url ?>/seguimiento/<?= htmlspecialchars($token) ?></strong>
    </p>
    <button onclick="window.print()">Imprimir</button>
    <p><a href="/constancia/<?= htmlspecialchars($token) ?>/pdf">Descargar PDF</a></p>

</div>