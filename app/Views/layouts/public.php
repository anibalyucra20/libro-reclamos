<!doctype html>
<html lang="es">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <meta name="color-scheme" content="light" />
  <title>Libro de Reclamaciones</title>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Iconos (opcional pero recomendado) -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="icon" href="https://cmplima.org.pe/wp-content/uploads/2022/09/Libro-de-reclamaciones-Azul-300x300-1.png">
  <style>
    :root {
      --app-bg: #f6f7fb;
      --card-border: rgba(15, 23, 42, .08);
      --shadow: 0 10px 30px rgba(2, 6, 23, .08);
    }

    body {
      background:
        radial-gradient(1100px 600px at 15% -10%, rgba(13, 110, 253, .16), transparent 55%),
        radial-gradient(900px 500px at 90% 0%, rgba(25, 135, 84, .12), transparent 55%),
        var(--app-bg);
    }

    .app-card {
      border: 1px solid var(--card-border);
      box-shadow: var(--shadow);
      border-radius: 1rem;
    }

    .navbar {
      backdrop-filter: blur(10px);
      background: rgba(255, 255, 255, .85) !important;
      border-bottom: 1px solid rgba(15, 23, 42, .08);
    }

    .brand-badge {
      width: 36px;
      height: 36px;
      border-radius: 12px;
      background: linear-gradient(135deg, #0d6efd, #66b2ff);
      box-shadow: 0 10px 20px rgba(13, 110, 253, .25);
      display: inline-block;
    }

    .page-title {
      letter-spacing: -0.02em;
    }

    footer {
      color: #6c757d;
    }

    /* Pequeñas mejoras a inputs */
    .form-control,
    .form-select {
      border-radius: .75rem;
    }

    .btn {
      border-radius: .75rem;
      font-weight: 600;
    }

    /* Tablas en móvil: scroll horizontal */
    .table-responsive {
      border-radius: 1rem;
      overflow: hidden;
      border: 1px solid rgba(15, 23, 42, .08);
      background: #fff;
    }
  </style>

  <style>
    .lr-hero {
      background: #f2a51a;
      color: #fff;
      border-radius: 14px;
      padding: 22px 24px;
      position: relative;
      overflow: hidden;
    }

    .lr-hero h1 {
      margin: 0;
      font-size: 26px;
      font-weight: 800;
      letter-spacing: .2px;
    }

    .lr-hero .sub {
      opacity: .95;
      margin-top: 4px;
      line-height: 1.2;
    }

    .lr-hero .sub small {
      opacity: .95;
    }

    .lr-hero .actions {
      position: absolute;
      right: 18px;
      top: 18px;
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
    }

    .lr-card {
      border: 1px solid rgba(0, 0, 0, .08);
      border-radius: 12px;
      background: #fff;
      box-shadow: 0 10px 25px rgba(0, 0, 0, .06);
      overflow: hidden;
    }

    .lr-card-header {
      padding: 14px 18px;
      border-bottom: 1px solid rgba(0, 0, 0, .06);
      background: #fbfbfb;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      flex-wrap: wrap;
    }

    .lr-card-header .title {
      font-weight: 700;
      color: #1f2a37;
    }

    .lr-kv {
      padding: 16px 18px;
    }

    .lr-kv .row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 12px 16px;
    }

    @media (max-width: 992px) {
      .lr-kv .row {
        grid-template-columns: 1fr;
      }

      .lr-hero .actions {
        position: static;
        margin-top: 14px;
      }
    }

    .lr-field {
      border: 1px solid rgba(0, 0, 0, .08);
      border-radius: 10px;
      padding: 12px 14px;
      background: #fff;
    }

    .lr-field .k {
      font-size: 12px;
      color: #6b7280;
      margin-bottom: 2px;
    }

    .lr-field .v {
      font-weight: 650;
      color: #111827;
    }

    .lr-section-title {
      margin: 0;
      padding: 14px 18px;
      font-weight: 800;
      color: #1f2a37;
      border-top: 1px solid rgba(0, 0, 0, .06);
      background: #fbfbfb;
    }

    .lr-body {
      padding: 16px 18px;
    }

    .lr-badge {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 6px 10px;
      border-radius: 999px;
      font-weight: 700;
      font-size: 12px;
      white-space: nowrap;
    }

    .lr-dot {
      width: 9px;
      height: 9px;
      border-radius: 999px;
      display: inline-block;
    }

    .lr-answer {
      border: 1px solid rgba(0, 0, 0, .08);
      border-radius: 12px;
      padding: 14px 14px;
      background: #fff;
      margin-bottom: 10px;
    }

    .lr-answer .meta {
      font-size: 12px;
      color: #6b7280;
      margin-bottom: 8px;
    }

    .lr-answer .text {
      color: #111827;
      line-height: 1.45;
    }

    .lr-timeline {
      list-style: none;
      padding-left: 0;
      margin: 0;
    }

    .lr-timeline li {
      display: grid;
      grid-template-columns: 160px 1fr;
      gap: 10px;
      padding: 10px 0;
      border-bottom: 1px dashed rgba(0, 0, 0, .12);
    }

    .lr-timeline li:last-child {
      border-bottom: 0;
    }

    .lr-timeline .t {
      color: #6b7280;
      font-size: 12px;
    }

    .lr-timeline .e {
      font-weight: 650;
      color: #111827;
    }

    .lr-footer-note {
      margin-top: 18px;
      background: #7a7a7a;
      color: #fff;
      padding: 16px 18px;
      border-radius: 12px;
      text-align: center;
      font-size: 13px;
      line-height: 1.35;
    }

    /* botones alineados con tu look */
    .btn-outline-white {
      border: 1px solid rgba(255, 255, 255, .65);
      color: #fff;
      background: transparent;
    }

    .btn-outline-white:hover {
      background: rgba(255, 255, 255, .12);
      color: #fff;
    }

    /* impresión: ocultar botones */
    @media print {
      .no-print {
        display: none !important;
      }

      .lr-card {
        box-shadow: none;
      }

      .lr-hero {
        border-radius: 0;
      }

      body {
        background: #fff;
      }
    }
  </style>
