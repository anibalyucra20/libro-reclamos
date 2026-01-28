<?php
return [
  'env' => $_ENV['APP_ENV'] ?? 'prod',
  'debug' => (bool)($_ENV['APP_DEBUG'] ?? false),
  'url' => $_ENV['APP_URL'] ?? '',
  'root_domain' => 'websigi.com',
  'panel_path'  => 'panel',
];
