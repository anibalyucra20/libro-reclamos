<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\DB;
use App\Services\BusinessDays;
use DateTimeImmutable;
use Exception;

final class Reclamo extends BaseModel
{
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
        $pdo->prepare("INSERT INTO correlativos (establecimiento_id, anio, ultimo_numero)
                       VALUES (:estab, :anio, 0)")
          ->execute(['estab' => $estabId, 'anio' => $anio]);

        $st = $pdo->prepare($sqlSel);
        $st->execute(['estab' => $estabId, 'anio' => $anio]);
        $row = $st->fetch();
      }

      $next = ((int)$row['ultimo_numero']) + 1;

      $pdo->prepare("UPDATE correlativos
                     SET ultimo_numero = :next
                     WHERE id = :id")
        ->execute(['next' => $next, 'id' => (int)$row['id']]);

      $codigoReclamo = sprintf('%d-%06d', $anio, $next);

      // 15 días hábiles
      $start = new DateTimeImmutable('now');
      $venc  = BusinessDays::addBusinessDays($start, 15)->format('Y-m-d');

      // token evidencia / seguimiento
      $token = bin2hex(random_bytes(16));

      $sqlIns = "INSERT INTO reclamos (
          empresa_id, establecimiento_id, codigo_reclamo, correlativo_num, anio,
          tipo, fecha_registro, fecha_vencimiento_respuesta,

          consumidor_nombres, consumidor_apellidos, consumidor_doc_tipo, consumidor_doc_num,
          consumidor_email, consumidor_telefono, consumidor_direccion,

          consumidor_tipo, consumidor_menor,
          tutor_nombres, tutor_doc_tipo, tutor_doc_num,
          contacto_nombres, contacto_doc_tipo, contacto_doc_num,

          bien_tipo, bien_doc_tipo, bien_doc_num,
          bien_contratado, monto_reclamado, detalle, pedido,

          canal, estado, acepta_declaracion, evidencia_token, created_ip, user_agent
        ) VALUES (
          :empresa_id, :establecimiento_id, :codigo_reclamo, :correlativo_num, :anio,
          :tipo, NOW(), :fecha_vencimiento,

          :nombres, :apellidos, :doc_tipo, :doc_num,
          :email, :telefono, :direccion,

          :cons_tipo, :cons_menor,
          :tutor_nom, :tutor_doc_tipo, :tutor_doc_num,
          :cont_nom, :cont_doc_tipo, :cont_doc_num,

          :bien_tipo, :bien_doc_tipo, :bien_doc_num,
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

        'cons_tipo' => $data['consumidor_tipo'] ?? null,
        'cons_menor' => (int)($data['consumidor_menor'] ?? 0),

        'tutor_nom' => $data['tutor_nombres'] ?? null,
        'tutor_doc_tipo' => $data['tutor_doc_tipo'] ?? null,
        'tutor_doc_num' => $data['tutor_doc_num'] ?? null,

        'cont_nom' => $data['contacto_nombres'] ?? null,
        'cont_doc_tipo' => $data['contacto_doc_tipo'] ?? null,
        'cont_doc_num' => $data['contacto_doc_num'] ?? null,

        'bien_tipo' => $data['bien_tipo'] ?? null,
        'bien_doc_tipo' => $data['bien_doc_tipo'] ?? null,
        'bien_doc_num' => $data['bien_doc_num'] ?? null,

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

  public static function nombreImprimible(array $r): string
  {
    $tipo = strtoupper((string)($r['consumidor_tipo'] ?? ''));
    if ($tipo === 'JURIDICA') {
      return trim((string)($r['consumidor_nombres'] ?? ''));
    }
    $n = trim((string)($r['consumidor_nombres'] ?? ''));
    $a = trim((string)($r['consumidor_apellidos'] ?? ''));
    return trim($n . ' ' . $a);
  }

  public static function attachEvidencia(int $reclamoId, int $empresaId, array $ev): void
  {
    $sql = "UPDATE reclamos SET
            evidencia_path = :path,
            evidencia_mime = :mime,
            evidencia_size = :size,
            evidencia_original = :orig,
            evidencia_uploaded_at = :up_at,
            updated_at = NOW()
          WHERE id = :id AND empresa_id = :eid
          LIMIT 1";
    $st = self::pdo()->prepare($sql);
    $st->execute([
      'path' => $ev['path'] ?? null,
      'mime' => $ev['mime'] ?? null,
      'size' => $ev['size'] ?? null,
      'orig' => $ev['original'] ?? null,
      'up_at' => $ev['uploaded_at'] ?? null,
      'id' => $reclamoId,
      'eid' => $empresaId,
    ]);
  }



  public static function findByTokenInEmpresa(string $token, int $empresaId): ?array
  {
    $sql = "SELECT
            r.id, r.codigo_reclamo, r.correlativo_num, r.anio,
            r.tipo, r.estado, r.fecha_registro, r.fecha_vencimiento_respuesta,

            r.consumidor_nombres, r.consumidor_apellidos, r.consumidor_doc_tipo, r.consumidor_doc_num,
            r.consumidor_email, r.consumidor_telefono, r.consumidor_direccion,

            r.consumidor_tipo, r.consumidor_menor,
            r.tutor_nombres, r.tutor_doc_tipo, r.tutor_doc_num,
            r.contacto_nombres, r.contacto_doc_tipo, r.contacto_doc_num,

            r.bien_tipo, r.bien_doc_tipo, r.bien_doc_num,
            r.bien_contratado, r.monto_reclamado, r.detalle, r.pedido,
            r.evidencia_path, r.evidencia_mime, r.evidencia_size, r.evidencia_original, r.evidencia_uploaded_at,

            e.nombre AS establecimiento_nombre,
            e.direccion AS establecimiento_direccion,

            em.razon_social AS empresa_razon_social,
            em.nombre_comercial AS empresa_nombre_comercial,
            em.ruc AS empresa_ruc
        FROM reclamos r
        JOIN establecimientos e ON e.id = r.establecimiento_id
        JOIN empresas em ON em.id = r.empresa_id
        WHERE r.evidencia_token = :token
          AND r.empresa_id = :empresa_id
          AND (r.evidencia_token_expires_at IS NULL OR r.evidencia_token_expires_at >= NOW())
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
          ORDER BY rr.fecha_respuesta DESC";
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
    $codigo  = trim($codigo);
    $docTipo = strtoupper(trim($docTipo));
    $docNum  = trim($docNum);

    // Normaliza "2026-00001" -> "2026-000001"
    if (preg_match('/^(\d{4})-(\d{1,6})$/', $codigo, $m)) {
      $codigo = sprintf('%s-%06d', $m[1], (int)$m[2]);
    }

    $sql = "SELECT evidencia_token
          FROM reclamos
          WHERE empresa_id = :empresa_id
            AND codigo_reclamo = :codigo
            AND (
              (consumidor_doc_tipo = :doc_tipo1 AND consumidor_doc_num = :doc_num1)
              OR (tutor_doc_tipo     = :doc_tipo2 AND tutor_doc_num     = :doc_num2)
              OR (contacto_doc_tipo  = :doc_tipo3 AND contacto_doc_num  = :doc_num3)
            )
          LIMIT 1";

    $st = self::pdo()->prepare($sql);

    $st->bindValue(':empresa_id', $empresaId, \PDO::PARAM_INT);
    $st->bindValue(':codigo', $codigo, \PDO::PARAM_STR);

    // mismos valores, pero placeholders distintos (para evitar HY093)
    $st->bindValue(':doc_tipo1', $docTipo, \PDO::PARAM_STR);
    $st->bindValue(':doc_num1',  $docNum,  \PDO::PARAM_STR);

    $st->bindValue(':doc_tipo2', $docTipo, \PDO::PARAM_STR);
    $st->bindValue(':doc_num2',  $docNum,  \PDO::PARAM_STR);

    $st->bindValue(':doc_tipo3', $docTipo, \PDO::PARAM_STR);
    $st->bindValue(':doc_num3',  $docNum,  \PDO::PARAM_STR);

    $st->execute();

    $row = $st->fetch();
    return $row['evidencia_token'] ?? null;
  }
}
