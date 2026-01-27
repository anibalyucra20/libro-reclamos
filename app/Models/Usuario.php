<?php
declare(strict_types=1);

namespace App\Models;

final class Usuario extends BaseModel
{
  public static function firstEmpresaSlugForUser(int $userId): ?string
  {
    // Si es superadmin global, devolver la primera empresa activa
    $sqlSuper = "SELECT 1 FROM usuario_scope us
                 JOIN roles r ON r.id = us.rol_id
                 WHERE us.usuario_id = :uid
                   AND us.estado='ACTIVO'
                   AND us.empresa_id IS NULL
                   AND r.code='superadmin'
                 LIMIT 1";
    $st = self::pdo()->prepare($sqlSuper);
    $st->execute(['uid' => $userId]);
    if ($st->fetch()) {
      $st2 = self::pdo()->query("SELECT slug FROM empresas WHERE estado='ACTIVO' ORDER BY id ASC LIMIT 1");
      $row = $st2->fetch();
      return $row['slug'] ?? null;
    }

    // Caso normal: primera empresa asignada en scope
    $sql = "SELECT e.slug
            FROM usuario_scope us
            JOIN empresas e ON e.id = us.empresa_id
            WHERE us.usuario_id = :uid
              AND us.estado='ACTIVO'
              AND us.empresa_id IS NOT NULL
              AND e.estado='ACTIVO'
            ORDER BY us.empresa_id ASC
            LIMIT 1";
    $st = self::pdo()->prepare($sql);
    $st->execute(['uid' => $userId]);
    $row = $st->fetch();
    return $row['slug'] ?? null;
  }
}
