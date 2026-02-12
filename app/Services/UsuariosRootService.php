<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\DB;
use PDO;

final class UsuariosRootService
{
  private PDO $pdo;

  public function __construct()
  {
    $this->pdo = DB::pdo();
  }

  public function getEmpresasActivas(): array
  {
    $st = $this->pdo->query("SELECT id, razon_social, slug, estado FROM empresas WHERE estado='ACTIVO' ORDER BY razon_social ASC");
    return $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
  }

  public function getRoles(): array
  {
    $st = $this->pdo->query("SELECT id, code, nombre FROM roles ORDER BY id ASC");
    return $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
  }

  public function listGlobal(array $filters): array
  {
    $empresaId = $filters['empresa_id'] ?? null;
    $q = trim((string)($filters['q'] ?? ''));

    $where = [];
    $params = [];

    if ($empresaId) {
      $where[] = "us.empresa_id = :eid";
      $params[':eid'] = (int)$empresaId;
    }

    if ($q !== '') {
      $where[] = "(u.email LIKE :q OR u.nombres LIKE :q OR u.apellidos LIKE :q)";
      $params[':q'] = '%' . $q . '%';
    }

    $whereSql = $where ? ("WHERE " . implode(" AND ", $where)) : "";

    $sql = "
      SELECT
        u.id, u.nombres, u.apellidos, u.email, u.estado, u.created_at,
        COUNT(CASE WHEN us.estado='ACTIVO' THEN 1 END) AS scopes_activos,
        GROUP_CONCAT(DISTINCT CASE WHEN us.estado='ACTIVO' THEN e.slug END ORDER BY e.slug SEPARATOR ', ') AS empresas
      FROM usuarios u
      LEFT JOIN usuario_scope us ON us.usuario_id = u.id
      LEFT JOIN empresas e ON e.id = us.empresa_id
      $whereSql
      GROUP BY u.id
      ORDER BY u.id DESC
      LIMIT 300
    ";

    $st = $this->pdo->prepare($sql);
    $st->execute($params);
    return $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
  }

  public function findUserRootEmpresa(Int $id_Empresa)
  {
    $sql = "
      SELECT
        u.id, u.nombres, u.apellidos, u.email, u.estado, u.created_at, e.razon_social
      FROM usuarios u
      INNER JOIN usuario_scope us ON us.usuario_id = u.id
      INNER JOIN empresas e ON e.id = us.empresa_id
      WHERE e.id = $id_Empresa AND (us.rol_id = 1 OR us.rol_id = 2)
      LIMIT 1
    ";

    $st = $this->pdo->prepare($sql);
    $st->execute();
    return $st->fetch(PDO::FETCH_ASSOC);
  }

  public function createInicialEmpresaUser(array $data): int
  {
    $empresaId = (int)($data['empresa_id'] ?? 0);
    if ($empresaId <= 0) throw new \RuntimeException('Empresa requerida');

    // valida empresa activa
    $st = $this->pdo->prepare("SELECT id FROM empresas WHERE id=:id AND estado='ACTIVO' LIMIT 1");
    $st->execute([':id' => $empresaId]);
    if (!$st->fetch(PDO::FETCH_ASSOC)) throw new \RuntimeException('Empresa inválida o inactiva');

    $svc = new UsuariosService();

    // Forzamos scope nivel empresa
    return $svc->createUserWithScopeEmpresa($empresaId, [
      'email' => $data['email'] ?? '',
      'nombres' => $data['nombres'] ?? '',
      'apellidos' => $data['apellidos'] ?? '',
      'password' => $data['password'] ?? '',
      'rol_id' => $data['rol_id'] ?? 0,
      'establecimiento_id' => '', // <- nivel empresa
    ]);
  }

  public function getUser(int $userId): ?array
  {
    $st = $this->pdo->prepare("SELECT id, nombres, apellidos, email, estado, created_at, updated_at FROM usuarios WHERE id=:id LIMIT 1");
    $st->execute([':id' => $userId]);
    $row = $st->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
  }

  public function getScopesAllEmpresas(int $userId): array
  {
    $sql = "
      SELECT us.id, us.empresa_id, us.establecimiento_id, us.rol_id, us.estado, us.created_at,
             r.nombre AS rol_nombre,
             e.slug AS empresa_slug, e.razon_social AS empresa_razon,
             est.nombre AS establecimiento_nombre
      FROM usuario_scope us
      JOIN roles r ON r.id = us.rol_id
      JOIN empresas e ON e.id = us.empresa_id
      LEFT JOIN establecimientos est ON est.id = us.establecimiento_id
      WHERE us.usuario_id = :uid
      ORDER BY us.id DESC
    ";
    $st = $this->pdo->prepare($sql);
    $st->execute([':uid' => $userId]);
    return $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
  }

  public function resetPassword(int $userId, string $newPassword): void
  {
    $svc = new UsuariosService();
    $svc->resetPassword($userId, $newPassword);
  }
}
