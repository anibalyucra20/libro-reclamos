<?php

declare(strict_types=1);

namespace App\Middlewares;

use App\Core\Request;
use App\Models\Empresa;

final class TenantMiddleware
{
  public function handle(Request $request): void
  {
    $app = require dirname(__DIR__) . '/Config/app.php';

    // panel por PATH
    $panelPath   = trim((string)($app['panel_path'] ?? 'panel'), '/'); // 'panel' o 'admin'
    $panelPrefix = '/' . $panelPath;

    // dominio raíz
    $rootDomain = strtolower((string)($app['root_domain'] ?? ($_ENV['ROOT_DOMAIN'] ?? '')));

    $host = strtolower($request->host());
    $host = explode(':', $host)[0];

    $uri  = $_SERVER['REQUEST_URI'] ?? '/';
    $path = parse_url($uri, PHP_URL_PATH) ?: '/';
    $path = rtrim($path, '/') ?: '/';

    $isPanelPath = function (string $p) use ($panelPrefix): bool {
      return $p === $panelPrefix || str_starts_with($p, $panelPrefix . '/');
    };

    // DEV localhost/IP: soportar panel_root por /panel y tenant por TENANT_SLUG
    if ($host === 'localhost' || filter_var($host, FILTER_VALIDATE_IP)) {

      $uriPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
      $panelPrefix = '/panel'; // si luego lo haces configurable, lo cambias aquí

      // ✅ Panel ROOT en localhost: http://localhost:8080/panel/...
      if ($uriPath === $panelPrefix || str_starts_with($uriPath, $panelPrefix . '/')) {
        $request->tenant = [
          'mode' => 'panel_root',
          'panel_prefix' => $panelPrefix,
          'mount' => $panelPrefix,
        ];
        return;
      }

      // ✅ Tenant DEV: http://localhost:8080/ (requiere TENANT_SLUG)
      $mode = $_ENV['DEV_MODE'] ?? 'public'; // public|panel
      $slug = $_ENV['TENANT_SLUG'] ?? '';
      if ($slug === '') {
        $request->tenant = ['mode' => 'dev_no_tenant'];
        return;
      }

      $empresa = Empresa::findBySlug($slug);
      if (!$empresa) {
        http_response_code(404);
        echo "Tenant no encontrado";
        exit;
      }

      if ($mode === 'panel') {
        $request->tenant = [
          'mode' => 'panel',
          'empresa_id' => (int)$empresa['id'],
          'empresa_slug' => $empresa['slug'],
          'razon_social' => $empresa['razon_social'],
          'nombre_comercial' => $empresa['nombre_comercial'],
          'panel_prefix' => $panelPrefix,
          'mount' => $panelPrefix,
        ];
        return;
      }

      $request->tenant = [
        'mode' => 'public',
        'empresa_id' => (int)$empresa['id'],
        'empresa_slug' => $empresa['slug'],
        'razon_social' => $empresa['razon_social'],
        'nombre_comercial' => $empresa['nombre_comercial'],
      ];
      return;
    }


    // 1) Dominio raíz: websigi.com  (marketing o panel_root por path)
    if ($rootDomain !== '' && ($host === $rootDomain || $host === 'www.' . $rootDomain)) {
      if ($isPanelPath($path)) {
        $request->tenant = ['mode' => 'panel_root', 'panel_prefix' => $panelPrefix, 'mount' => $panelPrefix];
        return;
      }
      $request->tenant = ['mode' => 'marketing', 'panel_prefix' => $panelPrefix, 'mount' => ''];
      return;
    }

    // 2) Subdominio 1 nivel: tenant.websigi.com
    // tenant = primer label del host
    $parts = explode('.', $host);
    $sub1  = $parts[0] ?? '';

    // Evitar casos comunes
    if ($sub1 === '' || $sub1 === 'www') {
      $request->tenant = ['mode' => 'unknown', 'panel_prefix' => $panelPrefix, 'mount' => ''];
      return;
    }

    $empresa = Empresa::findBySlug($sub1);
    if (!$empresa) {
      http_response_code(404);
      echo "Empresa no encontrada";
      exit;
    }

    if ($isPanelPath($path)) {
      $request->tenant = [
        'mode' => 'panel',
        'empresa_id' => (int)$empresa['id'],
        'empresa_slug' => $empresa['slug'],
        'razon_social' => $empresa['razon_social'],
        'nombre_comercial' => $empresa['nombre_comercial'],
        'panel_prefix' => $panelPrefix,
        'mount' => $panelPrefix, // sirve para "strip" luego
      ];
      return;
    }

    $request->tenant = [
      'mode' => 'public',
      'empresa_id' => (int)$empresa['id'],
      'empresa_slug' => $empresa['slug'],
      'razon_social' => $empresa['razon_social'],
      'nombre_comercial' => $empresa['nombre_comercial'],
      'panel_prefix' => $panelPrefix,
      'mount' => '',
    ];
  }
}
