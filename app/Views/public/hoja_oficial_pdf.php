<?php
$r = $reclamo ?? [];
$resp = $respuesta ?? null;

function h($v): string
{
  return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

$empresa = trim((string)($r['empresa_razon_social'] ?? ''));
$ruc = trim((string)($r['empresa_ruc'] ?? ''));
$dirProveedor = trim((string)($r['establecimiento_direccion'] ?? ''));

$fechaReg = (string)($r['fecha_registro'] ?? '');
$fecha = $fechaReg ? $fechaReg : '';

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
$tutor = '';
if ($esMenor) {
  $tutor = (string)($r['tutor_nombres'] ?? '');
}
if ($r['consumidor_tipo'] == 'JURIDICA') {
  $tutor = (string)($r['contacto_nombres'] ?? '');
}

$bienTipo = strtoupper((string)($r['bien_tipo'] ?? ''));
$monto = $r['monto_reclamado'];
$montoTxt = ($monto === null || $monto === '') ? '' : number_format((float)$monto, 2, '.', '');

$bienDesc = (string)($r['bien_contratado'] ?? '');

$tipo = strtoupper((string)($r['tipo'] ?? 'RECLAMO'));
$detalle = (string)($r['detalle'] ?? '');
$pedido = (string)($r['pedido'] ?? '');

$obsProv = $resp ? (string)($resp['respuesta'] ?? '') : '';
$fechaResp = ($resp && !empty($resp['fecha_respuesta'])) ? $resp['fecha_respuesta'] : '';
?>

<!doctype html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <style>
    @page {
      margin: 16mm 14mm;
    }

    body {
      font-family: Arial, Helvetica, sans-serif;
      font-size: 11px;
      color: #111;
    }

    .t {
      width: 100%;
      border-collapse: collapse;
    }

    .tabla {
      width: 100%;
      border-collapse: collapse;
    }

    .t,
    .t td,
    .t th {
      border: 1px solid #000;
    }

    .t td,
    .t th {
      padding: 6px 6px;
      vertical-align: top;
    }

    .center {
      text-align: center;
    }

    .left {
      text-align: left;
    }

    .right {
      text-align: right;
    }

    .bold {
      font-weight: 700;
    }

    .xs {
      font-size: 9px;
    }

    .muted {
      color: #333;
    }

    .box {
      border: 1px solid #000;
      padding: 1px 6px;
      display: inline-block;
      min-width: 14px;
      text-align: center;
    }

    .row2 {
      display: table;
      width: 100%;
    }

    .cell {
      display: table-cell;
      vertical-align: top;
    }

    .w50 {
      width: 50%;
    }

    .w20 {
      width: 20%;
    }

    .w15 {
      width: 15%;
    }

    .mt6 {
      margin-top: 6px;
    }

    .mt10 {
      margin-top: 10px;
    }

    .nowrap {
      white-space: nowrap;
    }

    .color_ff {
      background-color: rgb(203, 203, 203);
    }
  </style>
</head>

<body>

  <!-- CABECERA -->
  <table class="tabla">
    <tr>
      <table class="t">
        <tr>
          <td colspan="4" class="color_ff center bold" style="width: 50%;">
            LIBRO DE RECLAMACIONES
          </td>
          <td rowspan="2" class="center" style="width: 50%;">
            <b>HOJA DE RECLAMACIÓN</b><br>
            Nro. <?= h($hojaN) ?>
          </td>
        </tr>
        <tr>
          <?php
          $mes = date('m', strtotime($fechaReg));
          $dia = date('d', strtotime($fechaReg));
          $anio = date('Y', strtotime($fechaReg));
          ?>
          <td class="center">FECHA</td>
          <td class="center"><?= $dia ?></td>
          <td class="center"><?= $mes ?></td>
          <td class="center"><?= $anio ?></td>
        </tr>
      </table>
    </tr>
    <tr>
      <table class="t" style="border-top: none;">
        <tr>
          <td colspan="5">
            <div class="row2">
              <div class="cell w15 bold">Proveedor:</div>
              <div class="cell"><?= h($empresa) ?></div>
            </div>
            <div class="row2 mt6">
              <div class="cell w15 bold">RUC:</div>
              <div class="cell"><?= h($ruc) ?></div>
            </div>
            <div class="row2 mt6">
              <div class="cell w15 bold">Dirección:</div>
              <div class="cell"><?= h($dirProveedor) ?></div>
            </div>
          </td>
        </tr>
      </table>
    </tr>
  </table>
  <!-- 1 -->
  <table class="t">
    <tr>
      <th class="color_ff bold left" colspan="3">1. IDENTIFICACIÓN DEL CONSUMIDOR RECLAMANTE</th>
    </tr>
    <tr>
      <td colspan="3"><b>Nombre / Razón social:</b> <?= h($nombreConsumidor) ?></td>
    </tr>
    <tr>
      <td colspan="3"><b>Domicilio:</b> <?= h($dir) ?></td>
    </tr>
    <tr>
      <td><b><?= h($docTipo) ?>:</b> <?= h($docNum) ?></td>
      <td><b>Teléfono: </b> <?= h($tel) ?></td>
      <td><b>E-mail:</b> <?= h($email) ?></td>
    </tr>
    <tr>
      <td colspan="3"><b>Padre/Madre/Contacto:</b> <?= $tutor ?></td>
    </tr>
  </table>

  <!-- 2 -->
  <table class="t">
    <tr>
      <th class="color_ff bold left" style="width: 60%;">2. IDENTIFICACIÓN DEL BIEN CONTRATADO</th>
      <th class="bold">PRODUCTO</th>
      <th class="bold" style="width: 5%;"><?= ($bienTipo === 'PRODUCTO') ? 'X' : '&nbsp;' ?></th>
      <th class="bold">SERVICIO</th>
      <th class="bold" style="width: 5%;"><?= ($bienTipo === 'SERVICIO') ? 'X' : '&nbsp;' ?></th>
    </tr>
    <tr>
      <td colspan="5"><b>Monto reclamado:</b> S/. <?= h($montoTxt) ?> <br> <b>Descripción: </b><?= nl2br(h($bienDesc)) ?></td>
    </tr>
  </table>
  <!-- 3 -->
  <table class="t">
    <tr>
      <th class="color_ff bold left" style="width: 60%;">3. DETALLE DE LA RECLAMACIÓN Y PEDIDO DEL CONSUMIDOR</th>
      <th class="bold">RECLAMO</th>
      <th class="bold" style="width: 5%;"><?= ($tipo === 'RECLAMO') ? 'X' : '&nbsp;' ?></th>
      <th class="bold">QUEJA</th>
      <th class="bold" style="width: 5%;"><?= ($tipo === 'QUEJA') ? 'X' : '&nbsp;' ?></th>
    </tr>
    <tr>
      <td colspan="5"><b>Detalle: </b><?= nl2br(h($detalle)) ?></td>
    </tr>
    <tr>
      <td colspan="2" rowspan="2"><b>Pedido: </b><?= nl2br(h($pedido)) ?></td>
      <td colspan="3"><br><br></td>
    </tr>
    <tr>
      <td colspan="3" class="center">FIRMA DEL CONSUMIDOR</td>
    </tr>
  </table>
  <!-- 4 -->
  <table class="t">
    <tr>
      <th class="color_ff bold left" colspan="6">4. OBSERVACIONES Y ACCIONES ADOPTADAS POR EL PROVEEDOR</th>
    </tr>
    <tr>
      <td colspan="2" class="bold">FECHA DE COMUNICACION DE LA RESPUESTA:</td>
      <?php
      if ($resp) {
        $mes_r = date('m', strtotime($fechaResp));
        $dia_r = date('d', strtotime($fechaResp));
        $anio_r = date('Y', strtotime($fechaResp));
      } else {
        $mes_r = "-";
        $dia_r = "-";
        $anio_r = "-";
      }
      ?>
      <td><?= $dia_r ?></td>
      <td><?= $mes_r ?></td>
      <td><?= $anio_r ?></td>
      <td></td>
    </tr>
    <tr>
      <td colspan="5"><b>Respuesta:</b> <?= nl2br(h($obsProv)) ?></td>
      <td class="center">FIRMA DEL PROVEEDOR</td>
    </tr>
    <tr>
      <td style="width: 40%;"><b>RECLAMO: </b>Disconformidad relacionada a los productos o servicios.</td>
      <td colspan="5"><b>QUEJA: </b>Disconformidad no relacionada a los productos o servicios; o, malestar o descontento respecto a la atención al público.</td>
    </tr>
  </table>

  <div class="mt10 xs muted">
    * La formulación del reclamo no impide acudir a otras vías de solución de controversias ni es requisito previo para interponer una denuncia ante INDECOPI.
    <br>
    * El proveedor debe dar respuesta en un plazo no mayor a 15 días hábiles.
  </div>

</body>

</html>