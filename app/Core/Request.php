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
    $uri  = $_SERVER['REQUEST_URI'] ?? '/';
    $path = parse_url($uri, PHP_URL_PATH) ?: '/';
    $path = rtrim($path, '/') ?: '/';

    // El TenantMiddleware setea:
    // - tenant['mount'] = '/panel' cuando es panel/panel_root
    $mount = (string)($this->tenant['mount'] ?? '');
    if ($mount !== '' && $mount !== '/') {
      // Normalizar mount: sin slash final
      $m = rtrim($mount, '/');

      // Si la ruta es exactamente "/panel" => la tratamos como "/"
      if ($path === $m) return '/';

      // Si comienza con "/panel/" => quitamos ese prefijo
      if (str_starts_with($path, $m . '/')) {
        $path = substr($path, strlen($m));
        $path = $path === '' ? '/' : $path;
      }
    }

    return $path;
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
