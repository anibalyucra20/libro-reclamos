<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\DB;

final class RateLimit
{
    public static function check(string $key, ?string $ip, int $maxHits, int $windowSeconds): void
    {
        $ipBin = self::ipToBinary($ip);
        if ($ipBin === null) return;

        $pdo = DB::pdo();
        $pdo->beginTransaction();

        $st = $pdo->prepare("SELECT id, window_started_at, hits
                         FROM rate_limits
                         WHERE k=:k AND ip=:ip
                         LIMIT 1 FOR UPDATE");
        $st->execute(['k' => $key, 'ip' => $ipBin]);
        $row = $st->fetch();

        $now = date('Y-m-d H:i:s');

        if (!$row) {
            $pdo->prepare("INSERT INTO rate_limits (k, ip, window_started_at, hits)
                     VALUES (:k, :ip, :ws, 1)")
                ->execute(['k' => $key, 'ip' => $ipBin, 'ws' => $now]);
            $pdo->commit();
            return;
        }

        $ws = strtotime((string)$row['window_started_at']);
        $elapsed = time() - $ws;

        if ($elapsed > $windowSeconds) {
            $pdo->prepare("UPDATE rate_limits SET window_started_at=:ws, hits=1 WHERE id=:id")
                ->execute(['ws' => $now, 'id' => (int)$row['id']]);
            $pdo->commit();
            return;
        }

        $hits = (int)$row['hits'] + 1;
        $pdo->prepare("UPDATE rate_limits SET hits=:h WHERE id=:id")
            ->execute(['h' => $hits, 'id' => (int)$row['id']]);

        $pdo->commit();

        if ($hits > $maxHits) {
            http_response_code(429);
            echo "Demasiadas solicitudes. Intenta más tarde.";
            exit;
        }
    }

    private static function ipToBinary(?string $ip): ?string
    {
        if (!$ip) return null;
        $bin = @inet_pton($ip);
        return $bin === false ? null : $bin;
    }
}
