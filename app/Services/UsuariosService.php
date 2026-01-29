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
        SUM(CASE WHEN us.estado='ACTIVO' THEN 1 ELSE 0 END) AS scopes
      FROM usuarios u
      JOIN usuario_scope us ON us.usuario_id = u.id AND us.empresa_id = :eid
      GROUP BY u.id, u.nombres, u.apellidos, u.email, u.estado, u.created_at, u.updated_at
      HAVING scopes > 0
      ORDER BY u.id DESC
      LIMIT 200
    ";
    $st = $this->pdo->prepare($sql);
    $st->execute([':eid' => $empresaId]);
    return $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
  }

  public function getUserInEmpresa(int $empresaId, int $userId): ?array
  {
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
    // Si tienes roles globales y quieres ocultarlos en panel empresa,
    // aquí es donde filtras por code (whitelist). Si no, déjalo así.
    $st = $this->pdo->query("SELECT id, code, nombre FROM roles ORDER BY id ASC");
    return $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
  }

  public function getEstablecimientos(int $empresaId): array
  {
    $st = $this->pdo->prepare("
      SELECT id, nombre
      FROM establecimientos
      WHERE empresa_id=:eid AND estado='ACTIVO'
      ORDER BY nombre ASC
    ");
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

  private function assertRoleExists(int $rolId): void
  {
    if ($rolId <= 0) throw new \RuntimeException('Rol requerido');
    $st = $this->pdo->prepare("SELECT id FROM roles WHERE id=:id LIMIT 1");
    $st->execute([':id' => $rolId]);
    if (!$st->fetch(PDO::FETCH_ASSOC)) {
      throw new \RuntimeException('Rol inválido');
    }
  }

  private function assertEstablecimientoInEmpresa(int $empresaId, ?int $establecimientoId): void
  {
    if ($establecimientoId === null) return;

    if ($establecimientoId <= 0) {
      throw new \RuntimeException('Establecimiento inválido');
    }

    $st = $this->pdo->prepare("
      SELECT id
      FROM establecimientos
      WHERE id=:id AND empresa_id=:eid AND estado='ACTIVO'
      LIMIT 1
    ");
    $st->execute([':id' => $establecimientoId, ':eid' => $empresaId]);
    if (!$st->fetch(PDO::FETCH_ASSOC)) {
      throw new \RuntimeException('Establecimiento no pertenece a la empresa');
    }
  }

  private function assertUserExists(int $userId): void
  {
    $st = $this->pdo->prepare("SELECT id FROM usuarios WHERE id=:id LIMIT 1");
    $st->execute([':id' => $userId]);
    if (!$st->fetch(PDO::FETCH_ASSOC)) {
      throw new \RuntimeException('Usuario no existe');
    }
  }

  public function createUserWithScopeEmpresa(int $empresaId, array $data): int
  {
    $email = strtolower(trim((string)($data['email'] ?? '')));
    $nombres = trim((string)($data['nombres'] ?? ''));
    $apellidos = trim((string)($data['apellidos'] ?? ''));
    $rolId = (int)($data['rol_id'] ?? 0);
    $establecimientoId = (isset($data['establecimiento_id']) && $data['establecimiento_id'] !== '')
      ? (int)$data['establecimiento_id']
      : null;

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) throw new \RuntimeException('Email inválido');
    if ($nombres === '') throw new \RuntimeException('Nombres requeridos');

    $this->assertRoleExists($rolId);
    $this->assertEstablecimientoInEmpresa($empresaId, $establecimientoId);

    $passwordPlain = (string)($data['password'] ?? '');
    if (mb_strlen($passwordPlain) < 8) throw new \RuntimeException('Password mínimo 8 caracteres');

    $hash = password_hash($passwordPlain, PASSWORD_BCRYPT);

    $this->pdo->beginTransaction();
    try {
      // ¿existe usuario por email?
      $st = $this->pdo->prepare("SELECT id FROM usuarios WHERE email=:email LIMIT 1");
      $st->execute([':email' => $email]);
      $existing = $st->fetch(PDO::FETCH_ASSOC);

      if ($existing) {
        $userId = (int)$existing['id'];
        // opcional: actualizar nombres si vienen
        $this->pdo->prepare("
          UPDATE usuarios
          SET nombres=:n, apellidos=:a, updated_at=NOW()
          WHERE id=:id
        ")->execute([':n' => $nombres, ':a' => $apellidos, ':id' => $userId]);
      } else {
        $this->pdo->prepare("
          INSERT INTO usuarios (nombres, apellidos, email, password_hash, estado)
          VALUES (:n,:a,:e,:p,'ACTIVO')
        ")->execute([':n' => $nombres, ':a' => $apellidos, ':e' => $email, ':p' => $hash]);
        $userId = (int)$this->pdo->lastInsertId();
      }

      // Si es scope "empresa" (sid NULL), evita el problema de UNIQUE+NULL duplicable
      if ($establecimientoId === null) {
        $stChk = $this->pdo->prepare("
          SELECT id FROM usuario_scope
          WHERE usuario_id=:uid AND empresa_id=:eid AND establecimiento_id IS NULL
          LIMIT 1
        ");
        $stChk->execute([':uid' => $userId, ':eid' => $empresaId]);
        $row = $stChk->fetch(PDO::FETCH_ASSOC);

        if ($row) {
          $this->pdo->prepare("
            UPDATE usuario_scope
            SET rol_id=:rid, estado='ACTIVO'
            WHERE id=:id
          ")->execute([':rid' => $rolId, ':id' => (int)$row['id']]);
        } else {
          $this->pdo->prepare("
            INSERT INTO usuario_scope (usuario_id, empresa_id, establecimiento_id, rol_id, estado)
            VALUES (:uid, :eid, NULL, :rid, 'ACTIVO')
          ")->execute([':uid' => $userId, ':eid' => $empresaId, ':rid' => $rolId]);
        }
      } else {
        // scope establecimiento
        $sqlScope = "
          INSERT INTO usuario_scope (usuario_id, empresa_id, establecimiento_id, rol_id, estado)
          VALUES (:uid, :eid, :sid, :rid, 'ACTIVO')
          ON DUPLICATE KEY UPDATE rol_id=VALUES(rol_id), estado='ACTIVO'
        ";
        $this->pdo->prepare($sqlScope)->execute([
          ':uid' => $userId,
          ':eid' => $empresaId,
          ':sid' => $establecimientoId,
          ':rid' => $rolId,
        ]);
      }

      $this->pdo->commit();
      return $userId;
    } catch (\Throwable $e) {
      $this->pdo->rollBack();
      throw $e;
    }
  }

  public function updateUser(int $userId, array $data): void
  {
    $this->assertUserExists($userId);

    $nombres = trim((string)($data['nombres'] ?? ''));
    $apellidos = trim((string)($data['apellidos'] ?? ''));
    $estado = (string)($data['estado'] ?? 'ACTIVO');
    if (!in_array($estado, ['ACTIVO', 'INACTIVO'], true)) $estado = 'ACTIVO';
    if ($nombres === '') throw new \RuntimeException('Nombres requeridos');

    $st = $this->pdo->prepare("
      UPDATE usuarios
      SET nombres=:n, apellidos=:a, estado=:st, updated_at=NOW()
      WHERE id=:id
    ");
    $st->execute([':n' => $nombres, ':a' => $apellidos, ':st' => $estado, ':id' => $userId]);
  }

  public function addScope(int $empresaId, int $userId, int $rolId, ?int $establecimientoId): void
  {
    $this->assertUserExists($userId);
    $this->assertRoleExists($rolId);
    $this->assertEstablecimientoInEmpresa($empresaId, $establecimientoId);

    // Si scope empresa (sid NULL), evita duplicación por NULL en UNIQUE
    if ($establecimientoId === null) {
      $stChk = $this->pdo->prepare("
        SELECT id FROM usuario_scope
        WHERE usuario_id=:uid AND empresa_id=:eid AND establecimiento_id IS NULL
        LIMIT 1
      ");
      $stChk->execute([':uid' => $userId, ':eid' => $empresaId]);
      $row = $stChk->fetch(PDO::FETCH_ASSOC);

      if ($row) {
        $this->pdo->prepare("
          UPDATE usuario_scope
          SET rol_id=:rid, estado='ACTIVO'
          WHERE id=:id
        ")->execute([':rid' => $rolId, ':id' => (int)$row['id']]);
        return;
      }

      $this->pdo->prepare("
        INSERT INTO usuario_scope (usuario_id, empresa_id, establecimiento_id, rol_id, estado)
        VALUES (:uid,:eid,NULL,:rid,'ACTIVO')
      ")->execute([':uid' => $userId, ':eid' => $empresaId, ':rid' => $rolId]);

      return;
    }

    $sql = "
      INSERT INTO usuario_scope (usuario_id, empresa_id, establecimiento_id, rol_id, estado)
      VALUES (:uid,:eid,:sid,:rid,'ACTIVO')
      ON DUPLICATE KEY UPDATE rol_id=VALUES(rol_id), estado='ACTIVO'
    ";
    $this->pdo->prepare($sql)->execute([
      ':uid' => $userId,
      ':eid' => $empresaId,
      ':sid' => $establecimientoId,
      ':rid' => $rolId,
    ]);
  }

  public function disableScope(int $empresaId, int $userId, int $scopeId): void
  {
    $st = $this->pdo->prepare("
      UPDATE usuario_scope
      SET estado='INACTIVO'
      WHERE id=:sid AND usuario_id=:uid AND empresa_id=:eid
    ");
    $st->execute([':sid' => $scopeId, ':uid' => $userId, ':eid' => $empresaId]);

    if ($st->rowCount() < 1) {
      throw new \RuntimeException('Scope no encontrado');
    }
  }

  public function resetPassword(int $userId, string $newPassword): void
  {
    $this->assertUserExists($userId);

    $newPassword = (string)$newPassword;
    if (mb_strlen($newPassword) < 8) throw new \RuntimeException('Password mínimo 8 caracteres');

    $hash = password_hash($newPassword, PASSWORD_BCRYPT);
    $st = $this->pdo->prepare("UPDATE usuarios SET password_hash=:p, updated_at=NOW() WHERE id=:id");
    $st->execute([':p' => $hash, ':id' => $userId]);
  }
}
