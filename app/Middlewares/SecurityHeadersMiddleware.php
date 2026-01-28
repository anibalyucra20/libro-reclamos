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
    $host = $_SERVER['HTTP_HOST'] ?? '';
    $isPanel = str_contains($host, '.admin.') || str_starts_with($host, 'admin.');

    $csp = "default-src 'self'; "
      . "img-src 'self' data: https:; "
      . "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; "
      . "font-src 'self' https://cdn.jsdelivr.net data:; "
      . "script-src 'self' https://cdn.jsdelivr.net" . ($isPanel ? " 'unsafe-inline'" : "") . ";";

    header("Content-Security-Policy: " . $csp);
  }
}
