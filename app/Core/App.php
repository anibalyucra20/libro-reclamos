<?php

declare(strict_types=1);

namespace App\Core;

use App\Middlewares\TenantMiddleware;
use App\Middlewares\CsrfMiddleware;

final class App
{
  public function run(): void
  {
    $request  = new Request();
    $response = new Response();
    (new \App\Middlewares\SecurityHeadersMiddleware())->handle($request);
    // Seguridad de sesión
    $this->bootSession();

    $router = new Router($request, $response);

    // Middlewares globales
    (new TenantMiddleware())->handle($request); // resuelve tenant por subdominio
    (new CsrfMiddleware())->handle($request);   // valida CSRF en métodos mutadores

    // Rutas públicas (empresa.tudominio.com)
    if (($request->tenant['mode'] ?? '') === 'public') {
      $router->get('/', 'App\\Controllers\\PublicLibroController@home');
      $router->get('/reclamo/nuevo', 'App\\Controllers\\PublicLibroController@nuevo');
      $router->post('/reclamo', 'App\\Controllers\\PublicLibroController@crear');
      $router->get('/constancia/{token}', 'App\\Controllers\\PublicLibroController@constancia');
      $router->get('/seguimiento/{token}', 'App\\Controllers\\PublicLibroController@seguimiento');
      $router->get('/seguimiento', 'App\\Controllers\\PublicLibroController@seguimientoForm');
      $router->post('/seguimiento/buscar', 'App\\Controllers\\PublicLibroController@seguimientoBuscar');
      $router->get('/constancia/{token}/pdf', 'App\\Controllers\\PublicLibroController@constanciaPdf');

    }

    // Rutas panel por empresa (empresa.admin.tudominio.com)
    if (($request->tenant['mode'] ?? '') === 'panel') {
      $router->get('/login',  'App\\Controllers\\AuthController@form');
      $router->post('/login', 'App\\Controllers\\AuthController@login');
      $router->post('/logout', 'App\\Controllers\\AuthController@logout');

      // Requiere auth
      // (lo aplicamos dentro del controller por ahora; en Parte 5 lo hacemos middleware por grupo)
      $router->get('/reclamos',              'App\\Controllers\\PanelReclamosController@index');
      $router->get('/reclamos/{id}',         'App\\Controllers\\PanelReclamosController@show');
      $router->post('/reclamos/{id}/responder', 'App\\Controllers\\PanelReclamosController@responder');
    }

    // Panel raíz admin.tudominio.com (opcional)
    if (($request->tenant['mode'] ?? '') === 'panel_root') {
      $router->get('/login',  'App\\Controllers\\AuthController@form');
      $router->post('/login', 'App\\Controllers\\AuthController@login');
    }
    //$router->get('/__debug', 'App\\Controllers\\DebugController@index');

    $router->dispatch();
  }

  private function bootSession(): void
  {
    $sec = require dirname(__DIR__) . '/Config/security.php';

    session_name($sec['session_name']);
    session_set_cookie_params([
      'lifetime' => 0,
      'path' => '/',
      'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
      'httponly' => true,
      'samesite' => 'Lax',
    ]);

    if (session_status() !== PHP_SESSION_ACTIVE) {
      session_start();
    }
  }
}
