<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Services\Auth;
use App\Services\Csrf;
use App\Services\RateLimiter;
use App\Models\Usuario;

final class MarketingController extends Controller
{
  public function home(): void
  {
    $this->view('index', [
      'csrf' => Csrf::token(),
      'tenant' => $this->request->tenant,
    ], 'panel');
  }
}
