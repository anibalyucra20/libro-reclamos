<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\DB;
use App\Services\BusinessDays;
use DateTimeImmutable;
use Exception;

final class Reclamo extends BaseModel
{
  /**
   * Crea un reclamo de forma transaccional:
   * - Bloquea/actualiza correlativo por establecimiento/año
   * - Inserta reclamo
   * - Inserta evento "CREADO"
   *
   * Retorna array con ['id','codigo_reclamo','evidencia_token','fecha_vencimiento_respuesta'].
   */
  public static function createPublic(array $data): array
  {
    $pdo = DB::pdo();
    $pdo->beginTransaction();

    try {
      $empresaId = (int)$data['empresa_id'];
      $estabId   = (int)$data['establecimiento_id'];

      $anio = (int)(new DateTimeImmutable('now'))->format('Y');

      // 1) Obtener correlativo con lock
      $sqlSel = "SELECT id, ultimo_numero
                 FROM correlativos
                 WHERE establecimiento_id = :estab AND anio = :anio
                 FOR UPDATE";
      $st = $pdo->prepare($sqlSel);
      $st->execute(['estab' => $estabId, 'anio' => $anio]);
      $row = $st->fetch();

      if (!$row) {
        // crear fila correlativo
        $pdo->prepare("INSERT INTO correlativos (establecimiento_id, anio, ultimo_numero)
                       VALUES (:estab, :anio, 0)")
          ->execute(['estab' => $estabId, 'anio' => $anio]);

        // volver a leer con lock
        $st = $pdo->prepare($sqlSel);
        $st->execute(['estab' => $estabId, 'anio' => $anio]);
        $row = $st->fetch();
      }

      $next = ((int)$row['ultimo_numero']) + 1;

      $pdo->prepare("UPDATE correlativos
                     SET ultimo_numero = :next
                     WHERE id = :id")
        ->execute(['next' => $next, 'id' => (int)$row['id']]);

      // 2) Generar código visible (ejemplo: "2026-000001")
      // Si quieres incluir código_identificacion del local, lo hacemos en Parte 4.
      $codigoReclamo = sprintf('%d-%06d', $anio, $next);

      // 3) Calcular vencimiento: 15 días hábiles
      $start = new DateTimeImmutable('now');
      $venc  = BusinessDays::addBusinessDays($start, 15)->format('Y-m-d');

      // 4) Token evidencia / seguimiento
      $token = bin2hex(random_bytes(16)); // 32 chars

      // 5) Insert reclamo
      $sqlIns = "INSERT INTO reclamos (
          empresa_id, establecimiento_id, codigo_reclamo, correlativo_num, anio,
          tipo, fecha_registro, fecha_vencimiento_respuesta,
          consumidor_nombres, consumidor_apellidos, consumidor_doc_tipo, consumidor_doc_num,
          consumidor_email, consumidor_telefono, consumidor_direccion,
          bien_contratado, monto_reclamado, detalle, pedido,
          canal, estado, acepta_declaracion, evidencia_token, created_ip, user_agent
        ) VALUES (
          :empresa_id, :establecimiento_id, :codigo_reclamo, :correlativo_num, :anio,
          :tipo, NOW(), :fecha_vencimiento,
          :nombres, :apellidos, :doc_tipo, :doc_num,
          :email, :telefono, :direccion,
          :bien, :monto, :detalle, :pedido,
          'WEB', 'REGISTRADO', :acepta, :token, :ip, :ua
        )";

      $ipBin = self::ipToBinary($data['created_ip'] ?? null);

      $pdo->prepare($sqlIns)->execute([
        'empresa_id' => $empresaId,
        'establecimiento_id' => $estabId,
        'codigo_reclamo' => $codigoReclamo,
        'correlativo_num' => $next,
        'anio' => $anio,
        'tipo' => $data['tipo'],
        'fecha_vencimiento' => $venc,
        'nombres' => $data['consumidor_nombres'],
        'apellidos' => $data['consumidor_apellidos'] ?? null,
        'doc_tipo' => $data['consumidor_doc_tipo'],
        'doc_num' => $data['consumidor_doc_num'],
        'email' => $data['consumidor_email'] ?? null,
        'telefono' => $data['consumidor_telefono'] ?? null,
        'direccion' => $data['consumidor_direccion'] ?? null,
        'bien' => $data['bien_contratado'],
        'monto' => $data['monto_reclamado'] ?? null,
        'detalle' => $data['detalle'],
        'pedido' => $data['pedido'],
        'acepta' => (int)$data['acepta_declaracion'],
        'token' => $token,
        'ip' => $ipBin,
        'ua' => $data['user_agent'] ?? null,
      ]);

      $reclamoId = (int)$pdo->lastInsertId();

      // 6) Evento auditoría
      $pdo->prepare("INSERT INTO reclamo_eventos (reclamo_id, actor_usuario_id, evento, metadata_json, ip, user_agent)
                     VALUES (:rid, NULL, 'CREADO', JSON_OBJECT('canal','WEB'), :ip, :ua)")
        ->execute(['rid' => $reclamoId, 'ip' => $ipBin, 'ua' => $data['user_agent'] ?? null]);

      $pdo->commit();

      return [
        'id' => $reclamoId,
        'codigo_reclamo' => $codigoReclamo,
        'evidencia_token' => $token,
        'fecha_vencimiento_respuesta' => $venc,
      ];
    } catch (Exception $e) {
      $pdo->rollBack();
      throw $e;
    }
  }

  private static function ipToBinary(?string $ip): ?string
  {
    if (!$ip) return null;
    $bin = @inet_pton($ip);
    return $bin === false ? null : $bin;
  }


  public static function findByTokenInEmpresa(string $token, int $empresaId): ?array
  {
    $sql = "SELECT r.id, r.codigo_reclamo, r.tipo, r.estado, r.fecha_registro, r.fecha_vencimiento_respuesta,
                 r.consumidor_nombres, r.consumidor_apellidos, r.consumidor_doc_tipo, r.consumidor_doc_num,
                 r.consumidor_email, r.consumidor_telefono, r.consumidor_direccion,
                 r.bien_contratado, r.monto_reclamado, r.detalle, r.pedido,
                 e.nombre AS establecimiento_nombre, e.direccion AS establecimiento_direccion,
                 em.razon_social AS empresa_razon_social, em.nombre_comercial AS empresa_nombre_comercial, em.ruc AS empresa_ruc
          FROM reclamos r
          JOIN establecimientos e ON e.id = r.establecimiento_id
          JOIN empresas em ON em.id = r.empresa_id
          WHERE r.evidencia_token = :token
            AND r.empresa_id = :empresa_id AND (r.evidencia_token_expires_at IS NULL OR r.evidencia_token_expires_at >= NOW())
          LIMIT 1";
    $st = self::pdo()->prepare($sql);
    $st->execute(['token' => $token, 'empresa_id' => $empresaId]);
    $row = $st->fetch();
    return $row ?: null;
  }

  public static function respuestasByReclamoId(int $reclamoId): array
  {
    $sql = "SELECT rr.respuesta, rr.fecha_respuesta
          FROM reclamo_respuestas rr
          WHERE rr.reclamo_id = :rid
          ORDER BY rr.fecha_respuesta ASC";
    $st = self::pdo()->prepare($sql);
    $st->execute(['rid' => $reclamoId]);
    return $st->fetchAll() ?: [];
  }

  public static function eventosByReclamoId(int $reclamoId): array
  {
    $sql = "SELECT evento, created_at
          FROM reclamo_eventos
          WHERE reclamo_id = :rid
          ORDER BY created_at ASC";
    $st = self::pdo()->prepare($sql);
    $st->execute(['rid' => $reclamoId]);
    return $st->fetchAll() ?: [];
  }

  public static function tokenByCodigoAndDocInEmpresa(string $codigo, string $docTipo, string $docNum, int $empresaId): ?string
  {
    $sql = "SELECT evidencia_token
          FROM reclamos
          WHERE empresa_id = :empresa_id
            AND codigo_reclamo = :codigo
            AND consumidor_doc_tipo = :doc_tipo
            AND consumidor_doc_num = :doc_num
          LIMIT 1";
    $st = self::pdo()->prepare($sql);
    $st->execute([
      'empresa_id' => $empresaId,
      'codigo' => $codigo,
      'doc_tipo' => $docTipo,
      'doc_num' => $docNum
    ]);
    $row = $st->fetch();
    return $row['evidencia_token'] ?? null;
  }
}
