<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\DB;
use App\Services\Auth;
use App\Services\ACL;
use App\Services\Csrf;
use App\Services\AlertasReclamosService;

final class AlertasController extends Controller
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
        $this->guard('empresas.gestionar');

        $empresaId = (int)$this->request->tenant['empresa_id'];
        $cfg = $this->getConfig($empresaId);

        // defaults si no existe
        if (!$cfg) {
            $cfg = [
                'emails' => '',
                'dias_antes' => 3,
                'alertar_por_vencer' => 1,
                'alertar_vencidos' => 1,
                'hora_envio' => '09:00',
                'estado' => 'ACTIVO',
            ];
        }

        $this->view('panel/alertas/index', [
            'tenant' => $this->request->tenant,
            'csrf' => Csrf::token(),
            'cfg' => $cfg,
        ], 'panel');
    }

    public function save(): void
    {
        $this->guard('empresas.gestionar');

        $empresaId = (int)$this->request->tenant['empresa_id'];

        $emails = trim((string)$this->request->input('emails', ''));
        $diasAntes = (int)$this->request->input('dias_antes', 3);
        $porVencer = (int)$this->request->input('alertar_por_vencer', 0) === 1 ? 1 : 0;
        $vencidos  = (int)$this->request->input('alertar_vencidos', 0) === 1 ? 1 : 0;
        $horaEnvio = trim((string)$this->request->input('hora_envio', '09:00'));
        $estado    = (string)$this->request->input('estado', 'ACTIVO');

        // validaciones mínimas
        if ($diasAntes < 0) $diasAntes = 0;
        if ($diasAntes > 30) $diasAntes = 30;

        if (!preg_match('/^\d{2}:\d{2}$/', $horaEnvio)) $horaEnvio = '09:00';
        if (!in_array($estado, ['ACTIVO', 'INACTIVO'], true)) $estado = 'ACTIVO';

        // normalizar emails (solo validos)
        $emailsNorm = $this->normalizeEmails($emails);

        $pdo = DB::pdo();
        $sql = "
      INSERT INTO empresa_alertas (empresa_id, emails, dias_antes, alertar_por_vencer, alertar_vencidos, hora_envio, estado)
      VALUES (:empresa_id, :emails, :dias_antes, :por_vencer, :vencidos, :hora_envio, :estado)
      ON DUPLICATE KEY UPDATE
        emails=VALUES(emails),
        dias_antes=VALUES(dias_antes),
        alertar_por_vencer=VALUES(alertar_por_vencer),
        alertar_vencidos=VALUES(alertar_vencidos),
        hora_envio=VALUES(hora_envio),
        estado=VALUES(estado),
        updated_at=NOW()
    ";
        $st = $pdo->prepare($sql);
        $st->execute([
            ':empresa_id' => $empresaId,
            ':emails' => $emailsNorm,
            ':dias_antes' => $diasAntes,
            ':por_vencer' => $porVencer,
            ':vencidos' => $vencidos,
            ':hora_envio' => $horaEnvio,
            ':estado' => $estado,
        ]);

        $this->response->redirect('/alertas?saved=1');
    }

    public function test(): void
    {
        $this->guard('empresas.gestionar');

        // Ejecuta el servicio (envía si hay items por vencer/vencidos)
        // Nota: esto manda correo REAL, no simulado.
        $empresaId = (int)$this->request->tenant['empresa_id'];
        $svc = new AlertasReclamosService();
        $svc->runForEmpresa($empresaId);
        $this->response->redirect('/alertas?test=1');
    }

    private function getConfig(int $empresaId): ?array
    {
        $st = DB::pdo()->prepare("SELECT * FROM empresa_alertas WHERE empresa_id=:id LIMIT 1");
        $st->execute([':id' => $empresaId]);
        $row = $st->fetch();
        return $row ?: null;
    }

    private function normalizeEmails(string $raw): string
    {
        $parts = array_filter(array_map('trim', preg_split('/[,\s;]+/', $raw) ?: []));
        $ok = [];
        foreach ($parts as $e) {
            if (filter_var($e, FILTER_VALIDATE_EMAIL)) $ok[] = strtolower($e);
        }
        $ok = array_values(array_unique($ok));
        return implode(',', $ok);
    }
}