</head>

<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center gap-2 fw-bold" href="/">
        <i class="bi bi-book me-1"></i>
        <span class="page-title">Libro de Reclamaciones</span>
      </a>

      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#topNav"
        aria-controls="topNav" aria-expanded="false" aria-label="Menú">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="topNav">
        <div class="ms-auto d-flex gap-2 mt-3 mt-lg-0">
          <!-- Botones opcionales (si tienes rutas) -->
          <!--
          <a class="btn btn-outline-primary" href="/reclamos"><i class="bi bi-list-ul me-1"></i> Ver reclamos</a>
          <a class="btn btn-primary" href="/reclamos/nuevo"><i class="bi bi-plus-lg me-1"></i> Nuevo reclamo</a>
          -->
        </div>
      </div>
    </div>
  </nav>

  <!-- Contenido -->
  <main class="container py-4 py-lg-5">
    <div class="app-card bg-white p-3 p-md-4">
      <?php if (!empty($errores)): ?>
        <div class="alert alert-danger">
          <ul>
            <?php foreach ($errores as $e): ?>
              <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>
      <?php if (!empty($_SESSION['flash_success'])): ?>
        <div class="alert alert-success alert-dismissible">
          <?= $_SESSION['flash_success'] ?>
        </div>
        <?php unset($_SESSION['flash_success']); ?>
      <?php endif; ?>
      <?php if (!empty($_SESSION['flash_error'])): ?>
        <div class="alert alert-danger alert-dismissible">
          <?= $_SESSION['flash_error'] ?>
        </div>
        <?php unset($_SESSION['flash_error']); ?>
      <?php endif; ?>
      <?= $content ?>
    </div>
  </main>

  <!-- Footer -->
  <footer class="py-4">
    <div class="container d-flex flex-column flex-md-row gap-2 justify-content-between align-items-md-center">
      <small>© <?= date('Y') ?> • Libro de Reclamaciones</small>
      <small class="text-body-secondary">Atención al cliente • Registro formal</small>
    </div>
  </footer>

  <!-- Bootstrap JS -->
  <script src="/assets/bootstrap.bundle.min.js"></script>
</body>

</html>