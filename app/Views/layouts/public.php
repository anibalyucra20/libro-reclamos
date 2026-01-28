<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <meta name="color-scheme" content="light" />
  <title>Libro de Reclamaciones</title>

  <!-- Bootstrap 5 -->
  <link href="/assets//bootstrap.min.css" rel="stylesheet">

  <!-- Iconos (opcional pero recomendado) -->
  <link rel="stylesheet" href="/assets//bootstrap-icons.min.css">

  <style>
    :root{
      --app-bg: #f6f7fb;
      --card-border: rgba(15, 23, 42, .08);
      --shadow: 0 10px 30px rgba(2, 6, 23, .08);
    }

    body{
      background:
        radial-gradient(1100px 600px at 15% -10%, rgba(13,110,253,.16), transparent 55%),
        radial-gradient(900px 500px at 90% 0%, rgba(25,135,84,.12), transparent 55%),
        var(--app-bg);
    }

    .app-card{
      border: 1px solid var(--card-border);
      box-shadow: var(--shadow);
      border-radius: 1rem;
    }

    .navbar{
      backdrop-filter: blur(10px);
      background: rgba(255,255,255,.85) !important;
      border-bottom: 1px solid rgba(15, 23, 42, .08);
    }

    .brand-badge{
      width: 36px;
      height: 36px;
      border-radius: 12px;
      background: linear-gradient(135deg, #0d6efd, #66b2ff);
      box-shadow: 0 10px 20px rgba(13,110,253,.25);
      display: inline-block;
    }

    .page-title{
      letter-spacing: -0.02em;
    }

    footer{
      color: #6c757d;
    }

    /* Pequeñas mejoras a inputs */
    .form-control, .form-select{
      border-radius: .75rem;
    }
    .btn{
      border-radius: .75rem;
      font-weight: 600;
    }

    /* Tablas en móvil: scroll horizontal */
    .table-responsive{
      border-radius: 1rem;
      overflow: hidden;
      border: 1px solid rgba(15, 23, 42, .08);
      background: #fff;
    }
  </style>
</head>

<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg sticky-top">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center gap-2 fw-bold" href="#">
        <span class="brand-badge" aria-hidden="true"></span>
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
