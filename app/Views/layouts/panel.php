<?php

use App\Services\Auth;
use App\Services\ACL;
use App\Services\Csrf;

$__csrf = Csrf::token();
$__user = Auth::user();
$__empresaId = (int)($tenant['empresa_id'] ?? 0);

$__canExport = $__user && $__empresaId > 0
  ? ACL::can((int)$__user['id'], 'reclamos.exportar', $__empresaId, null)
  : false;

$__canReportes = $__user && $__empresaId > 0
  ? ACL::can((int)$__user['id'], 'reclamos.reportes', $__empresaId, null)
  : false;
$__canAlertas = $__user && $__empresaId > 0
  ? ACL::can((int)$__user['id'], 'alertas.gestionar', $__empresaId, null)
  : false;

$__canUsuariosEmpresa = $__user && $__empresaId > 0
  ? ACL::can((int)$__user['id'], 'usuarios.gestionar', $__empresaId, null)
  : false;

$__canUsuariosGlobal = $__user
  ? ACL::can((int)$__user['id'], 'usuarios.gestionar', null, null)
  : false;

// Si es panel_root, usa permiso global; si es panel empresa, usa el de empresa
$__canUsuarios = (($tenant['mode'] ?? '') === 'panel_root') ? $__canUsuariosGlobal : $__canUsuariosEmpresa;


$__canEstab = $__user && $__empresaId > 0
  ? ACL::can((int)$__user['id'], 'establecimientos.gestionar', $__empresaId, null)
  : false;
$__canEmpresasGlobal = $__user ? ACL::can((int)$__user['id'], 'empresas.gestionar', null, null) : false;


// helper para activar item actual
$__path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

$__panelPrefix = rtrim((string)($tenant['panel_prefix'] ?? '/panel'), '/');
if ($__panelPrefix === '') $__panelPrefix = '/panel';

// path normalizado para "active": quita /panel si existe
$__pathNorm = $__path;
if ($__pathNorm === $__panelPrefix) $__pathNorm = '/';
if (str_starts_with($__pathNorm, $__panelPrefix . '/')) {
  $__pathNorm = substr($__pathNorm, strlen($__panelPrefix));
  $__pathNorm = $__pathNorm === '' ? '/' : $__pathNorm;
}

