<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\DB;
use PDO;

final class ReportesReclamosService
{
  private PDO $pdo;

  public function __construct()
  {
    $this->pdo = DB::pdo();
  }

  public function dashboard(int $empresaId, array $f): array
  {
    $desde = $this->safeDate((string)($f['desde'] ?? ''), date('Y-m-01'));
    $hasta = $this->safeDate((string)($f['hasta'] ?? ''), date('Y-m-d'));

    $where = "r.empresa_id = :eid AND DATE(r.fecha_registro) BETWEEN :desde AND :hasta";
    $params = [':eid' => $empresaId, ':desde' => $desde, ':hasta' => $hasta];

    $estado = (string)($f['estado'] ?? '');
    if (in_array($estado, ['REGISTRADO', 'EN_PROCESO', 'RESPONDIDO', 'CERRADO'], true)) {
      $where .= " AND r.estado = :estado";
      $params[':estado'] = $estado;
    }

    $tipo = (string)($f['tipo'] ?? '');
    if (in_array($tipo, ['RECLAMO', 'QUEJA'], true)) {
      $where .= " AND r.tipo = :tipo";
      $params[':tipo'] = $tipo;
    }

    $sid = (int)($f['establecimiento_id'] ?? 0);
    if ($sid > 0) {
      $where .= " AND r.establecimiento_id = :sid";
      $params[':sid'] = $sid;
    }

    return [
      'range' => ['desde' => $desde, 'hasta' => $hasta, 'estado' => $estado, 'tipo' => $tipo, 'establecimiento_id' => $sid ?: null],
      'kpis' => $this->kpis($where, $params),
      'por_estado' => $this->groupCount("r.estado", $where, $params),
      'por_tipo' => $this->groupCount("r.tipo", $where, $params),
      'operativo_hoy' => $this->operativoHoy($empresaId, $sid ?: null),
      'serie_diaria' => $this->serieDiaria($where, $params),
      'top_establecimientos' => $this->topEstablecimientos($empresaId, $desde, $hasta),
    ];
  }

  private function kpis(string $where, array $params): array
  {
    $sql = "
      SELECT
        COUNT(*) AS total,
        SUM(CASE WHEN r.estado IN ('REGISTRADO','EN_PROCESO') THEN 1 ELSE 0 END) AS abiertos,
        SUM(CASE WHEN r.estado='RESPONDIDO' THEN 1 ELSE 0 END) AS respondidos,
        SUM(CASE WHEN r.estado='CERRADO' THEN 1 ELSE 0 END) AS cerrados
      FROM reclamos r
      WHERE {$where}
    ";
    $st = $this->pdo->prepare($sql);
    $st->execute($params);
    return $st->fetch(PDO::FETCH_ASSOC) ?: ['total' => 0, 'abiertos' => 0, 'respondidos' => 0, 'cerrados' => 0];
  }

  private function groupCount(string $field, string $where, array $params): array
  {
    $sql = "
      SELECT {$field} AS k, COUNT(*) AS total
      FROM reclamos r
      WHERE {$where}
      GROUP BY {$field}
      ORDER BY total DESC
    ";
    $st = $this->pdo->prepare($sql);
    $st->execute($params);
    return $st->fetchAll(PDO::FETCH_ASSOC);
  }

  private function operativoHoy(int $empresaId, ?int $sid = null): array
  {
    $where = "r.empresa_id = :eid AND r.estado IN ('REGISTRADO','EN_PROCESO')";
    $params = [':eid' => $empresaId];

    if ($sid && $sid > 0) {
      $where .= " AND r.establecimiento_id = :sid";
      $params[':sid'] = $sid;
    }

    $sql = "
      SELECT
        SUM(CASE WHEN r.fecha_vencimiento_respuesta < CURDATE() THEN 1 ELSE 0 END) AS vencidos,
        SUM(CASE WHEN r.fecha_vencimiento_respuesta = CURDATE() THEN 1 ELSE 0 END) AS vencen_hoy,
        SUM(CASE WHEN r.fecha_vencimiento_respuesta BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 3 DAY) THEN 1 ELSE 0 END) AS por_vencer_3
      FROM reclamos r
      WHERE {$where}
    ";
    $st = $this->pdo->prepare($sql);
    $st->execute($params);
    return $st->fetch(PDO::FETCH_ASSOC) ?: ['vencidos' => 0, 'vencen_hoy' => 0, 'por_vencer_3' => 0];
  }

  private function serieDiaria(string $where, array $params): array
  {
    $sql = "
      SELECT DATE(r.fecha_registro) AS dia, COUNT(*) AS total
      FROM reclamos r
      WHERE {$where}
      GROUP BY DATE(r.fecha_registro)
      ORDER BY dia ASC
    ";
    $st = $this->pdo->prepare($sql);
    $st->execute($params);
    return $st->fetchAll(PDO::FETCH_ASSOC);
  }

  private function topEstablecimientos(int $empresaId, string $desde, string $hasta): array
  {
    $sql = "
      SELECT e.id, e.nombre, COUNT(r.id) AS total
      FROM establecimientos e
      LEFT JOIN reclamos r
        ON r.establecimiento_id = e.id
      AND r.empresa_id = :eid1
      AND DATE(r.fecha_registro) BETWEEN :desde AND :hasta
      WHERE e.empresa_id = :eid2
      GROUP BY e.id, e.nombre
      ORDER BY total DESC
      LIMIT 10
    ";

    $st = $this->pdo->prepare($sql);
    $st->execute([
      ':eid1' => $empresaId,
      ':eid2' => $empresaId,
      ':desde' => $desde,
      ':hasta' => $hasta,
    ]);

    return $st->fetchAll(PDO::FETCH_ASSOC);
  }

  private function safeDate(string $v, string $fallback): string
  {
    return preg_match('/^\d{4}-\d{2}-\d{2}$/', $v) ? $v : $fallback;
  }
}
