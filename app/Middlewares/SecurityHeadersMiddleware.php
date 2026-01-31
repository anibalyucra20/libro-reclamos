<?php

declare(strict_types=1);

namespace App\Middlewares;

use App\Core\Request;

final class SecurityHeadersMiddleware
{
  public function handle(Request $request): void
  {
    header('X-Frame-Options: DENY');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: geolocation=(), microphone=(), camera=()');

    // CSP básica (ajusta si usas CDNs)
    $uriPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

    // tu panel puede ser /panel o /admin (usa el mismo config que TenantMiddleware si quieres)
    $panelPath = '/panel';
    $isPanel = ($uriPath === $panelPath) || str_starts_with($uriPath, $panelPath . '/');

    /*$csp = "default-src 'self'; "
      . "img-src 'self' data: https:; "
      . "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; "
      . "font-src 'self' https://cdn.jsdelivr.net data:; "
      . "script-src 'self' https://cdn.jsdelivr.net" . ($isPanel ? " 'unsafe-inline'" : "") . ";";

    header("Content-Security-Policy: " . $csp);*/
  }
}
