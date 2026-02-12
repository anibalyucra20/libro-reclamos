<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\DB;
use App\Models\Empresa;
use App\Services\Auth;
use App\Services\ACL;
use App\Services\Csrf;

final class EmpresasController extends Controller
{
  private function guard(): void
  {
    (new \App\Middlewares\AuthMiddleware())->handle($this->request, $this->response);

    $user = Auth::user();
    if (!$user) {
      http_response_code(403);
      $_SESSION['flash_error'] = "Forbidden";
      header("Location: /");
      exit;
    }

    // ✅ permiso empresas.gestionar con scope GLOBAL (empresa_id NULL)
    if (!ACL::can((int)$user['id'], 'empresas.gestionar', null, null)) {
      http_response_code(403);
      $_SESSION['flash_error'] = "Sin permiso";
      header("Location: /");
      exit;
    }
  }

  public function index(): void
  {
    $this->guard();

    $rows = Empresa::all();

    $this->view('panel_root/empresas/index', [
      'tenant' => $this->request->tenant,
      'rows' => $rows,
    ], 'panel');
  }

  public function nuevo(): void
  {
    $this->guard();

    $this->view('panel_root/empresas/form', [
      'tenant' => $this->request->tenant,
      'csrf' => Csrf::token(),
      'mode' => 'new',
      'row' => [
        'ruc' => '',
        'razon_social' => '',
        'nombre_comercial' => '',
        'slug' => '',
        'email_contacto' => '',
        'telefono_contacto' => '',
        'direccion_fiscal' => '',
        'estado' => 'ACTIVO',
      ],
    ], 'panel');
  }

  public function crear(): void
  {
    $this->guard();

    $data = $this->collectInput();
    $panel = $this->panelPrefix();
    try {
      $id = $this->insert($data);
      $this->response->redirect($panel . '/empresas/' . $id . '?created=1');
    } catch (\Throwable $e) {
      $this->view('panel_root/empresas/form', [
        'tenant' => $this->request->tenant,
        'csrf' => Csrf::token(),
        'mode' => 'new',
        'error' => $e->getMessage(),
        'row' => $data,
      ], 'panel');
    }
  }

  public function edit(): void
  {
    $this->guard();

    $id = (int)($this->request->params['id'] ?? 0);
    $row = Empresa::findById($id);
    if (!$row) {
      $_SESSION['flash_error'] = "Empresa no encontrado";
      //http_response_code(404);
      //echo "No encontrado";
      header("Location: /panel/empresas");
      return;
    }

    $this->view('panel_root/empresas/form', [
      'tenant' => $this->request->tenant,
      'csrf' => Csrf::token(),
      'mode' => 'edit',
      'row' => $row,
      'created' => isset($_GET['created']),
      'saved' => isset($_GET['saved']),
    ], 'panel');
  }

  public function update(): void
  {
    $this->guard();

    $id = (int)($this->request->params['id'] ?? 0);
    $row = Empresa::findById($id);
    if (!$row) {
      $_SESSION['flash_error'] = "Empresa no encontrado";
      //http_response_code(404);
      //echo "No encontrado";
      header("Location: /panel/empresas");
      return;
    }

    $data = $this->collectInput(true);

    $pdo = DB::pdo();
    $sql = "UPDATE empresas SET
        ruc=:ruc,
        razon_social=:razon_social,
        nombre_comercial=:nombre_comercial,
        slug=:slug,
        email_contacto=:email_contacto,
        telefono_contacto=:telefono_contacto,
        direccion_fiscal=:direccion_fiscal,
        estado=:estado,
        updated_at=NOW()
      WHERE id=:id
      LIMIT 1";
    $st = $pdo->prepare($sql);
    $st->execute([
      ':ruc' => $data['ruc'],
      ':razon_social' => $data['razon_social'],
      ':nombre_comercial' => $data['nombre_comercial'] ?: null,
      ':slug' => $data['slug'],
      ':email_contacto' => $data['email_contacto'] ?: null,
      ':telefono_contacto' => $data['telefono_contacto'] ?: null,
      ':direccion_fiscal' => $data['direccion_fiscal'] ?: null,
      ':estado' => $data['estado'],
      ':id' => $id,
    ]);
    $panel = $this->panelPrefix();
    $this->response->redirect($panel . '/empresas/' . $id . '?saved=1');
  }

  private function collectInput(bool $isUpdate = false): array
  {
    $ruc = trim((string)$this->request->input('ruc'));
    $razon = trim((string)$this->request->input('razon_social'));
    $slug = trim((string)$this->request->input('slug'));

    if ($ruc === '' || !preg_match('/^\d{11}$/', $ruc)) throw new \RuntimeException('RUC inválido (11 dígitos)');
    if ($razon === '') throw new \RuntimeException('Razón social requerida');
    if ($slug === '' || !preg_match('/^[a-z0-9][a-z0-9\-]{1,118}[a-z0-9]$/', $slug)) {
      throw new \RuntimeException('Slug inválido (min 3, solo a-z 0-9 y guion)');
    }

    $estado = (string)$this->request->input('estado', 'ACTIVO');
    if (!in_array($estado, ['ACTIVO', 'INACTIVO'], true)) $estado = 'ACTIVO';

    return [
      'ruc' => $ruc,
      'razon_social' => $razon,
      'nombre_comercial' => trim((string)$this->request->input('nombre_comercial', '')),
      'slug' => strtolower($slug),
      'email_contacto' => trim((string)$this->request->input('email_contacto', '')),
      'telefono_contacto' => trim((string)$this->request->input('telefono_contacto', '')),
      'direccion_fiscal' => trim((string)$this->request->input('direccion_fiscal', '')),
      'estado' => $estado,
    ];
  }

  private function insert(array $data): int
  {
    $pdo = DB::pdo();
    $sql = "INSERT INTO empresas
      (ruc, razon_social, nombre_comercial, slug, email_contacto, telefono_contacto, direccion_fiscal, estado)
      VALUES
      (:ruc, :razon_social, :nombre_comercial, :slug, :email_contacto, :telefono_contacto, :direccion_fiscal, :estado)";
    $st = $pdo->prepare($sql);
    $st->execute([
      ':ruc' => $data['ruc'],
      ':razon_social' => $data['razon_social'],
      ':nombre_comercial' => $data['nombre_comercial'] ?: null,
      ':slug' => $data['slug'],
      ':email_contacto' => $data['email_contacto'] ?: null,
      ':telefono_contacto' => $data['telefono_contacto'] ?: null,
      ':direccion_fiscal' => $data['direccion_fiscal'] ?: null,
      ':estado' => $data['estado'],
    ]);
    return (int)$pdo->lastInsertId();
  }
}
