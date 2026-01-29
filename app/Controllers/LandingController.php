<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;

final class LandingController extends Controller
{
  public function index(): void
  {
    $this->view('landing.index', [
      'tenant' => $this->request->tenant,
    ], 'landing'); // layout propio
  }

  // opcionales
  public function pricing(): void
  {
    $this->view('landing.pricing', ['tenant'=>$this->request->tenant], 'landing');
  }

  public function contacto(): void
  {
    $this->view('landing.contacto', ['tenant'=>$this->request->tenant], 'landing');
  }
}
