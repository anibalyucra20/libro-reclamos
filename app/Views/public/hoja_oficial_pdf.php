<?php
$r = $reclamo ?? [];
$resp = $respuesta ?? null;

function h($v): string {
  return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

$empresa = trim((string)($r['empresa_razon_social'] ?? ''));
$ruc = trim((string)($r['empresa_ruc'] ?? ''));
$dirProveedor = trim((string)($r['establecimiento_direccion'] ?? ''));

$fechaReg = (string)($r['fecha_registro'] ?? '');
$fecha = $fechaReg ? date('d/m/Y', strtotime($fechaReg)) : '';

$anio = (int)($r['anio'] ?? 0);
$corr = (int)($r['correlativo_num'] ?? 0);
$hojaN = ($anio > 0 && $corr > 0) ? sprintf('%d-%06d', $anio, $corr) : (string)($r['codigo_reclamo'] ?? '');

$consTipo = strtoupper((string)($r['consumidor_tipo'] ?? 'NATURAL'));
$esMenor = ((int)($r['consumidor_menor'] ?? 0) === 1);

$nombreConsumidor = ($consTipo === 'JURIDICA')
  ? trim((string)($r['consumidor_nombres'] ?? ''))
  : trim(trim((string)($r['consumidor_nombres'] ?? '')) . ' ' . trim((string)($r['consumidor_apellidos'] ?? '')));

$docTipo = (string)($r['consumidor_doc_tipo'] ?? '');
$docNum  = (string)($r['consumidor_doc_num'] ?? '');

$tel = (string)($r['consumidor_telefono'] ?? '');
$email = (string)($r['consumidor_email'] ?? '');
$dir = (string)($r['consumidor_direccion'] ?? '');

$tutor = (string)($r['tutor_nombres'] ?? '');

$bienTipo = strtoupper((string)($r['bien_tipo'] ?? ''));
$monto = $r['monto_reclamado'];
$montoTxt = ($monto === null || $monto === '') ? '' : number_format((float)$monto, 2, '.', '');

$bienDesc = (string)($r['bien_contratado'] ?? '');

$tipo = strtoupper((string)($r['tipo'] ?? 'RECLAMO'));
$detalle = (string)($r['detalle'] ?? '');
$pedido = (string)($r['pedido'] ?? '');

$obsProv = $resp ? (string)($resp['respuesta'] ?? '') : '';
$fechaResp = ($resp && !empty($resp['fecha_respuesta'])) ? date('d/m/Y', strtotime((string)$resp['fecha_respuesta'])) : '';
?>

<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <style>
    @page { margin: 16mm 14mm; }
    body { font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #111; }

    .t { width: 100%; border-collapse: collapse; }
    .t, .t td, .t th { border: 1px solid #000; }
    .t td, .t th { padding: 6px 6px; vertical-align: top; }

    .center { text-align: center; }
    .right { text-align: right; }
    .bold { font-weight: 700; }
    .xs { font-size: 9px; }
    .muted { color: #333; }
    .box { border: 1px solid #000; padding: 1px 6px; display: inline-block; min-width: 14px; text-align: center; }

    .row2 { display: table; width: 100%; }
    .cell { display: table-cell; vertical-align: top; }
    .w50 { width: 50%; }
    .w20 { width: 20%; }
    .w15 { width: 15%; }

    .mt6 { margin-top: 6px; }
    .mt10 { margin-top: 10px; }
    .nowrap { white-space: nowrap; }
  </style>
</head>

<body>

  <!-- CABECERA -->
  <table class="t">
    <tr>
      <td class="center bold" style="width: 70%;">
        HOJA DE RECLAMACIÓN
        <div class="xs muted">Libro de Reclamaciones</div>
      </td>
      <td style="width: 30%;">
        <div class="row2">
          <div class="cell w50 bold">Hoja N°</div>
          <div class="cell w50 right"><?= h($hojaN) ?></div>
        </div>
        <div class="row2 mt6">
          <div class="cell w50 bold">Fecha</div>
          <div class="cell w50 right"><?= h($fecha) ?></div>
        </div>
      </td>
    </tr>
    <tr>
      <td colspan="2">
        <div class="row2">
          <div class="cell w15 bold">Proveedor:</div>
          <div class="cell"><?= h($empresa) ?></div>
        </div>
        <div class="row2 mt6">
          <div class="cell w15 bold">RUC:</div>
          <div class="cell w20"><?= h($ruc) ?></div>
          <div class="cell w15 bold">Dirección:</div>
          <div class="cell"><?= h($dirProveedor) ?></div>
        </div>
      </td>
    </tr>
  </table>

  <!-- 1 -->
  <table class="t mt10">
    <tr>
      <th class="center bold" colspan="4">1. IDENTIFICACIÓN DEL CONSUMIDOR RECLAMANTE</th>
    </tr>
    <tr>
      <td class="bold" style="width: 18%;">Nombre / Razón social</td>
      <td style="width: 52%;"><?= h($nombreConsumidor) ?></td>
      <td class="bold" style="width: 15%;">Doc.</td>
      <td style="width: 15%;" class="nowrap"><?= h($docTipo) ?> <?= h($docNum) ?></td>
    </tr>
    <tr>
      <td class="bold">Domicilio</td>
      <td><?= h($dir) ?></td>
      <td class="bold">Teléfono</td>
      <td><?= h($tel) ?></td>
    </tr>
    <tr>
      <td class="bold">E-mail</td>
      <td><?= h($email) ?></td>
      <td class="bold">Padre/Madre/Tutor</td>
      <td><?= $esMenor ? h($tutor) : '' ?></td>
    </tr>
  </table>

  <!-- 2 -->
  <table class="t mt10">
    <tr>
      <th class="center bold" colspan="4">2. IDENTIFICACIÓN DEL BIEN CONTRATADO</th>
    </tr>
    <tr>
      <td class="bold" style="width: 18%;">Bien</td>
      <td style="width: 52%;">
        <span class="box"><?= ($bienTipo === 'PRODUCTO') ? 'X' : '&nbsp;' ?></span> Producto
        &nbsp;&nbsp;&nbsp;
        <span class="box"><?= ($bienTipo === 'SERVICIO') ? 'X' : '&nbsp;' ?></span> Servicio
      </td>
      <td class="bold" style="width: 15%;">Monto reclamado</td>
      <td style="width: 15%;" class="right"><?= h($montoTxt) ?></td>
    </tr>
    <tr>
      <td class="bold">Descripción</td>
      <td colspan="3"><?= nl2br(h($bienDesc)) ?></td>
    </tr>
  </table>

  <!-- 3 -->
  <table class="t mt10">
    <tr>
      <th class="center bold" colspan="4">3. DETALLE DE LA RECLAMACIÓN Y PEDIDO DEL CONSUMIDOR</th>
    </tr>
    <tr>
      <td class="bold" style="width: 18%;">Tipo</td>
      <td colspan="3">
        <span class="box"><?= ($tipo === 'RECLAMO') ? 'X' : '&nbsp;' ?></span> Reclamo
        &nbsp;&nbsp;&nbsp;
        <span class="box"><?= ($tipo === 'QUEJA') ? 'X' : '&nbsp;' ?></span> Queja
      </td>
    </tr>
    <tr>
      <td class="bold">Detalle</td>
      <td colspan="3" style="height: 110px;"><?= nl2br(h($detalle)) ?></td>
    </tr>
    <tr>
      <td class="bold">Pedido</td>
      <td colspan="3" style="height: 80px;"><?= nl2br(h($pedido)) ?></td>
    </tr>
  </table>

  <!-- 4 -->
  <table class="t mt10">
    <tr>
      <th class="center bold" colspan="4">4. OBSERVACIONES Y ACCIONES ADOPTADAS POR EL PROVEEDOR</th>
    </tr>
    <tr>
      <td class="bold" style="width: 18%;">Observaciones</td>
      <td colspan="3" style="height: 110px;"><?= nl2br(h($obsProv)) ?></td>
    </tr>
    <tr>
      <td class="bold">Fecha de comunicación</td>
      <td colspan="3"><?= h($fechaResp) ?></td>
    </tr>
  </table>

  <div class="mt10 xs muted">
    * La formulación del reclamo no impide acudir a otras vías de solución de controversias ni es requisito previo para interponer una denuncia ante INDECOPI.
    <br>
    * El proveedor debe dar respuesta en un plazo no mayor a 15 días hábiles.
  </div>

</body>
</html>
