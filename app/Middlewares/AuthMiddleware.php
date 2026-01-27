<?php
declare(strict_types=1);

namespace App\Middlewares;

use App\Core\Request;
use App\Core\Response;
use App\Services\Auth;

final class AuthMiddleware
{
  public function handle(Request $request, Response $response): void
  {
    if (!Auth::check()) {
      $response->redirect('/login');
    }
  }
}
