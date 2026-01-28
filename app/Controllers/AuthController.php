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

    RateLimiter::clear($ip);

    $mode = $this->request->tenant['mode'] ?? '';
    $panel = $this->panelPrefix();

    // Panel empresa: SIEMPRE bajo /panel
    if ($mode === 'panel') {
      $this->response->redirect($panel . '/reclamos');
      return;
    }

    // Panel root: quedarse en websigi.com/panel
    if ($mode === 'panel_root') {
      $u = Auth::user();

      // Superadmin global => /panel/empresas
      if (\App\Services\ACL::can((int)$u['id'], 'empresas.gestionar', null, null)) {
        $this->response->redirect($panel . '/empresas');
        return;
      }

      // Usuario no superadmin => mandarlo al primer tenant asignado, pero SIN sub-subdominio
      $slug = Usuario::firstEmpresaSlugForUser((int)$u['id']);
      if (!$slug) {
        http_response_code(403);
        echo "No tienes empresas asignadas.";
        return;
      }

      // Construir URL: {slug}.BASE_DOMAIN/panel/reclamos
      // BASE_DOMAIN lo sacamos de root_domain si existe, si no del host actual quitando "www."
      $rootDomain = (string)($_ENV['ROOT_DOMAIN'] ?? '');
      $host = strtolower($_SERVER['HTTP_HOST'] ?? '');
      $host = explode(':', $host)[0];
      $baseDomain = $rootDomain !== '' ? $rootDomain : preg_replace('/^www\./', '', $host);

      $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
      $this->response->redirect($scheme . '://' . $slug . '.' . $baseDomain . $panel . '/reclamos');
      return;
    }

    // fallback
    $this->response->redirect($panel . '/login');
  }

  public function logout(): void
  {
    Auth::logout();
    $this->response->redirect($this->panelPrefix() . '/login');
  }
}
