<?php
$displayName = htmlspecialchars($tenant['nombre_comercial'] ?? $tenant['razon_social'] ?? 'Empresa');
?>
<div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3 mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1">Libro de Reclamaciones</h1>
        <p class="text-body-secondary mb-0">
            <i class="bi bi-building me-1"></i>
            <?= $displayName ?>
        </p>
    </div>
</div>
<div class="row g-3">
    <div class="col-12 col-lg-12">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <h2 class="h5 fw-semibold mb-2">¿Deseas registrar un reclamo o queja?</h2>
                <p class="text-body-secondary mb-3">
                    Completa el formulario con tus datos y la descripción del incidente. Te entregaremos un
                    <strong>código de seguimiento</strong> para consultar el estado.
                </p>
                <div id="info" class="mt-4">
                    <div class="alert alert-light border d-flex gap-2 align-items-start">
                        <i class="bi bi-exclamation-circle mt-1"></i>
                        <div>
                            <div class="fw-semibold">Importante</div>
                            <div class="text-body-secondary">
                                Asegúrate de ingresar información verídica y un medio de contacto válido para poder atender tu solicitud.
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <a class="btn btn-outline-primary" href="/reclamo/nuevo">
                            <i class="bi bi-plus-circle me-1"></i>
                            Nuevo reclamo / queja
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a class="btn btn-outline-primary" href="/seguimiento">
                            <i class="bi bi-search me-1"></i>
                            Consultar estado de reclamo
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a class="btn btn-outline-secondary" href="#info">
                            <i class="bi bi-shield-check me-1"></i>
                            Políticas y plazos
                        </a>
                    </div>
                </div>


            </div>
        </div>
    </div>
</div>