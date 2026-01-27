<?php
return [
  'env' => $_ENV['APP_ENV'] ?? 'prod',
  'debug' => (bool)($_ENV['APP_DEBUG'] ?? false),
  'url' => $_ENV['APP_URL'] ?? '',
  'admin_subdomain' => $_ENV['ADMIN_SUBDOMAIN'] ?? 'admin',
];
