<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\DB;

final class Auth
{
  public static function user(): ?array
  {
    return $_SESSION['_user'] ?? null;
  }

  public static function check(): bool
  {
    return isset($_SESSION['_user']);
  }

  public static function login(string $email, string $password): bool
  {
    $sql = "SELECT id, nombres, apellidos, email, password_hash, estado
            FROM usuarios
            WHERE email = :email
            LIMIT 1";
    $st = DB::pdo()->prepare($sql);
    $st->execute(['email' => $email]);
    $u = $st->fetch();

    if (!$u || $u['estado'] !== 'ACTIVO') return false;
    if (!password_verify($password, $u['password_hash'])) return false;

    // Evitar session fixation
    session_regenerate_id(true);

    $_SESSION['_user'] = [
      'id' => (int)$u['id'],
      'nombres' => $u['nombres'],
      'apellidos' => $u['apellidos'],
      'email' => $u['email'],
    ];
    return true;
  }

  public static function logout(): void
  {
    unset($_SESSION['_user']);
    session_regenerate_id(true);
  }
}
