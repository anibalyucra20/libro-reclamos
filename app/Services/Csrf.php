<?php
declare(strict_types=1);

namespace App\Services;

final class Csrf
{
  public static function token(): string
  {
    $sec = require dirname(__DIR__) . '/Config/security.php';
    $key = $sec['csrf_key'];

    if (empty($_SESSION[$key])) {
      $_SESSION[$key] = bin2hex(random_bytes(32));
    }
    return $_SESSION[$key];
  }

  public static function check(?string $token): bool
  {
    $sec = require dirname(__DIR__) . '/Config/security.php';
    $key = $sec['csrf_key'];

    return is_string($token) && isset($_SESSION[$key]) && hash_equals($_SESSION[$key], $token);
  }
}
