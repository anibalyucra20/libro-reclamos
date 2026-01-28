<?php
return [
  'env' => $_ENV['APP_ENV'] ?? 'prod',
  'debug' => (bool)($_ENV['APP_DEBUG'] ?? false),
  'url' => $_ENV['APP_URL'] ?? '',
  'root_domain' => $_ENV['ROOT_DOMAIN'] ?? 'local',
  'panel_path'  => 'panel',
];
