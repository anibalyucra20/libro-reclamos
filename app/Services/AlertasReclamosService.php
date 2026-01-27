<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\DB;
use PDO;

final class AlertasReclamosService
{
    private PDO $pdo;
    private Logger $logger;
    private Mailer $mailer;

    public function __construct()
    {
        $this->pdo = DB::pdo();
        $this->logger = new Logger();
        $this->mailer = new Mailer();
    }

    public function run(): array
    {
        $stats = [
            'empresas_procesadas' => 0,
            'emails_enviados' => 0,
            'items_por_vencer' => 0,
            'items_vencidos' => 0,
        ];

        $empresas = $this->pdo->query("SELECT id, razon_social, slug, email_contacto FROM empresas WHERE estado='ACTIVO'")
            ->fetchAll(PDO::FETCH_ASSOC);

        foreach ($empresas as $emp) {
            $empresaId = (int)$emp['id'];
            $cfg = $this->getConfig($empresaId);

            if (!$cfg || $cfg['estado'] !== 'ACTIVO') {
                continue;
            }

            // opcional: respetar hora_envio (simple)
            if (!$this->isHoraEnvio($cfg['hora_envio'] ?? '09:00')) {
                continue;
            }

            $emails = $this->parseEmails((string)$cfg['emails']);
            if (!$emails) continue;

            $diasAntes = (int)$cfg['dias_antes'];

            $porVencer = [];
            if ((int)$cfg['alertar_por_vencer'] === 1) {
                $porVencer = $this->getPorVencer($empresaId, $diasAntes);
                $porVencer = $this->filtrarNoEnviados($empresaId, $porVencer, 'POR_VENCER');
            }

            $vencidos = [];
            if ((int)$cfg['alertar_vencidos'] === 1) {
                $vencidos = $this->getVencidos($empresaId);
                $vencidos = $this->filtrarNoEnviados($empresaId, $vencidos, 'VENCIDO');
            }

            if (!$porVencer && !$vencidos) {
                continue;
            }

            $subject = "Alertas Libro de Reclamaciones - {$emp['razon_social']}";
            $html = $this->renderEmailHtml($emp, $diasAntes, $porVencer, $vencidos);

            Mailer::sendHtml($emails, $subject, $html);

            // registrar envíos (idempotencia)
            foreach ($porVencer as $r) {
                $this->registrarEnvio($empresaId, (int)$r['id'], 'POR_VENCER', $r['fecha_vencimiento_respuesta'], implode(',', $emails));
            }
            foreach ($vencidos as $r) {
                $this->registrarEnvio($empresaId, (int)$r['id'], 'VENCIDO', $r['fecha_vencimiento_respuesta'], implode(',', $emails));
            }

            $stats['empresas_procesadas']++;
            $stats['emails_enviados']++;
            $stats['items_por_vencer'] += count($porVencer);
            $stats['items_vencidos'] += count($vencidos);

            $this->logger->info("Alertas enviadas empresa_id={$empresaId} porVencer=" . count($porVencer) . " vencidos=" . count($vencidos));
        }

        return $stats;
    }

