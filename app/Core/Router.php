<?php

declare(strict_types=1);

namespace App\Core;

use App\Core\Controller;

final class Router
{
  private array $routes = ['GET' => [], 'POST' => []];

  public function __construct(
    private Request $request,
    private Response $response
  ) {}

  public function get(string $pattern, string $handler): void
  {
    $this->routes['GET'][$pattern] = $handler;
  }

  public function post(string $pattern, string $handler): void
  {
    $this->routes['POST'][$pattern] = $handler;
  }

  public function dispatch(): void
  {
    $method = $this->request->method();
    $path   = $this->request->path();

    foreach ($this->routes[$method] ?? [] as $pattern => $handler) {
      $match = $this->match($pattern, $path);
      if ($match !== null) {
        $this->request->params = $match;
        $this->invoke($handler);
        return;
      }
    }

    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    // Normalizamos quitando trailing slash excepto si es root
    if ($path !== '/' && str_ends_with($path, '/')) {
      $path = rtrim($path, '/');
    }
    $panelPrefix = '/panel';
    // Determinar destino
    if ($path === $panelPrefix || str_starts_with($path, $panelPrefix . '/')) {
      $redirectTo = $panelPrefix . '/';
    } else {
      $redirectTo = '/';
    }
    $_SESSION['flash_error'] = "Página no encontrada";
    // Redirección 302 (no uses 404 si vas a redirigir)
    header("Location: " . $redirectTo, true, 302);
    exit;
  }

  private function match(string $pattern, string $path): ?array
  {
    // Normalizar: solo quitamos slash final si NO es la raíz
    $patternNorm = ($pattern !== '/') ? rtrim($pattern, '/') : '/';
    $pathNorm    = ($path !== '/') ? rtrim($path, '/') : '/';

    $regex = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '(?P<$1>[^/]+)', $patternNorm);
    $regex = '#^' . $regex . '$#';

    if (!preg_match($regex, $pathNorm, $matches)) return null;

    $params = [];
    foreach ($matches as $k => $v) {
      if (is_string($k)) $params[$k] = $v;
    }
    return $params;
  }


  private function invoke(string $handler): void
  {
    [$class, $method] = explode('@', $handler, 2);
    $controller = new $class($this->request, $this->response);
    $controller->$method();
  }
}
