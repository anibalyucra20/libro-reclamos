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
    $adminSub = strtolower($app['admin_subdomain'] ?? 'admin');

    $host = strtolower($request->host());
    $host = explode(':', $host)[0]; // quitar puerto

    // DEV localhost/IP: usar TENANT_SLUG + MODE
    if ($host === 'localhost' || filter_var($host, FILTER_VALIDATE_IP)) {
      $mode = $_ENV['DEV_MODE'] ?? 'public'; // public|panel
      $slug = $_ENV['TENANT_SLUG'] ?? '';
      if ($slug === '') {
        $request->tenant = ['mode' => 'dev_no_tenant'];
        return;
      }
      if ($mode === 'panel') {
        $empresa = Empresa::findBySlug($slug);
        if (!$empresa) { http_response_code(404); echo "Tenant no encontrado"; exit; }
        $request->tenant = ['mode'=>'panel','empresa'=>$empresa,'empresa_id'=>(int)$empresa['id']];
        return;
      }
      $empresa = Empresa::findBySlug($slug);
      if (!$empresa) { http_response_code(404); echo "Tenant no encontrado"; exit; }
      $request->tenant = ['mode'=>'public','empresa'=>$empresa,'empresa_id'=>(int)$empresa['id']];
      return;
    }

    $parts = explode('.', $host);

    // Necesitamos al menos 3 labels para empresa.admin.dominio
    // Ej: empresa1.admin.tudominio.com => [empresa1, admin, tudominio, com]
    $sub1 = $parts[0] ?? '';
    $sub2 = $parts[1] ?? '';

    // Caso PANEL por empresa: {empresa}.{admin}.dominio
    if ($sub2 === $adminSub && $sub1 !== '') {
      $empresa = Empresa::findBySlug($sub1);
      if (!$empresa) { http_response_code(404); echo "Empresa no encontrada"; exit; }

      $request->tenant = [
        'mode' => 'panel',
        'empresa_id' => (int)$empresa['id'],
        'empresa_slug' => $empresa['slug'],
        'razon_social' => $empresa['razon_social'],
        'nombre_comercial' => $empresa['nombre_comercial'],
      ];
      return;
    }

    // Caso PANEL raíz: admin.dominio
    if ($sub1 === $adminSub) {
      $request->tenant = ['mode' => 'panel_root'];
      return;
    }

    // Caso PÚBLICO: empresa.dominio
    if ($sub1 !== '') {
      $empresa = Empresa::findBySlug($sub1);
      if (!$empresa) { http_response_code(404); echo "Empresa no encontrada"; exit; }

      $request->tenant = [
        'mode' => 'public',
        'empresa_id' => (int)$empresa['id'],
        'empresa_slug' => $empresa['slug'],
        'razon_social' => $empresa['razon_social'],
        'nombre_comercial' => $empresa['nombre_comercial'],
      ];
      return;
    }

    $request->tenant = ['mode' => 'unknown'];
  }
}
