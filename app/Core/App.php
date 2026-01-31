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
    // Landing principal (websigi.com)
    if (($request->tenant['mode'] ?? '') === 'landing') {
      $router->get('/', 'App\\Controllers\\LandingController@index');
      $router->get('/pricing', 'App\\Controllers\\LandingController@pricing'); // opcional
      $router->get('/contacto', 'App\\Controllers\\LandingController@contacto'); // opcional
    }

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

      $router->get('/reportes', 'App\\Controllers\\ReportesReclamosController@index');
      $router->get('/reportes/data', 'App\\Controllers\\ReportesReclamosController@data');


      // Requiere auth
      // (lo aplicamos dentro del controller por ahora; en Parte 5 lo hacemos middleware por grupo)
      $router->get('/',              'App\\Controllers\\PanelReclamosController@index');
      $router->get('/reclamos',              'App\\Controllers\\PanelReclamosController@index');
      $router->get('/reclamos/exportar', 'App\\Controllers\\ExportReclamosController@xlsx');
      $router->get('/reclamos/{id}',         'App\\Controllers\\PanelReclamosController@show');
      $router->post('/reclamos/{id}/responder', 'App\\Controllers\\PanelReclamosController@responder');

      $router->get('/alertas', 'App\\Controllers\\AlertasController@index');
      $router->post('/alertas', 'App\\Controllers\\AlertasController@save');
      $router->post('/alertas/probar', 'App\\Controllers\\AlertasController@test');



      $router->get('/usuarios', 'App\\Controllers\\UsuariosController@index');
      $router->get('/usuarios/nuevo', 'App\\Controllers\\UsuariosController@nuevo');
      $router->post('/usuarios', 'App\\Controllers\\UsuariosController@crear');

      $router->get('/usuarios/{id}', 'App\\Controllers\\UsuariosController@show');
      $router->post('/usuarios/{id}', 'App\\Controllers\\UsuariosController@update');

      $router->post('/usuarios/{id}/scope', 'App\\Controllers\\UsuariosController@scopeAdd');
      $router->post('/usuarios/{id}/scope/{scope_id}/delete', 'App\\Controllers\\UsuariosController@scopeDelete');

      $router->post('/usuarios/{id}/password', 'App\\Controllers\\UsuariosController@resetPassword');


      $router->get('/empresas', 'App\\Controllers\\EmpresasController@index');
      $router->get('/empresas/nuevo', 'App\\Controllers\\EmpresasController@nuevo');
      $router->post('/empresas', 'App\\Controllers\\EmpresasController@crear');
      $router->get('/empresas/{id}', 'App\\Controllers\\EmpresasController@edit');
      $router->post('/empresas/{id}', 'App\\Controllers\\EmpresasController@update');


      $router->get('/establecimientos', 'App\\Controllers\\EstablecimientosController@index');
      $router->get('/establecimientos/nuevo', 'App\\Controllers\\EstablecimientosController@nuevo');
      $router->post('/establecimientos', 'App\\Controllers\\EstablecimientosController@crear');

      $router->get('/establecimientos/{id}', 'App\\Controllers\\EstablecimientosController@edit');
      $router->post('/establecimientos/{id}', 'App\\Controllers\\EstablecimientosController@update');
    }

    // Panel raíz admin.tudominio.com (opcional)
    if (($request->tenant['mode'] ?? '') === 'panel_root') {
      $router->get('/', 'App\\Controllers\\EmpresasController@index');

      $router->get('/login',  'App\\Controllers\\AuthController@form');
      $router->post('/login', 'App\\Controllers\\AuthController@login');

      $router->post('/logout', 'App\\Controllers\\AuthController@logout');

      $router->get('/empresas', 'App\\Controllers\\EmpresasController@index');
      $router->get('/empresas/nuevo', 'App\\Controllers\\EmpresasController@nuevo');
      $router->post('/empresas', 'App\\Controllers\\EmpresasController@crear');
      $router->get('/empresas/{id}', 'App\\Controllers\\EmpresasController@edit');
      $router->post('/empresas/{id}', 'App\\Controllers\\EmpresasController@update');

      $router->get('/usuarios', 'App\\Controllers\\UsuariosRootController@index');
      $router->get('/usuarios/nuevo', 'App\\Controllers\\UsuariosRootController@nuevo');
      $router->post('/usuarios', 'App\\Controllers\\UsuariosRootController@crear');
      $router->get('/usuarios/{id}', 'App\\Controllers\\UsuariosRootController@show');
      $router->post('/usuarios/{id}/password', 'App\\Controllers\\UsuariosRootController@resetPassword');
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
