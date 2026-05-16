<?php
$__panelPrefix = rtrim((string)($tenant['panel_prefix'] ?? '/panel'), '/');
if ($__panelPrefix === '') $__panelPrefix = '/panel';
?>
<?php if (!empty($error)): ?>
  <p style="color:red;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>
<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="card shadow-lg border-0" style="max-width: 420px; width: 100%; border-radius: 16px;">
        <div class="card-body p-4 p-md-5">
            <!-- Encabezado del Login -->
            <div class="text-center mb-4">
                <div class="mb-3 d-inline-block p-3 bg-light rounded-circle text-success">
                    <!-- Icono de candado simple (puedes cambiarlo por tu logo) -->
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-lock-fill" viewBox="0 0 16 16">
                        <path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2m3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2"/>
                    </svg>
                </div>
                <h4 class="fw-bold text-dark mb-1">Bienvenido</h4>
                <p class="text-muted small">Ingresa tus credenciales para acceder al sistema</p>
            </div>
            <!-- Formulario -->
            <form method="POST" action="<?= $__panelPrefix ?>/login">
                <input type="hidden" name="_csrf" value="<?= htmlspecialchars($csrf) ?>">
                <!-- Grupo: Correo -->
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-secondary mb-1">Correo Electrónico</label>
                    <input type="email" 
                           name="email" 
                           class="form-control form-control-lg border-2 bg-light bg-opacity-50" 
                           placeholder="nombre@correo.com"
                           style="border-color: #e9ecef; font-size: 0.95rem; border-radius: 8px;" 
                           required>
                </div>
                <!-- Grupo: Contraseña -->
                <div class="mb-4">
                    <label class="form-label small fw-semibold text-secondary mb-1">Contraseña</label>
                    <input type="password" 
                           name="password" 
                           class="form-control form-control-lg border-2 bg-light bg-opacity-50" 
                           placeholder="••••••••"
                           style="border-color: #e9ecef; font-size: 0.95rem; border-radius: 8px;" 
                           required>
                </div>

                <!-- Botón de Ingreso -->
                <div class="d-grid">
                    <button type="submit" 
                            class="btn btn-success btn-lg fw-bold shadow-sm py-2" 
                            style="border-radius: 8px; font-size: 1rem; transition: all 0.3s ease;">
                        Ingresar al Sistema
                    </button>
                </div>
                
            </form>
        </div>
    </div>
</div>
