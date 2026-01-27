<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\DB;

final class ACL
{
  /**
   * Valida si usuario tiene permiso en el scope (empresa y opcional establecimiento).
   * - Si el usuario tiene scope a nivel empresa (establecimiento_id NULL), cubre todos los locales.
   * - Si tiene scope por local, solo ese local.
   */
  public static function can(int $userId, string $permCode, ?int $empresaId, ?int $establecimientoId = null): bool
  {
    // Superadmin global: tiene scope con empresa_id NULL
    $sql = "
      SELECT 1
      FROM usuario_scope us
      JOIN roles r ON r.id = us.rol_id
      JOIN rol_permiso rp ON rp.rol_id = r.id
      JOIN permisos p ON p.id = rp.permiso_id
      WHERE us.usuario_id = :uid
        AND us.estado = 'ACTIVO'
        AND p.code = :perm
        AND (
          (us.empresa_id IS NULL) OR
          (us.empresa_id = :eid AND (us.establecimiento_id IS NULL OR us.establecimiento_id = :sid))
        )
      LIMIT 1
    ";
    $st = DB::pdo()->prepare($sql);
    $st->execute([
      'uid' => $userId,
      'perm' => $permCode,
      'eid' => $empresaId,
      'sid' => $establecimientoId,
    ]);
    return (bool)$st->fetch();
  }
}