    private function getConfig(int $empresaId): ?array
    {
        $st = $this->pdo->prepare("SELECT * FROM empresa_alertas WHERE empresa_id=:id LIMIT 1");
        $st->execute([':id' => $empresaId]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    private function getPorVencer(int $empresaId, int $diasAntes): array
    {
        $sql = "
            SELECT r.id, r.codigo_reclamo, r.tipo, r.estado, r.fecha_registro, r.fecha_vencimiento_respuesta,
                   e.nombre AS establecimiento_nombre
            FROM reclamos r
            JOIN establecimientos e ON e.id = r.establecimiento_id
            WHERE r.empresa_id = :empresa_id
              AND r.estado IN ('REGISTRADO','EN_PROCESO')
              AND r.fecha_vencimiento_respuesta = DATE_ADD(CURDATE(), INTERVAL :dias_antes DAY)
            ORDER BY r.fecha_vencimiento_respuesta ASC
        ";
        $st = $this->pdo->prepare($sql);
        $st->bindValue(':empresa_id', $empresaId, PDO::PARAM_INT);
        $st->bindValue(':dias_antes', $diasAntes, PDO::PARAM_INT);
        $st->execute();
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getVencidos(int $empresaId): array
    {
        $sql = "
            SELECT r.id, r.codigo_reclamo, r.tipo, r.estado, r.fecha_registro, r.fecha_vencimiento_respuesta,
                   e.nombre AS establecimiento_nombre
            FROM reclamos r
            JOIN establecimientos e ON e.id = r.establecimiento_id
            WHERE r.empresa_id = :empresa_id
              AND r.estado IN ('REGISTRADO','EN_PROCESO')
              AND r.fecha_vencimiento_respuesta < CURDATE()
            ORDER BY r.fecha_vencimiento_respuesta ASC
        ";
        $st = $this->pdo->prepare($sql);
        $st->execute([':empresa_id' => $empresaId]);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    private function filtrarNoEnviados(int $empresaId, array $rows, string $tipo): array
    {
        if (!$rows) return [];

        $out = [];
        $sql = "SELECT 1 FROM alertas_envios WHERE empresa_id=:empresa_id AND reclamo_id=:reclamo_id AND tipo=:tipo LIMIT 1";
        $st = $this->pdo->prepare($sql);

        foreach ($rows as $r) {
            $st->execute([
                ':empresa_id' => $empresaId,
                ':reclamo_id' => (int)$r['id'],
                ':tipo' => $tipo,
            ]);
            $exists = $st->fetchColumn();
            if (!$exists) $out[] = $r;
        }
        return $out;
    }

    private function registrarEnvio(int $empresaId, int $reclamoId, string $tipo, string $fechaObjetivo, string $enviadoA): void
    {
        $sql = "INSERT INTO alertas_envios (empresa_id, reclamo_id, tipo, fecha_objetivo, enviado_a)
                VALUES (:empresa_id, :reclamo_id, :tipo, :fecha_objetivo, :enviado_a)
                ON DUPLICATE KEY UPDATE enviado_a=VALUES(enviado_a)";
        $st = $this->pdo->prepare($sql);
        $st->execute([
            ':empresa_id' => $empresaId,
            ':reclamo_id' => $reclamoId,
            ':tipo' => $tipo,
            ':fecha_objetivo' => $fechaObjetivo,
            ':enviado_a' => $enviadoA,
        ]);
    }

    private function parseEmails(string $emails): array
    {
        $parts = array_filter(array_map('trim', explode(',', $emails)));
        $out = [];
        foreach ($parts as $e) {
            if (filter_var($e, FILTER_VALIDATE_EMAIL)) $out[] = $e;
        }
        return array_values(array_unique($out));
    }

    private function isHoraEnvio(string $hhmm): bool
    {
        $now = new \DateTime('now');
        $target = \DateTime::createFromFormat('H:i', $hhmm);
        if (!$target) return true;
        // misma hora y mismo minuto
        return $now->format('H:i') === $target->format('H:i');
    }


    private function renderEmailHtml(array $empresa, int $diasAntes, array $porVencer, array $vencidos): string
    {
        $h = fn($s) => htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');

        $html = "<h2>Alertas - {$h($empresa['razon_social'])}</h2>";
        $html .= "<p>Fecha: " . date('Y-m-d H:i') . "</p>";

        if ($porVencer) {
            $html .= "<h3>Por vencer (en {$diasAntes} día(s))</h3><ul>";
            foreach ($porVencer as $r) {
                $html .= "<li><b>{$h($r['codigo_reclamo'])}</b> ({$h($r['tipo'])}) - {$h($r['establecimiento_nombre'])} - vence: {$h($r['fecha_vencimiento_respuesta'])} - estado: {$h($r['estado'])}</li>";
            }
            $html .= "</ul>";
        }

        if ($vencidos) {
            $html .= "<h3>Vencidos</h3><ul>";
            foreach ($vencidos as $r) {
                $html .= "<li><b>{$h($r['codigo_reclamo'])}</b> ({$h($r['tipo'])}) - {$h($r['establecimiento_nombre'])} - venció: {$h($r['fecha_vencimiento_respuesta'])} - estado: {$h($r['estado'])}</li>";
            }
            $html .= "</ul>";
        }

        $html .= "<hr><p>Libro de Reclamaciones Digital</p>";
        return $html;
    }
}
