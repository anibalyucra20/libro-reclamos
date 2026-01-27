<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\DB;
use DateTimeImmutable;

final class RateLimiter
{
  // Config simple (ajustable)
  private const MAX_ATTEMPTS = 5;
  private const LOCK_MINUTES = 10;

  public static function checkOrFail(?string $ip): void
  {
    $ipBin = self::ipToBinary($ip);
    if ($ipBin === null) return; // si no hay IP, no limitamos

    $sql = "SELECT attempts, locked_until
            FROM login_attempts
            WHERE ip = :ip
            LIMIT 1";
    $st = DB::pdo()->prepare($sql);
    $st->execute(['ip' => $ipBin]);
    $row = $st->fetch();

    if (!$row) return;

    if (!empty($row['locked_until'])) {
      $lockedUntil = new DateTimeImmutable($row['locked_until']);
      if ($lockedUntil > new DateTimeImmutable('now')) {
        http_response_code(429);
        echo "Demasiados intentos. Intenta nuevamente más tarde.";
        exit;
      }
    }
  }

  public static function hit(?string $ip): void
  {
    $ipBin = self::ipToBinary($ip);
    if ($ipBin === null) return;

    $pdo = DB::pdo();
    $pdo->beginTransaction();

    $st = $pdo->prepare("SELECT id, attempts, locked_until
                         FROM login_attempts
                         WHERE ip=:ip
                         LIMIT 1 FOR UPDATE");
    $st->execute(['ip' => $ipBin]);
    $row = $st->fetch();

    if (!$row) {
      $pdo->prepare("INSERT INTO login_attempts (ip, attempts, last_attempt_at)
                     VALUES (:ip, 1, NOW())")
          ->execute(['ip' => $ipBin]);
      $pdo->commit();
      return;
    }

    // si estaba lock y ya pasó, resetea
    $attempts = (int)$row['attempts'];
    $lockedUntil = $row['locked_until'];

    $now = new DateTimeImmutable('now');

    if ($lockedUntil) {
      $lu = new DateTimeImmutable($lockedUntil);
      if ($lu <= $now) {
        $attempts = 0;
        $lockedUntil = null;
      }
    }

    $attempts++;

    $newLockedUntil = null;
    if ($attempts >= self::MAX_ATTEMPTS) {
      $newLockedUntil = $now->modify('+' . self::LOCK_MINUTES . ' minutes')->format('Y-m-d H:i:s');
    }

    $pdo->prepare("UPDATE login_attempts
                   SET attempts=:att, last_attempt_at=NOW(), locked_until=:lu
                   WHERE id=:id")
        ->execute([
          'att' => $attempts,
          'lu' => $newLockedUntil,
          'id' => (int)$row['id'],
        ]);

    $pdo->commit();
  }

  public static function clear(?string $ip): void
  {
    $ipBin = self::ipToBinary($ip);
    if ($ipBin === null) return;

    DB::pdo()->prepare("DELETE FROM login_attempts WHERE ip=:ip")->execute(['ip' => $ipBin]);
  }

  private static function ipToBinary(?string $ip): ?string
  {
    if (!$ip) return null;
    $bin = @inet_pton($ip);
    return $bin === false ? null : $bin;
  }
}
