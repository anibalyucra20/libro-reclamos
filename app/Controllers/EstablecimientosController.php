<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\DB;
use App\Models\Establecimiento;
use App\Services\Auth;
use App\Services\ACL;
use App\Services\Csrf;

final class EstablecimientosController extends Controller
{
    private function guard(): void
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

        if (!ACL::can((int)$user['id'], 'establecimientos.gestionar', $empresaId, null)) {
            http_response_code(403);
            echo "Sin permiso";
            exit;
        }
    }

    public function index(): void
    {
        $this->guard();

        $empresaId = (int)$this->request->tenant['empresa_id'];
        $rows = Establecimiento::allByEmpresaAnyEstado($empresaId);

        $this->view('panel/establecimientos/index', [
            'tenant' => $this->request->tenant,
            'rows' => $rows,
        ], 'panel');
    }

    public function nuevo(): void
    {
        $this->guard();

        $this->view('panel/establecimientos/form', [
            'tenant' => $this->request->tenant,
            'csrf' => Csrf::token(),
            'mode' => 'new',
            'row' => [
                'codigo_identificacion' => '',
                'nombre' => '',
                'direccion' => '',
                'ubigeo' => '',
                'departamento' => '',
                'provincia' => '',
                'distrito' => '',
                'telefono' => '',
                'email' => '',
                'estado' => 'ACTIVO',
            ],
        ], 'panel');
    }

    public function crear(): void
    {
        $this->guard();

        $empresaId = (int)$this->request->tenant['empresa_id'];

        $data = $this->collectInput();
        $data['empresa_id'] = $empresaId;
 $panel = $this->panelPrefix();
        try {
            $id = $this->insert($data);
            $this->response->redirect($panel.'/establecimientos/' . $id . '?created=1');
        } catch (\Throwable $e) {
            $this->view('panel/establecimientos/form', [
                'tenant' => $this->request->tenant,
                'csrf' => Csrf::token(),
                'mode' => 'new',
                'error' => $e->getMessage(),
                'row' => $data + ['estado' => 'ACTIVO'],
            ], 'panel');
        }
    }

    public function edit(): void
    {
        $this->guard();

        $empresaId = (int)$this->request->tenant['empresa_id'];
        $id = (int)($this->request->params['id'] ?? 0);

        $row = Establecimiento::findInEmpresaAnyEstado($empresaId, $id);
        if (!$row) {
            http_response_code(404);
            echo "No encontrado";
            return;
        }

        $this->view('panel/establecimientos/form', [
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

        $empresaId = (int)$this->request->tenant['empresa_id'];
        $id = (int)($this->request->params['id'] ?? 0);

        $row = Establecimiento::findInEmpresaAnyEstado($empresaId, $id);
        if (!$row) {
            http_response_code(404);
            echo "No encontrado";
            return;
        }

        $data = $this->collectInput();
 $panel = $this->panelPrefix();
        try {
            $this->doUpdate($empresaId, $id, $data);
            $this->response->redirect($panel.'/establecimientos/' . $id . '?saved=1');
        } catch (\Throwable $e) {
            http_response_code(422);
            echo $e->getMessage();
        }
    }

    private function collectInput(): array
    {
        $codigo = trim((string)$this->request->input('codigo_identificacion'));
        $nombre = trim((string)$this->request->input('nombre'));
        $direccion = trim((string)$this->request->input('direccion'));

        if ($codigo === '') throw new \RuntimeException('Código requerido');
        if ($nombre === '') throw new \RuntimeException('Nombre requerido');
        if ($direccion === '') throw new \RuntimeException('Dirección requerida');

        $estado = (string)$this->request->input('estado', 'ACTIVO');
        if (!in_array($estado, ['ACTIVO', 'INACTIVO'], true)) $estado = 'ACTIVO';

        return [
            'codigo_identificacion' => $codigo,
            'nombre' => $nombre,
            'direccion' => $direccion,
            'ubigeo' => trim((string)$this->request->input('ubigeo', '')),
            'departamento' => trim((string)$this->request->input('departamento', '')),
            'provincia' => trim((string)$this->request->input('provincia', '')),
            'distrito' => trim((string)$this->request->input('distrito', '')),
            'telefono' => trim((string)$this->request->input('telefono', '')),
            'email' => trim((string)$this->request->input('email', '')),
            'estado' => $estado,
        ];
    }

    private function insert(array $data): int
    {
        $pdo = DB::pdo();

        $sql = "INSERT INTO establecimientos
      (empresa_id, codigo_identificacion, nombre, direccion, ubigeo, departamento, provincia, distrito, telefono, email, estado)
      VALUES
      (:empresa_id, :codigo, :nombre, :direccion, :ubigeo, :departamento, :provincia, :distrito, :telefono, :email, :estado)";
        $st = $pdo->prepare($sql);
        $st->execute([
            ':empresa_id' => (int)$data['empresa_id'],
            ':codigo' => $data['codigo_identificacion'],
            ':nombre' => $data['nombre'],
            ':direccion' => $data['direccion'],
            ':ubigeo' => $data['ubigeo'] ?: null,
            ':departamento' => $data['departamento'] ?: null,
            ':provincia' => $data['provincia'] ?: null,
            ':distrito' => $data['distrito'] ?: null,
            ':telefono' => $data['telefono'] ?: null,
            ':email' => $data['email'] ?: null,
            ':estado' => $data['estado'],
        ]);

        return (int)$pdo->lastInsertId();
    }

    private function doUpdate(int $empresaId, int $id, array $data): void
    {
        $pdo = DB::pdo();

        $sql = "UPDATE establecimientos SET
        codigo_identificacion=:codigo,
        nombre=:nombre,
        direccion=:direccion,
        ubigeo=:ubigeo,
        departamento=:departamento,
        provincia=:provincia,
        distrito=:distrito,
        telefono=:telefono,
        email=:email,
        estado=:estado,
        updated_at=NOW()
      WHERE id=:id AND empresa_id=:empresa_id
      LIMIT 1";
        $st = $pdo->prepare($sql);
        $st->execute([
            ':codigo' => $data['codigo_identificacion'],
            ':nombre' => $data['nombre'],
            ':direccion' => $data['direccion'],
            ':ubigeo' => $data['ubigeo'] ?: null,
            ':departamento' => $data['departamento'] ?: null,
            ':provincia' => $data['provincia'] ?: null,
            ':distrito' => $data['distrito'] ?: null,
            ':telefono' => $data['telefono'] ?: null,
            ':email' => $data['email'] ?: null,
            ':estado' => $data['estado'],
            ':id' => $id,
            ':empresa_id' => $empresaId,
        ]);
    }
}
