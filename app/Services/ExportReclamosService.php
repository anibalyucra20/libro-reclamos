<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\DB;
use PDO;

final class ExportReclamosService
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = DB::pdo();
    }

    public function streamRows(int $empresaId, array $filters): iterable
    {
        $desde = $this->safeDate((string)($filters['desde'] ?? ''), date('Y-m-01'));
        $hasta = $this->safeDate((string)($filters['hasta'] ?? ''), date('Y-m-d'));

        $where = "r.empresa_id = :empresa_id AND DATE(r.fecha_registro) BETWEEN :desde AND :hasta";
        $params = [':empresa_id' => $empresaId, ':desde' => $desde, ':hasta' => $hasta];

        $estado = (string)($filters['estado'] ?? '');
        if (in_array($estado, ['REGISTRADO', 'EN_PROCESO', 'RESPONDIDO', 'CERRADO'], true)) {
            $where .= " AND r.estado = :estado";
            $params[':estado'] = $estado;
        }
        $tipo = (string)($filters['tipo'] ?? '');
        if (in_array($tipo, ['RECLAMO', 'QUEJA'], true)) {
            $where .= " AND r.tipo = :tipo";
            $params[':tipo'] = $tipo;
        }


        $sql = "
      SELECT
        r.id, r.codigo_reclamo, r.tipo, r.estado, r.fecha_registro, r.fecha_vencimiento_respuesta,
        e.nombre AS establecimiento_nombre,
        r.consumidor_doc_tipo, r.consumidor_doc_num, r.consumidor_nombres, r.consumidor_apellidos,
        r.consumidor_email, r.consumidor_telefono,
        r.bien_contratado, r.monto_reclamado, r.canal
      FROM reclamos r
      JOIN establecimientos e ON e.id = r.establecimiento_id
      WHERE {$where}
      ORDER BY r.fecha_registro DESC
    ";

        $st = $this->pdo->prepare($sql);
        $st->execute($params);

        while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
            yield $row;
        }
    }

    public function auditarExportacionPorReclamo(array $reclamoIds, int $actorUserId, array $filters): void
    {
        $reclamoIds = array_values(array_unique(array_filter(array_map('intval', $reclamoIds))));
        if (!$reclamoIds) return;

        $max = 2000;
        if (count($reclamoIds) > $max) $reclamoIds = array_slice($reclamoIds, 0, $max);

        $meta = json_encode(['action' => 'csv', 'filters' => $filters], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        $chunkSize = 300;
        for ($i = 0; $i < count($reclamoIds); $i += $chunkSize) {
            $chunk = array_slice($reclamoIds, $i, $chunkSize);

            $values = [];
            $params = [];
            $k = 0;

            foreach ($chunk as $rid) {
                $values[] = "(:rid{$k}, :uid{$k}, :evt{$k}, :meta{$k})";
                $params[":rid{$k}"] = $rid;
                $params[":uid{$k}"] = $actorUserId;
                $params[":evt{$k}"] = 'EXPORTADO';
                $params[":meta{$k}"] = $meta;
                $k++;
            }

            $sql = "INSERT INTO reclamo_eventos (reclamo_id, actor_usuario_id, evento, metadata_json)
              VALUES " . implode(',', $values);
            $st = $this->pdo->prepare($sql);
            $st->execute($params);
        }
    }

    private function safeDate(string $value, string $fallback): string
    {
        return preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) ? $value : $fallback;
    }
    public function outputXlsx(int $empresaId, array $filters, int $actorUserId): void
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Reclamos');

        // Encabezados
        $headers = [
            'ID',
            'Código',
            'Tipo',
            'Estado',
            'Fecha registro',
            'Fecha vencimiento',
            'Establecimiento',
            'Doc tipo',
            'Doc num',
            'Nombres',
            'Apellidos',
            'Email',
            'Teléfono',
            'Bien/Servicio',
            'Monto',
            'Canal'
        ];
        $sheet->fromArray($headers, null, 'A1');

        $rowNum = 2;
        $exportedIds = [];

        foreach ($this->streamRows($empresaId, $filters) as $r) {
            $exportedIds[] = (int)$r['id'];

            $sheet->fromArray([
                $r['id'],
                $r['codigo_reclamo'],
                $r['tipo'],
                $r['estado'],
                (string)$r['fecha_registro'],
                (string)$r['fecha_vencimiento_respuesta'],
                $r['establecimiento_nombre'],
                $r['consumidor_doc_tipo'],
                $r['consumidor_doc_num'],
                $r['consumidor_nombres'],
                $r['consumidor_apellidos'],
                $r['consumidor_email'],
                $r['consumidor_telefono'],
                $r['bien_contratado'],
                $r['monto_reclamado'],
                $r['canal'],
            ], null, 'A' . $rowNum);

            $rowNum++;

            // (opcional) evitar exports gigantes en XLSX
            if ($rowNum > 50000) break;
        }

        // Auditoría
        $this->auditarExportacionPorReclamo($exportedIds, $actorUserId, array_merge($filters, ['format' => 'xlsx']));

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        // mejora compatibilidad / tamaño
        $writer->setPreCalculateFormulas(false);
        $writer->save('php://output');
    }
}
