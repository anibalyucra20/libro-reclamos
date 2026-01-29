<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>WebSigi - Libro de Reclamaciones Digital</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/assets/landing.css">
</head>
<body>
  <header class="border-bottom bg-white">
    <div class="container py-3 d-flex align-items-center justify-content-between">
      <a class="fw-bold text-decoration-none text-dark" href="/">WebSigi</a>
      <nav class="d-flex gap-3">
        <a class="text-decoration-none" href="/pricing">Planes</a>
        <a class="text-decoration-none" href="/contacto">Contacto</a>
        <a class="btn btn-primary btn-sm" href="/panel/login">Ingresar</a>
      </nav>
    </div>
  </header>

  <main>
    <?= $content ?>
  </main>

  <footer class="border-top mt-5">
    <div class="container py-4 text-body-secondary small">
      © <?= date('Y') ?> WebSigi
    </div>
  </footer>
</body>
</html>
