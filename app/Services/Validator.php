<?php
declare(strict_types=1);

namespace App\Services;

final class Validator
{
  public static function required(string $v): bool { return trim($v) !== ''; }
  public static function email(string $v): bool { return $v === '' || filter_var($v, FILTER_VALIDATE_EMAIL) !== false; }
  public static function maxLen(string $v, int $n): bool { return mb_strlen($v) <= $n; }

  public static function docNum(string $tipo, string $num): bool
  {
    $num = trim($num);
    if ($num === '') return false;
    if (in_array($tipo, ['DNI'], true)) return preg_match('/^\d{8}$/', $num) === 1;
    if (in_array($tipo, ['RUC'], true)) return preg_match('/^\d{11}$/', $num) === 1;
    return self::maxLen($num, 20);
  }
}
