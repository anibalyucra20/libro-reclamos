<?php
declare(strict_types=1);

namespace App\Models;

final class Establecimiento extends BaseModel
{
  public static function allByEmpresa(int $empresaId): array
  {
    $sql = "SELECT id, codigo_identificacion, nombre, direccion
            FROM establecimientos
            WHERE empresa_id = :empresa_id AND estado='ACTIVO'
            ORDER BY nombre ASC";
    $st = self::pdo()->prepare($sql);
    $st->execute(['empresa_id' => $empresaId]);
    return $st->fetchAll() ?: [];
  }

  public static function findInEmpresa(int $empresaId, int $establecimientoId): ?array
  {
    $sql = "SELECT id, codigo_identificacion, nombre, direccion
            FROM establecimientos
            WHERE id = :id AND empresa_id = :empresa_id AND estado='ACTIVO'
            LIMIT 1";
    $st = self::pdo()->prepare($sql);
    $st->execute(['id' => $establecimientoId, 'empresa_id' => $empresaId]);
    $row = $st->fetch();
    return $row ?: null;
  }
}
