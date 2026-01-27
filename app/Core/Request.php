<?php
declare(strict_types=1);

namespace App\Core;

final class Request
{
  public array $params = [];     // params de ruta
  public array $tenant = [];     // data del tenant resuelto (empresa)

  public function method(): string
  {
    return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
  }

  public function path(): string
  {
    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    $path = parse_url($uri, PHP_URL_PATH) ?: '/';
    return rtrim($path, '/') ?: '/';
  }

  public function host(): string
  {
    return strtolower($_SERVER['HTTP_HOST'] ?? '');
  }

  public function input(string $key, mixed $default = null): mixed
  {
    return $_POST[$key] ?? $_GET[$key] ?? $default;
  }

  public function allPost(): array
  {
    return $_POST;
  }
}
