<?php
declare(strict_types=1);

namespace App\Middlewares;

use App\Core\Request;
use App\Services\Csrf;

final class CsrfMiddleware
{
  public function handle(Request $request): void
  {
    $method = $request->method();
    if (in_array($method, ['POST','PUT','PATCH','DELETE'], true)) {
      $token = $_POST['_csrf'] ?? null;
      if (!Csrf::check($token)) {
        http_response_code(419);
        echo "CSRF token inválido";
        exit;
      }
    }
  }
}
