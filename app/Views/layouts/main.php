<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($titulo ?? 'SICOMU') ?> | Sistema Integral de Cobros Municipales</title>

    <?= csrf_meta() ?>

    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/vendor/bootstrap/css/bootstrap.min.css') ?>">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/vendor/bootstrap-icons/bootstrap-icons.css') ?>">
    <!-- OverlayScrollbars -->
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/vendor/overlayscrollbars/overlayscrollbars.min.css') ?>">
    <!-- AdminLTE v4 core -->
    <link rel="stylesheet" href="<?= base_url('assets/adminlte/css/adminlte.css') ?>">

    <!-- DataTables Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/3.0.2/css/responsive.bootstrap5.min.css">
	
	<!-- Select2 -->
	<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
	<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

    <?= $this->renderSection('css') ?>
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
<div class="app-wrapper">

    <?= $this->include('partials/header') ?>
    <?= $this->include('partials/sidebar') ?>

    <main class="app-main">
        <div class="app-content-header">
            <div class="container-fluid">
                <h3 class="mb-0"><?= esc($titulo ?? 'Dashboard') ?></h3>
            </div>
        </div>

        <div class="app-content">
            <div class="container-fluid">

                <?php /* ─── Mensajes Flash ─── */ ?>
                <?php if (session()->getFlashdata('mensaje')): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        <?= esc(session()->getFlashdata('mensaje')) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <?= esc(session()->getFlashdata('error')) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('errores')): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <strong>Se encontraron los siguientes errores:</strong>
                        <ul class="mb-0 mt-1 ps-3">
                            <?php foreach (session()->getFlashdata('errores') as $err): ?>
                                <li><?= esc($err) ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
                    </div>
                <?php endif; ?>

                <?= $this->renderSection('contenido') ?>

            </div>
        </div>
    </main>

    <?= $this->include('partials/footer') ?>
</div>

<!-- ═══════════════════════════════════════════════════════════════ -->
<!-- SCRIPTS: Orden es CRÍTICO                                     -->
<!-- ═══════════════════════════════════════════════════════════════ -->

<!-- 1. jQuery (requerido por DataTables) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

<!-- 2. Bootstrap Bundle JS -->
<script src="<?= base_url('assets/adminlte/vendor/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>

<!-- 3. OverlayScrollbars JS -->
<script src="<?= base_url('assets/adminlte/vendor/overlayscrollbars/overlayscrollbars.browser.es6.min.js') ?>"></script>

<!-- 4. AdminLTE v4 core -->
<script src="<?= base_url('assets/adminlte/js/adminlte.js') ?>"></script>

<!-- 5. DataTables Core (después de jQuery) -->
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.min.js"></script>

<!-- 6. DataTables Buttons -->
<script src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.print.min.js"></script>

<!-- 7. DataTables Responsive -->
<script src="https://cdn.datatables.net/responsive/3.0.2/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/3.0.2/js/responsive.bootstrap5.min.js"></script>

<!-- 8. Export dependencies -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.12/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.12/vfs_fonts.min.js"></script>

<!-- 9. Select2 (DEBE cargarse ANTES de las vistas que lo usan) -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
/**
 * Configuración global de DataTables en español
 */
const dtSpanish = {
    "processing": "Procesando...",
    "lengthMenu": "Mostrar _MENU_ registros",
    "zeroRecords": "No se encontraron resultados",
    "emptyTable": "Ningún dato disponible en esta tabla",
    "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
    "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
    "infoFiltered": "(filtrado de un total de _MAX_ registros)",
    "search": "Buscar:",
    "loadingRecords": "Cargando...",
    "paginate": {
        "first": "Primero",
        "last": "Último",
        "next": "Siguiente",
        "previous": "Anterior"
    },
    "aria": {
        "sortAscending": ": Activar para ordenar la columna de manera ascendente",
        "sortDescending": ": Activar para ordenar la columna de manera descendente"
    }
};

/**
 * Configuración base para todas las tablas del sistema.
 * NOTA: DataTables 2.x usa new DataTable() (no $.fn.DataTable)
 */
function initDataTable(selector, options = {}) {
    const defaults = {
        language: dtSpanish, // <-- PASAMOS EL OBJETO DIRECTAMENTE
        responsive: true,
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'Todos']],
        dom: '<"row mb-3"<"col-md-6"B><"col-md-6"f>>' +
             '<"row"<"col-12"tr>>' +
             '<"row mt-3"<"col-md-6"i><"col-md-6"p>>',
        buttons: [
            {
                extend: 'excel',
                text: '<i class="bi bi-file-earmark-excel"></i> Excel',
                className: 'btn btn-success btn-sm',
                exportOptions: { columns: ':not(.no-export)' }
            },
            {
                extend: 'pdf',
                text: '<i class="bi bi-file-earmark-pdf"></i> PDF',
                className: 'btn btn-danger btn-sm',
                exportOptions: { columns: ':not(.no-export)' }
            }
        ],
        order: [[0, 'asc']],
        ...options
    };

    return new DataTable(selector, defaults);
}

/**
 * Envía un formulario POST para acciones destructivas.
 * Incluye automáticamente el token CSRF.
 */
function enviarPost(url, mensajeConfirmacion) {
    if (mensajeConfirmacion && !confirm(mensajeConfirmacion)) {
        return false;
    }

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = url;

    // Buscamos el meta csrf generado por csrf_meta() por su atributo 'name',
    // que empieza con 'csrf-' o coincide con el name configurado en Security.php
    const metaToken = document.querySelector('meta[name="csrf-token"], meta[name="<?= csrf_token() ?>"]');
    if (metaToken) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = '<?= csrf_token() ?>';
        input.value = metaToken.content;
        form.appendChild(input);
    } else {
        console.error('No se encontró el meta CSRF. Revisa csrf_meta() en el head.');
    }

    document.body.appendChild(form);
    form.submit();
    return false;
}
</script>

<?= $this->renderSection('js') ?>

</body>
</html>