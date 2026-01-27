<?php
declare(strict_types=1);

namespace App\Services;

final class Logger
{
  public static function info(string $message, array $context = []): void
  {
    self::write('INFO', $message, $context);
  }

  public static function warning(string $message, array $context = []): void
  {
    self::write('WARN', $message, $context);
  }

  public static function error(string $message, array $context = []): void
  {
    self::write('ERROR', $message, $context);
  }

  private static function write(string $level, string $message, array $context): void
  {
    $base = dirname(__DIR__, 2);
    $file = $base . '/storage/logs/app.log';

    $ts = date('Y-m-d H:i:s');
    $ctx = $context ? json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : '';
    $line = "[{$ts}] {$level} {$message}" . ($ctx ? " {$ctx}" : "") . PHP_EOL;

    @file_put_contents($file, $line, FILE_APPEND);
  }
}
