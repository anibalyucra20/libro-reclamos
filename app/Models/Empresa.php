<?php
declare(strict_types=1);

namespace App\Models;

final class Empresa extends BaseModel
{
  public static function findBySlug(string $slug): ?array
  {
    $sql = "SELECT id, ruc, razon_social, nombre_comercial, slug, estado
            FROM empresas
            WHERE slug = :slug AND estado = 'ACTIVO'
            LIMIT 1";
    $st = self::pdo()->prepare($sql);
    $st->execute(['slug' => $slug]);
    $row = $st->fetch();
    return $row ?: null;
  }
}
