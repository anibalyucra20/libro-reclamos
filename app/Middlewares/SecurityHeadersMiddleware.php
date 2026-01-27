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
    header("Content-Security-Policy: default-src 'self'; img-src 'self' data:; style-src 'self' 'unsafe-inline'; script-src 'self'");
  }
}
