<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Services\Auth;
use App\Services\Csrf;
use App\Services\RateLimiter;
use App\Models\Usuario;

final class AuthController extends Controller
{
  public function form(): void
  {
    $this->view('panel.login', [
      'csrf' => Csrf::token(),
      'tenant' => $this->request->tenant,
    ], 'panel');
  }

  public function login(): void
  {
    $ip = $_SERVER['REMOTE_ADDR'] ?? null;

    // 1) rate-limit check (si está lockeado, corta)
    RateLimiter::checkOrFail($ip);

    $email = trim((string)$this->request->input('email'));
    $pass  = (string)$this->request->input('password');

    if (!Auth::login($email, $pass)) {
      RateLimiter::hit($ip);

      $this->view('panel.login', [
        'csrf' => Csrf::token(),
        'error' => 'Credenciales inválidas',
        'tenant' => $this->request->tenant,
      ], 'panel');
      return;
    }

    // login OK => limpiar attempts
    RateLimiter::clear($ip);

    // 2) Redirección
    $mode = $this->request->tenant['mode'] ?? '';

    if ($mode === 'panel') {
      $this->response->redirect('/reclamos');
      return;
    }

    if ($mode === 'panel_root') {
      $u = Auth::user();
      $slug = Usuario::firstEmpresaSlugForUser((int)$u['id']);

      if (!$slug) {
        http_response_code(403);
        echo "No tienes empresas asignadas.";
        return;
      }

      // construir URL: {slug}.{ADMIN_SUBDOMAIN}.dominio actual
      $adminSub = $_ENV['ADMIN_SUBDOMAIN'] ?? 'admin';
      $host = strtolower($_SERVER['HTTP_HOST'] ?? '');
      $host = explode(':', $host)[0];

      // host actual = admin.tudominio.com => quitar "admin."
      $baseDomain = preg_replace('/^' . preg_quote($adminSub, '/') . '\./', '', $host);

      $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
      $this->response->redirect($scheme . '://' . $slug . '.' . $adminSub . '.' . $baseDomain . '/reclamos');
      return;
    }

    // fallback
    $this->response->redirect('/login');
  }

  public function logout(): void
  {
    Auth::logout();
    $this->response->redirect('/login');
  }
}
