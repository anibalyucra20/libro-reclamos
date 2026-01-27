<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <meta name="color-scheme" content="light" />
  <title>Panel - Libro de Reclamaciones</title>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <style>
    :root{
      --app-bg: #f6f7fb;
      --border: rgba(15, 23, 42, .10);
      --shadow: 0 10px 30px rgba(2, 6, 23, .08);
    }
    body{
      background:
        radial-gradient(1100px 600px at 15% -10%, rgba(13,110,253,.12), transparent 55%),
        radial-gradient(900px 500px at 90% 0%, rgba(25,135,84,.10), transparent 55%),
        var(--app-bg);
    }

    .app-shell{
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    .topbar{
      position: sticky;
      top: 0;
      z-index: 1030;
      backdrop-filter: blur(10px);
      background: rgba(255,255,255,.85) !important;
      border-bottom: 1px solid var(--border);
    }

    .brand-badge{
      width: 34px;
      height: 34px;
      border-radius: 12px;
      background: linear-gradient(135deg, #0d6efd, #66b2ff);
      box-shadow: 0 10px 20px rgba(13,110,253,.20);
      display: inline-block;
    }

    .layout{
      flex: 1;
      display: grid;
      grid-template-columns: 280px 1fr;
    }

    .sidebar{
      border-right: 1px solid var(--border);
      background: rgba(255,255,255,.75);
      backdrop-filter: blur(10px);
      padding: 14px;
    }

    .sidebar-card{
      background: #fff;
      border: 1px solid var(--border);
      border-radius: 1rem;
      box-shadow: var(--shadow);
      padding: 12px;
    }

    .main{
      padding: 18px 16px 26px;
    }

    .main-card{
      background: #fff;
      border: 1px solid var(--border);
      border-radius: 1rem;
      box-shadow: var(--shadow);
      padding: 16px;
    }

    .nav-pill{
      border-radius: .9rem;
      padding: .55rem .75rem;
      display: flex;
      align-items: center;
      gap: .6rem;
      color: #0f172a;
      text-decoration: none;
      border: 1px solid transparent;
    }
    .nav-pill:hover{
      background: rgba(13,110,253,.06);
      border-color: rgba(13,110,253,.10);
    }
    .nav-pill.active{
      background: rgba(13,110,253,.12);
      border-color: rgba(13,110,253,.18);
      color: #0b5ed7;
      font-weight: 700;
    }

    /* responsive: sidebar a offcanvas en móvil */
    @media (max-width: 991.98px){
      .layout{ grid-template-columns: 1fr; }
      .sidebar{ display: none; }
      .main{ padding-top: 14px; }
    }
  </style>
</head>

<body>
  <div class="app-shell">

    <!-- Topbar -->
    <nav class="navbar topbar">
      <div class="container-fluid px-3 px-lg-4">
        <div class="d-flex align-items-center gap-2">
          <button class="btn btn-outline-secondary d-lg-none" type="button"
                  data-bs-toggle="offcanvas" data-bs-target="#sideMenu" aria-controls="sideMenu">
            <i class="bi bi-list"></i>
          </button>

          <a class="navbar-brand d-flex align-items-center gap-2 fw-bold mb-0" href="/reclamos">
            <span class="brand-badge" aria-hidden="true"></span>
            <span>Panel</span>
          </a>

          <?php if (!empty($tenant['empresa_slug'])): ?>
            <span class="badge text-bg-light border">
              <i class="bi bi-building me-1"></i>
              <?= htmlspecialchars($tenant['empresa_slug']) ?>
            </span>
          <?php endif; ?>
        </div>

        <div class="ms-auto d-flex align-items-center gap-2">
          <!-- Acciones rápidas (opcional) -->
          <a class="btn btn-sm btn-outline-primary" href="/reclamos">
            <i class="bi bi-inbox me-1"></i> Reclamos
          </a>
          <!-- Si tienes logout:
          <a class="btn btn-sm btn-outline-danger" href="/logout"><i class="bi bi-box-arrow-right me-1"></i> Salir</a>
          -->
        </div>
      </div>
    </nav>

    <!-- Offcanvas sidebar (mobile) -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="sideMenu" aria-labelledby="sideMenuLabel">
      <div class="offcanvas-header">
        <h5 class="offcanvas-title fw-bold" id="sideMenuLabel">Menú</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
      </div>
      <div class="offcanvas-body">
        <div class="d-grid gap-2">
          <a class="nav-pill active" href="/reclamos">
            <i class="bi bi-inbox"></i> Reclamos
          </a>
          <!-- Agrega aquí más links si existen -->
        </div>
        <hr>
        <small class="text-body-secondary">Panel de administración</small>
      </div>
    </div>

    <!-- Desktop layout -->
    <div class="layout">
      <aside class="sidebar">
        <div class="sidebar-card">
          <div class="d-flex align-items-center justify-content-between mb-2">
            <div class="fw-bold">Navegación</div>
            <span class="text-body-secondary small">Panel</span>
          </div>

          <div class="d-grid gap-2">
            <a class="nav-pill active" href="/reclamos">
              <i class="bi bi-inbox"></i> Reclamos
            </a>
            <!-- Agrega aquí más items si existen:
            <a class="nav-pill" href="/establecimientos"><i class="bi bi-geo"></i> Establecimientos</a>
            <a class="nav-pill" href="/usuarios"><i class="bi bi-people"></i> Usuarios</a>
            -->
          </div>

          <hr class="my-3">

          <div class="small text-body-secondary">
            <?php if (!empty($tenant['empresa_slug'])): ?>
              Empresa: <span class="fw-semibold"><?= htmlspecialchars($tenant['empresa_slug']) ?></span>
            <?php else: ?>
              Empresa: <span class="fw-semibold">—</span>
            <?php endif; ?>
          </div>
        </div>
      </aside>

      <main class="main">
        <div class="container-fluid px-0">
          <div class="main-card">
            <?= $content ?>
          </div>

          <div class="mt-3 text-center">
            <small class="text-body-secondary">© <?= date('Y') ?> • Panel de Reclamos</small>
          </div>
        </div>
      </main>
    </div>

  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
