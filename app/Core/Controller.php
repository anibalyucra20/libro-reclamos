<?php
declare(strict_types=1);

namespace App\Core;

abstract class Controller
{
  public function __construct(
    protected Request $request,
    protected Response $response
  ) {}

  protected function view(string $view, array $data = [], string $layout = 'public'): void
  {
    View::render($view, $data, $layout);
  }
}
