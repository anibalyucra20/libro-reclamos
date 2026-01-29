<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Services\Auth;
use App\Services\ACL;
use App\Services\Csrf;
use App\Services\UsuariosRootService;

final class UsuariosRootController extends Controller
{
  private function guard(): void
  {
    (new \App\Middlewares\AuthMiddleware())->handle($this->request, $this->response);

    if (($this->request->tenant['mode'] ?? '') !== 'panel_root') {
      http_response_code(400);
      echo "Panel root inválido";
      exit;
    }

    $user = Auth::user();
    if (!$user) {
      http_response_code(403);
      echo "Forbidden";
      exit;
    }

    if (!ACL::can((int)$user['id'], 'usuarios.gestionar', null, null)) {
      http_response_code(403);
      echo "Sin permiso";
      exit;
    }
  }

  public function index(): void
  {
    $this->guard();

    $svc = new UsuariosRootService();

    $empresaId = (int)($this->request->input('empresa_id', $_GET['empresa_id'] ?? 0));
    $q = trim((string)($this->request->input('q', $_GET['q'] ?? '')));

    $rows = $svc->listGlobal([
      'empresa_id' => $empresaId > 0 ? $empresaId : null,
      'q' => $q,
    ]);

    $this->view('panel_root/usuarios/index', [
      'tenant' => $this->request->tenant,
      'rows' => $rows,
      'empresas' => $svc->getEmpresasActivas(),
      'filters' => ['empresa_id' => $empresaId, 'q' => $q],
    ], 'panel');
  }

  public function nuevo(): void
  {
    $this->guard();

    $svc = new UsuariosRootService();

    $this->view('panel_root/usuarios/form', [
      'tenant' => $this->request->tenant,
      'csrf' => Csrf::token(),
      'empresas' => $svc->getEmpresasActivas(),
      'roles' => $svc->getRoles(),
      'row' => [
        'empresa_id' => (int)($_GET['empresa_id'] ?? 0),
        'email' => '',
        'nombres' => '',
        'apellidos' => '',
        'rol_id' => 0,
        'password' => '',
      ],
    ], 'panel');
  }

  public function crear(): void
  {
    $this->guard();

    if (!Csrf::check((string)$this->request->input('_csrf'))) {
      http_response_code(419);
      echo "CSRF inválido";
      return;
    }

    $svc = new UsuariosRootService();
    $panel = $this->panelPrefix();

    $data = [
      'empresa_id' => (int)$this->request->input('empresa_id'),
      'email' => $this->request->input('email'),
      'nombres' => $this->request->input('nombres'),
      'apellidos' => $this->request->input('apellidos'),
      'rol_id' => $this->request->input('rol_id'),
      'password' => $this->request->input('password'),
    ];

    try {
      $userId = $svc->createInicialEmpresaUser($data);
      $this->response->redirect($panel . '/usuarios/' . $userId . '?created=1');
    } catch (\Throwable $e) {
      $this->view('panel_root/usuarios/form', [
        'tenant' => $this->request->tenant,
        'csrf' => Csrf::token(),
        'error' => $e->getMessage(),
        'empresas' => $svc->getEmpresasActivas(),
        'roles' => $svc->getRoles(),
        'row' => $data,
      ], 'panel');
    }
  }

  public function show(): void
  {
    $this->guard();

    $id = (int)($this->request->params['id'] ?? 0);
    $svc = new UsuariosRootService();

    $u = $svc->getUser($id);
    if (!$u) {
      http_response_code(404);
      echo "No encontrado";
      return;
    }

    $this->view('panel_root/usuarios/show', [
      'tenant' => $this->request->tenant,
      'csrf' => Csrf::token(),
      'user' => $u,
      'scopes' => $svc->getScopesAllEmpresas($id),
      'created' => isset($_GET['created']),
      'pwd' => isset($_GET['pwd']),
    ], 'panel');
  }

  public function resetPassword(): void
  {
    $this->guard();

    if (!Csrf::check((string)$this->request->input('_csrf'))) {
      http_response_code(419);
      echo "CSRF inválido";
      return;
    }

    $id = (int)($this->request->params['id'] ?? 0);
    $pwd = (string)$this->request->input('password');

    if (mb_strlen($pwd) < 8) {
      http_response_code(422);
      echo "Password mínimo 8 caracteres";
      return;
    }

    $svc = new UsuariosRootService();
    $u = $svc->getUser($id);
    if (!$u) {
      http_response_code(404);
      echo "No encontrado";
      return;
    }

    $svc->resetPassword($id, $pwd);

    $panel = $this->panelPrefix();
    $this->response->redirect($panel . '/usuarios/' . $id . '?pwd=1');
  }
}
