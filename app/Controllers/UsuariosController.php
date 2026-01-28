<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Services\Auth;
use App\Services\ACL;
use App\Services\Csrf;
use App\Services\UsuariosService;

final class UsuariosController extends Controller
{
  private function guard(string $perm): void
  {
    (new \App\Middlewares\AuthMiddleware())->handle($this->request, $this->response);

    if (($this->request->tenant['mode'] ?? '') !== 'panel') {
      http_response_code(400);
      echo "Panel inválido";
      exit;
    }

    $user = Auth::user();
    $empresaId = (int)($this->request->tenant['empresa_id'] ?? 0);

    if (!$user || $empresaId <= 0) {
      http_response_code(403);
      echo "Forbidden";
      exit;
    }

    if (!ACL::can((int)$user['id'], $perm, $empresaId, null)) {
      http_response_code(403);
      echo "Sin permiso";
      exit;
    }
  }

  public function index(): void
  {
    $this->guard('usuarios.gestionar');

    $empresaId = (int)$this->request->tenant['empresa_id'];
    $svc = new UsuariosService();

    $users = $svc->listByEmpresa($empresaId);

    $this->view('panel/usuarios/index', [
      'tenant' => $this->request->tenant,
      'users' => $users,
    ], 'panel');
  }

  public function nuevo(): void
  {
    $this->guard('usuarios.gestionar');

    $empresaId = (int)$this->request->tenant['empresa_id'];
    $svc = new UsuariosService();

    $this->view('panel/usuarios/form', [
      'tenant' => $this->request->tenant,
      'csrf' => Csrf::token(),
      'roles' => $svc->getRoles(),
      'establecimientos' => $svc->getEstablecimientos($empresaId),
    ], 'panel');
  }

  public function crear(): void
  {
    $this->guard('usuarios.gestionar');

    $empresaId = (int)$this->request->tenant['empresa_id'];
    $svc = new UsuariosService();

    try {
      $userId = $svc->createUserWithScopeEmpresa($empresaId, [
        'email' => $this->request->input('email'),
        'nombres' => $this->request->input('nombres'),
        'apellidos' => $this->request->input('apellidos'),
        'password' => $this->request->input('password'),
        'rol_id' => $this->request->input('rol_id'),
        'establecimiento_id' => $this->request->input('establecimiento_id', ''),
      ]);
      $this->response->redirect('/usuarios/' . $userId . '?created=1');
    } catch (\Throwable $e) {
      $this->view('panel/usuarios/form', [
        'tenant' => $this->request->tenant,
        'csrf' => Csrf::token(),
        'error' => $e->getMessage(),
        'roles' => $svc->getRoles(),
        'establecimientos' => $svc->getEstablecimientos($empresaId),
      ], 'panel');
    }
  }

  public function show(): void
  {
    $this->guard('usuarios.gestionar');

    $empresaId = (int)$this->request->tenant['empresa_id'];
    $id = (int)($this->request->params['id'] ?? 0);

    $svc = new UsuariosService();
    $u = $svc->getUserInEmpresa($empresaId, $id);
    if (!$u) { http_response_code(404); echo "No encontrado"; return; }

    $this->view('panel/usuarios/show', [
      'tenant' => $this->request->tenant,
      'csrf' => Csrf::token(),
      'user' => $u,
      'scopes' => $svc->getScopes($empresaId, $id),
      'roles' => $svc->getRoles(),
      'establecimientos' => $svc->getEstablecimientos($empresaId),
      'created' => isset($_GET['created']),
      'saved' => isset($_GET['saved']),
      'pwd' => isset($_GET['pwd']),
    ], 'panel');
  }

  public function update(): void
  {
    $this->guard('usuarios.gestionar');

    $empresaId = (int)$this->request->tenant['empresa_id'];
    $id = (int)($this->request->params['id'] ?? 0);

    $svc = new UsuariosService();
    $u = $svc->getUserInEmpresa($empresaId, $id);
    if (!$u) { http_response_code(404); echo "No encontrado"; return; }

    try {
      $svc->updateUser($id, [
        'nombres' => $this->request->input('nombres'),
        'apellidos' => $this->request->input('apellidos'),
        'estado' => $this->request->input('estado', 'ACTIVO'),
      ]);
      $this->response->redirect('/usuarios/' . $id . '?saved=1');
    } catch (\Throwable $e) {
      http_response_code(422);
      echo $e->getMessage();
    }
  }

  public function scopeAdd(): void
  {
    $this->guard('usuarios.gestionar');

    $empresaId = (int)$this->request->tenant['empresa_id'];
    $id = (int)($this->request->params['id'] ?? 0);

    $svc = new UsuariosService();
    $u = $svc->getUserInEmpresa($empresaId, $id);
    if (!$u) { http_response_code(404); echo "No encontrado"; return; }

    $rolId = (int)$this->request->input('rol_id');
    $sidRaw = $this->request->input('establecimiento_id', '');
    $establecimientoId = ($sidRaw === '' ? null : (int)$sidRaw);

    $svc->addScope($empresaId, $id, $rolId, $establecimientoId);
    $this->response->redirect('/usuarios/' . $id . '?saved=1');
  }

  public function scopeDelete(): void
  {
    $this->guard('usuarios.gestionar');

    $empresaId = (int)$this->request->tenant['empresa_id'];
    $id = (int)($this->request->params['id'] ?? 0);
    $scopeId = (int)($this->request->params['scope_id'] ?? 0);

    $svc = new UsuariosService();
    $u = $svc->getUserInEmpresa($empresaId, $id);
    if (!$u) { http_response_code(404); echo "No encontrado"; return; }

    $svc->disableScope($empresaId, $id, $scopeId);
    $this->response->redirect('/usuarios/' . $id . '?saved=1');
  }

  public function resetPassword(): void
  {
    $this->guard('usuarios.gestionar');

    $empresaId = (int)$this->request->tenant['empresa_id'];
    $id = (int)($this->request->params['id'] ?? 0);

    $svc = new UsuariosService();
    $u = $svc->getUserInEmpresa($empresaId, $id);
    if (!$u) { http_response_code(404); echo "No encontrado"; return; }

    try {
      $svc->resetPassword($id, (string)$this->request->input('password'));
      $this->response->redirect('/usuarios/' . $id . '?pwd=1');
    } catch (\Throwable $e) {
      http_response_code(422);
      echo $e->getMessage();
    }
  }
}
