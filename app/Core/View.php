<?php

declare(strict_types=1);

namespace App\Core;

final class View
{
  public static function render(string $view, array $data = [], string $layout = 'public'): void
  {
    extract($data);

    $base = dirname(__DIR__) . '/Views/';
    $viewFile   = $base . str_replace('.', '/', $view) . '.php';
    $layoutFile = $base . 'layouts/' . $layout . '.php';

    if (!file_exists($viewFile)) {
      http_response_code(500);
      echo "View no encontrada: {$viewFile}";
      exit;
    }

    ob_start();
    require $viewFile;
    $content = ob_get_clean();

    require $layoutFile;
  }

  public static function renderToString(string $view, array $data = []): string
  {
    extract($data);

    $base = dirname(__DIR__) . '/Views/';
    $viewFile = $base . str_replace('.', '/', $view) . '.php';

    if (!file_exists($viewFile)) {
      throw new \RuntimeException("View no encontrada: {$viewFile}");
    }

    ob_start();
    require $viewFile;
    return (string)ob_get_clean();
  }
}
