<?php

declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
  public function __construct(
    protected Request $request,
    protected Response $response
  ) {}
  
  public function panelPrefix(): string
  {
    $p = (string)($this->request->tenant['panel_prefix'] ?? '/panel');
    if ($p === '') $p = '/panel';
    if ($p[0] !== '/') $p = '/' . $p;
    return rtrim($p, '/');
  }
  protected function view(string $view, array $data = [], string $layout = 'public'): void
  {
    View::render($view, $data, $layout);
  }
}
