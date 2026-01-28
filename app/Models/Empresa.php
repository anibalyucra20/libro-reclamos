<?php
declare(strict_types=1);

namespace App\Models;

final class Empresa extends BaseModel
{
  public static function findBySlug(string $slug): ?array
  {
    $st = self::pdo()->prepare("SELECT * FROM empresas WHERE slug=:s LIMIT 1");
    $st->execute([':s' => $slug]);
    $row = $st->fetch();
    return $row ?: null;
  }

  public static function all(): array
  {
    $st = self::pdo()->query("SELECT * FROM empresas ORDER BY id DESC");
    return $st->fetchAll() ?: [];
  }

  public static function findById(int $id): ?array
  {
    $st = self::pdo()->prepare("SELECT * FROM empresas WHERE id=:id LIMIT 1");
    $st->execute([':id' => $id]);
    $row = $st->fetch();
    return $row ?: null;
  }
}
