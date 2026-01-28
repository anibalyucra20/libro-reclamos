<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\DB;
use PDO;

final class UsuariosService
{
  private PDO $pdo;

  public function __construct()
  {
    $this->pdo = DB::pdo();
  }

  public function listByEmpresa(int $empresaId): array
  {
    $sql = "
      SELECT
        u.id, u.nombres, u.apellidos, u.email, u.estado, u.created_at, u.updated_at,
        COUNT(us.id) AS scopes
      FROM usuarios u
      JOIN usuario_scope us ON us.usuario_id = u.id
      WHERE us.empresa_id = :eid
        AND us.estado = 'ACTIVO'
      GROUP BY u.id
      ORDER BY u.id DESC
      LIMIT 200
    ";
    $st = $this->pdo->prepare($sql);
    $st->execute([':eid' => $empresaId]);
    return $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
  }

  public function getUserInEmpresa(int $empresaId, int $userId): ?array
  {
    // valida que tenga al menos un scope en esta empresa
    $sql = "
      SELECT u.id, u.nombres, u.apellidos, u.email, u.estado, u.created_at, u.updated_at
      FROM usuarios u
      WHERE u.id = :uid
        AND EXISTS (
          SELECT 1 FROM usuario_scope us
          WHERE us.usuario_id = u.id
            AND us.empresa_id = :eid
            AND us.estado='ACTIVO'
        )
      LIMIT 1
    ";
    $st = $this->pdo->prepare($sql);
    $st->execute([':eid' => $empresaId, ':uid' => $userId]);
    $row = $st->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
  }

  public function getRoles(): array
  {
    $st = $this->pdo->query("SELECT id, code, nombre FROM roles ORDER BY id ASC");
    return $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
  }

  public function getEstablecimientos(int $empresaId): array
  {
    $st = $this->pdo->prepare("SELECT id, nombre FROM establecimientos WHERE empresa_id=:eid AND estado='ACTIVO' ORDER BY nombre ASC");
    $st->execute([':eid' => $empresaId]);
    return $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
  }

  public function getScopes(int $empresaId, int $userId): array
  {
    $sql = "
      SELECT us.id, us.empresa_id, us.establecimiento_id, us.rol_id, us.estado, us.created_at,
             r.nombre AS rol_nombre, r.code AS rol_code,
             e.nombre AS establecimiento_nombre
      FROM usuario_scope us
      JOIN roles r ON r.id = us.rol_id
      LEFT JOIN establecimientos e ON e.id = us.establecimiento_id
      WHERE us.usuario_id = :uid
        AND us.empresa_id = :eid
      ORDER BY us.id DESC
    ";
    $st = $this->pdo->prepare($sql);
    $st->execute([':eid' => $empresaId, ':uid' => $userId]);
    return $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
  }

  public function createUserWithScopeEmpresa(int $empresaId, array $data): int
  {
    $email = strtolower(trim((string)$data['email']));
    $nombres = trim((string)$data['nombres']);
    $apellidos = trim((string)($data['apellidos'] ?? ''));
    $rolId = (int)$data['rol_id'];
    $establecimientoId = isset($data['establecimiento_id']) && $data['establecimiento_id'] !== '' ? (int)$data['establecimiento_id'] : null;

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) throw new \RuntimeException('Email inválido');
    if ($nombres === '') throw new \RuntimeException('Nombres requeridos');
    if ($rolId <= 0) throw new \RuntimeException('Rol requerido');

    $passwordPlain = (string)($data['password'] ?? '');
    if ($passwordPlain === '') throw new \RuntimeException('Password requerido');
    $hash = password_hash($passwordPlain, PASSWORD_BCRYPT);

    $this->pdo->beginTransaction();

    // ¿existe usuario por email?
    $st = $this->pdo->prepare("SELECT id FROM usuarios WHERE email=:email LIMIT 1");
    $st->execute([':email' => $email]);
    $existing = $st->fetch(PDO::FETCH_ASSOC);

    if ($existing) {
      $userId = (int)$existing['id'];
      // opcional: actualizar nombres si vienen
      $this->pdo->prepare("UPDATE usuarios SET nombres=:n, apellidos=:a, updated_at=NOW() WHERE id=:id")
        ->execute([':n'=>$nombres, ':a'=>$apellidos, ':id'=>$userId]);
    } else {
      $this->pdo->prepare("INSERT INTO usuarios (nombres, apellidos, email, password_hash, estado) VALUES (:n,:a,:e,:p,'ACTIVO')")
        ->execute([':n'=>$nombres, ':a'=>$apellidos, ':e'=>$email, ':p'=>$hash]);
      $userId = (int)$this->pdo->lastInsertId();
    }

    // scope en empresa (o local) para esta empresa
    $sqlScope = "
      INSERT INTO usuario_scope (usuario_id, empresa_id, establecimiento_id, rol_id, estado)
      VALUES (:uid, :eid, :sid, :rid, 'ACTIVO')
      ON DUPLICATE KEY UPDATE estado='ACTIVO'
    ";
    $st2 = $this->pdo->prepare($sqlScope);
    $st2->execute([
      ':uid' => $userId,
      ':eid' => $empresaId,
      ':sid' => $establecimientoId,
      ':rid' => $rolId,
    ]);

    $this->pdo->commit();
    return $userId;
  }

  public function updateUser(int $userId, array $data): void
  {
    $nombres = trim((string)$data['nombres']);
    $apellidos = trim((string)($data['apellidos'] ?? ''));
    $estado = (string)($data['estado'] ?? 'ACTIVO');
    if (!in_array($estado, ['ACTIVO','INACTIVO'], true)) $estado = 'ACTIVO';
    if ($nombres === '') throw new \RuntimeException('Nombres requeridos');

    $st = $this->pdo->prepare("UPDATE usuarios SET nombres=:n, apellidos=:a, estado=:st, updated_at=NOW() WHERE id=:id");
    $st->execute([':n'=>$nombres, ':a'=>$apellidos, ':st'=>$estado, ':id'=>$userId]);
  }

  public function addScope(int $empresaId, int $userId, int $rolId, ?int $establecimientoId): void
  {
    $sql = "
      INSERT INTO usuario_scope (usuario_id, empresa_id, establecimiento_id, rol_id, estado)
      VALUES (:uid,:eid,:sid,:rid,'ACTIVO')
      ON DUPLICATE KEY UPDATE estado='ACTIVO'
    ";
    $st = $this->pdo->prepare($sql);
    $st->execute([
      ':uid'=>$userId,
      ':eid'=>$empresaId,
      ':sid'=>$establecimientoId,
      ':rid'=>$rolId,
    ]);
  }

  public function disableScope(int $empresaId, int $userId, int $scopeId): void
  {
    $sql = "UPDATE usuario_scope SET estado='INACTIVO' WHERE id=:sid AND usuario_id=:uid AND empresa_id=:eid";
    $st = $this->pdo->prepare($sql);
    $st->execute([':sid'=>$scopeId, ':uid'=>$userId, ':eid'=>$empresaId]);
  }

  public function resetPassword(int $userId, string $newPassword): void
  {
    $newPassword = (string)$newPassword;
    if (strlen($newPassword) < 6) throw new \RuntimeException('Password mínimo 6 caracteres');
    $hash = password_hash($newPassword, PASSWORD_BCRYPT);
    $st = $this->pdo->prepare("UPDATE usuarios SET password_hash=:p, updated_at=NOW() WHERE id=:id");
    $st->execute([':p'=>$hash, ':id'=>$userId]);
  }
}