?>

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
<link rel="icon" href="https://cmplima.org.pe/wp-content/uploads/2022/09/Libro-de-reclamaciones-Azul-300x300-1.png">
  <style>
    :root {
      --app-bg: #f6f7fb;
      --border: rgba(15, 23, 42, .10);
      --shadow: 0 10px 30px rgba(2, 6, 23, .08);
    }

    body {
      background:
        radial-gradient(1100px 600px at 15% -10%, rgba(13, 110, 253, .12), transparent 55%),
        radial-gradient(900px 500px at 90% 0%, rgba(25, 135, 84, .10), transparent 55%),
        var(--app-bg);
    }

    .app-shell {
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    .topbar {
      position: sticky;
      top: 0;
      z-index: 1030;
      backdrop-filter: blur(10px);
      background: rgba(255, 255, 255, .85) !important;
      border-bottom: 1px solid var(--border);
    }

    .brand-badge {
      width: 34px;
      height: 34px;
      border-radius: 12px;
      background: linear-gradient(135deg, #0d6efd, #66b2ff);
      box-shadow: 0 10px 20px rgba(13, 110, 253, .20);
      display: inline-block;
    }

    .layout {
      flex: 1;
      display: grid;
      grid-template-columns: 280px 1fr;
    }

    .sidebar {
      border-right: 1px solid var(--border);
      background: rgba(255, 255, 255, .75);
      backdrop-filter: blur(10px);
      padding: 14px;
    }

    .sidebar-card {
      background: #fff;
      border: 1px solid var(--border);
      border-radius: 1rem;
      box-shadow: var(--shadow);
      padding: 12px;
    }

    .main {
      padding: 18px 16px 26px;
    }

    .main-card {
      background: #fff;
      border: 1px solid var(--border);
      border-radius: 1rem;
      box-shadow: var(--shadow);
      padding: 16px;
    }

    .nav-pill {
      border-radius: .9rem;
      padding: .55rem .75rem;
      display: flex;
      align-items: center;
      gap: .6rem;
      color: #0f172a;
      text-decoration: none;
      border: 1px solid transparent;
    }

    .nav-pill:hover {
      background: rgba(13, 110, 253, .06);
      border-color: rgba(13, 110, 253, .10);
    }

    .nav-pill.active {
      background: rgba(13, 110, 253, .12);
      border-color: rgba(13, 110, 253, .18);
      color: #0b5ed7;
      font-weight: 700;
    }

    /* responsive: sidebar a offcanvas en móvil */
    @media (max-width: 991.98px) {
      .layout {
        grid-template-columns: 1fr;
      }

      .sidebar {
        display: none;
      }

      .main {
        padding-top: 14px;
      }
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

          <a class="navbar-brand d-flex align-items-center gap-2 fw-bold mb-0" href="<?= $__panelPrefix ?>/reclamos">
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
          <?php if (($tenant['mode'] ?? '') === 'panel_root'): ?>
            <a class="btn btn-sm btn-outline-primary" href="<?= $__panelPrefix ?>/empresas">
              <i class="bi bi-buildings me-1"></i> Empresas
            </a>
          <?php else: ?>
            <a class="btn btn-sm btn-outline-primary" href="<?= $__panelPrefix ?>/reclamos">
              <i class="bi bi-inbox me-1"></i> Reclamos
            </a>
          <?php endif; ?>
          <?php if ($__user): ?>
            <form method="POST" action="<?= $__panelPrefix ?>/logout" class="d-inline">
              <input type="hidden" name="_csrf" value="<?= htmlspecialchars($__csrf, ENT_QUOTES, 'UTF-8') ?>">
              <button class="btn btn-sm btn-outline-danger" type="submit">
                <i class="bi bi-box-arrow-right me-1"></i> Salir
              </button>
            </form>
          <?php endif; ?>


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

          <?php if (($tenant['mode'] ?? '') === 'panel_root'): ?>
            <a class="nav-pill <?= str_starts_with($__pathNorm, '/empresas') ? 'active' : '' ?>" href="<?= $__panelPrefix ?>/empresas">
              <i class="bi bi-buildings"></i> Empresas
            </a>
          <?php else: ?>
            <a class="nav-pill <?= str_starts_with($__pathNorm, '/reclamos') ? 'active' : '' ?>" href="<?= $__panelPrefix ?>/reclamos">
              <i class="bi bi-inbox"></i> Reclamos
            </a>
          <?php endif; ?>
          <!-- <?php if ($__canExport): ?>
            <a class="nav-pill <?= $__pathNorm === '/reclamos/exportar' ? 'active' : '' ?>"
              href="/reclamos/exportar?desde=<?= date('Y-m-01') ?>&hasta=<?= date('Y-m-d') ?>">
              <i class="bi bi-download"></i> Exportar CSV
            </a>
          <?php endif; ?> -->

          <?php if ($__canReportes): ?>
            <a class="nav-pill <?= str_starts_with($__pathNorm, '/reportes') ? 'active' : '' ?>" href="<?= $__panelPrefix ?>/reportes">
              <i class="bi bi-graph-up"></i> Reportes
            </a>
          <?php endif; ?>
          <?php if ($__canAlertas): ?>
            <a class="nav-pill <?= str_starts_with($__pathNorm, '/alertas') ? 'active' : '' ?>" href="<?= $__panelPrefix ?>/alertas">
              <i class="bi bi-bell"></i> Alertas
            </a>
          <?php endif; ?>
          <?php if ($__canUsuarios): ?>
            <a class="nav-pill <?= str_starts_with($__pathNorm, '/usuarios') ? 'active' : '' ?>" href="<?= $__panelPrefix ?>/usuarios">
              <i class="bi bi-people"></i> Usuarios
            </a>
          <?php endif; ?>
          <?php if ($__canEstab): ?>
            <a class="nav-pill <?= str_starts_with($__pathNorm, '/establecimientos') ? 'active' : '' ?>" href="<?= $__panelPrefix ?>/establecimientos">
              <i class="bi bi-geo"></i> Establecimientos
            </a>
          <?php endif; ?>

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
            <?php if (($tenant['mode'] ?? '') === 'panel_root'): ?>
              <a class="nav-pill <?= str_starts_with($__pathNorm, '/empresas') ? 'active' : '' ?>" href="<?= $__panelPrefix ?>/empresas">
                <i class="bi bi-buildings"></i> Empresas
              </a>
            <?php else: ?>
              <a class="nav-pill <?= str_starts_with($__pathNorm, '/reclamos') ? 'active' : '' ?>" href="<?= $__panelPrefix ?>/reclamos">
                <i class="bi bi-inbox"></i> Reclamos
              </a>
            <?php endif; ?>

            <!-- <?php if ($__canExport): ?>
              <a class="nav-pill <?= $__pathNorm === '/reclamos/exportar' ? 'active' : '' ?>"
                href="/reclamos/exportar?desde=<?= date('Y-m-01') ?>&hasta=<?= date('Y-m-d') ?>">
                <i class="bi bi-download"></i> Exportar CSV
              </a>
            <?php endif; ?>-->

            <?php if ($__canReportes): ?>
              <a class="nav-pill <?= str_starts_with($__pathNorm, '/reportes') ? 'active' : '' ?>" href="<?= $__panelPrefix ?>/reportes">
                <i class="bi bi-graph-up"></i> Reportes
              </a>
            <?php endif; ?>
            <?php if ($__canAlertas): ?>
              <a class="nav-pill <?= str_starts_with($__pathNorm, '/alertas') ? 'active' : '' ?>" href="<?= $__panelPrefix ?>/alertas">
                <i class="bi bi-bell"></i> Alertas
              </a>
            <?php endif; ?>
            <?php if ($__canUsuarios): ?>
              <a class="nav-pill <?= str_starts_with($__pathNorm, '/usuarios') ? 'active' : '' ?>" href="<?= $__panelPrefix ?>/usuarios">
                <i class="bi bi-people"></i> Usuarios
              </a>
            <?php endif; ?>
            <?php if ($__canEstab): ?>
              <a class="nav-pill <?= str_starts_with($__pathNorm, '/establecimientos') ? 'active' : '' ?>" href="<?= $__panelPrefix ?>/establecimientos">
                <i class="bi bi-geo"></i> Establecimientos
              </a>
            <?php endif; ?>

            <!-- Agrega aquí más items si existen:
            <a class="nav-pill" href="/establecimientos"><i class="bi bi-geo"></i> Establecimientos</a>
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

  <script src="/assets/bootstrap.bundle.min.js"></script>
</body>

</html>