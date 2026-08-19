<?= $this->extend('layouts/main') ?>

<?= $this->section('css') ?>
<style>
    /* Estilos adicionales para Select2 en AdminLTE */
    .select2-container--bootstrap-5 .select2-selection {
        min-height: 38px;
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        border-radius: 0.375rem;
    }
    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
        padding-left: 0;
        line-height: 1.5;
    }
    .select2-container--bootstrap-5 .select2-dropdown {
        border-color: #dee2e6;
    }
    /* Animación suave para el campo de clave */
    #clave_partida {
        transition: all 0.3s ease;
    }
    #clave_partida.auto-generada {
        background-color: #e8f5e9;
        border-color: #4caf50;
    }
    #clave_partida.manual {
        background-color: #fff3e0;
        border-color: #ff9800;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('contenido') ?>

<div class="card card-info card-outline mb-4">
    <div class="card-header">
        <h3 class="card-title mb-0">
            <i class="bi bi-folder-plus me-2"></i><?= esc($titulo) ?>
        </h3>
    </div>
    <div class="card-body">

        <?php if (session()->getFlashdata('errores')): ?>
            <div class="alert alert-danger">
                <ul class="mb-0 ps-3">
                    <?php foreach (session()->getFlashdata('errores') as $err): ?>
                        <li><?= esc($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?= site_url('catalogos/partidas/guardar' . ($partida ? '/' . $partida['id_partida'] : '')) ?>" method="post" id="formPartida">
            <?= csrf_field() ?>

            <div class="row">
                <!-- SELECT DE CUENTA CON SELECT2 -->
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">
                        <i class="bi bi-folder me-1"></i>Cuenta
                    </label>
                    <select name="id_cuenta" id="select_cuenta" class="form-select" required>
                        <option value="">-- Seleccione una cuenta --</option>
                        <?php foreach ($cuentas as $c): ?>
                            <option value="<?= $c['id_cuenta'] ?>"
                                data-clave-cuenta="<?= esc($c['clave_cuenta']) ?>"
                                <?= (old('id_cuenta', $partida['id_cuenta'] ?? '') == $c['id_cuenta']) ? 'selected' : '' ?>>
                                [<?= esc($c['clave_cuenta']) ?>] <?= esc($c['nombre_cuenta']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="form-text text-muted">
                        Seleccione la cuenta a la que pertenecerá esta partida.
                    </div>
                </div>

                <!-- CLAVE DE PARTIDA (AUTOGENERADA) -->
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">
                        <i class="bi bi-hash me-1"></i>Clave de partida
                        <span id="badge-auto" class="badge bg-success ms-1" style="display:none;">Auto</span>
                        <span id="badge-manual" class="badge bg-warning ms-1" style="display:none;">Manual</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light">
                            <i class="bi bi-key"></i>
                        </span>
                        <input type="number" 
                               name="clave_partida" 
                               id="clave_partida" 
                               class="form-control"
                               value="<?= esc(old('clave_partida', $partida['clave_partida'] ?? '')) ?>" 
                               required
                               min="1"
                               placeholder="Seleccione una cuenta primero">
                        <button type="button" 
                                class="btn btn-outline-secondary" 
                                id="btn-regenerar"
                                title="Regenerar clave automática"
                                disabled>
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                    </div>
                    <div class="form-text" id="mensaje-clave">
                        <?php if ($partida): ?>
                            <span class="text-info"><i class="bi bi-pencil-square me-1"></i>Modo edición: puede mantener o cambiar la clave.</span>
                        <?php else: ?>
                            <span class="text-muted">La clave se generará automáticamente al seleccionar una cuenta.</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- NOMBRE DE LA PARTIDA -->
            <div class="mb-3">
                <label class="form-label fw-bold">
                    <i class="bi bi-tag me-1"></i>Nombre de la partida
                </label>
                <input type="text" 
                       name="nombre_partida" 
                       class="form-control" 
                       value="<?= esc(old('nombre_partida', $partida['nombre_partida'] ?? '')) ?>" 
                       required
                       maxlength="50"
                       placeholder="Ej: Servicio de Limpia, Impuestos a la Propiedad, etc.">
            </div>

            <!-- BOTONES -->
            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-save me-1"></i> Guardar
                </button>
                <a href="<?= site_url('catalogos/partidas') ?>" class="btn btn-danger">
                    <i class="bi bi-x-circle me-1"></i> Cancelar
                </a>
            </div>
        </form>

    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // ═══════════════════════════════════════════════════════════════
    // INICIALIZAR SELECT2 EN EL SELECT DE CUENTAS
    // ═══════════════════════════════════════════════════════════════
    const $selectCuenta = $('#select_cuenta');
    const $inputClave = $('#clave_partida');
    const $btnRegenerar = $('#btn-regenerar');
    const $mensajeClave = $('#mensaje-clave');
    const $badgeAuto = $('#badge-auto');
    const $badgeManual = $('#badge-manual');
    
    $selectCuenta.select2({
        theme: 'bootstrap-5',
        placeholder: '-- Seleccione una cuenta --',
        allowClear: true,
        width: '100%',
        templateResult: function(data) {
            if (!data.id) return data.text;
            const clave = $(data.element).data('clave-cuenta');
            return $(`<span><strong>[${clave}]</strong> ${data.text.split('] ')[1]}</span>`);
        },
        templateSelection: function(data) {
            if (!data.id) return data.text;
            const clave = $(data.element).data('clave-cuenta');
            return $(`<span><strong>[${clave}]</strong> ${data.text.split('] ')[1]}</span>`);
        }
    });

    // ═══════════════════════════════════════════════════════════════
    // VARIABLES DE ESTADO
    // ═══════════════════════════════════════════════════════════════
    let claveAutogenerada = null;
    let esEdicion = <?= $partida ? 'true' : 'false' ?>;

    // ═══════════════════════════════════════════════════════════════
    // FUNCIÓN: Obtener siguiente clave vía AJAX
    // ═══════════════════════════════════════════════════════════════
    function obtenerSiguienteClave(idCuenta) {
        if (!idCuenta) {
            $inputClave.val('');
            $inputClave.prop('placeholder', 'Seleccione una cuenta primero');
            $inputClave.prop('disabled', true);
            $btnRegenerar.prop('disabled', true);
            mostrarEstado('vacio');
            return;
        }

        $inputClave.prop('disabled', false);
        $btnRegenerar.prop('disabled', false);

        fetch(`<?= site_url('catalogos/ajax/siguiente-clave-partida') ?>/${idCuenta}`)
            .then(response => {
                if (!response.ok) throw new Error('Error en la respuesta');
                return response.json();
            })
            .then(data => {
                claveAutogenerada = data.siguiente_clave;
                
                // Solo autogenerar si es nueva partida o el campo está vacío
                if (!esEdicion || !$inputClave.val()) {
                    $inputClave.val(claveAutogenerada);
                    mostrarEstado('auto');
                }
            })
            .catch(error => {
                console.error('Error al obtener siguiente clave:', error);
                $mensajeClave.html('<span class="text-danger"><i class="bi bi-exclamation-triangle me-1"></i>Error al generar clave automática.</span>');
            });
    }

    // ═══════════════════════════════════════════════════════════════
    // FUNCIÓN: Mostrar estado visual del campo
    // ═══════════════════════════════════════════════════════════════
    function mostrarEstado(estado) {
        $inputClave.removeClass('auto-generada manual');
        $badgeAuto.hide();
        $badgeManual.hide();

        switch(estado) {
            case 'auto':
                $inputClave.addClass('auto-generada');
                $badgeAuto.show();
                $mensajeClave.html('<span class="text-success"><i class="bi bi-magic me-1"></i>Clave generada automáticamente. Puede modificarla si lo desea.</span>');
                break;
            case 'manual':
                $inputClave.addClass('manual');
                $badgeManual.show();
                $mensajeClave.html('<span class="text-warning"><i class="bi bi-pencil me-1"></i>Clave modificada manualmente.</span>');
                break;
            case 'vacio':
                $mensajeClave.html('<span class="text-muted">Seleccione una cuenta para generar la clave.</span>');
                break;
        }
    }

    // ═══════════════════════════════════════════════════════════════
    // EVENTO: Cambio de cuenta (Select2)
    // ═══════════════════════════════════════════════════════════════
    $selectCuenta.on('change', function() {
        const idCuenta = $(this).val();
        obtenerSiguienteClave(idCuenta);
    });

    // ═══════════════════════════════════════════════════════════════
    // EVENTO: El usuario modifica la clave manualmente
    // ═══════════════════════════════════════════════════════════════
    $inputClave.on('input', function() {
        const valorActual = parseInt($(this).val());
        if (valorActual !== claveAutogenerada) {
            mostrarEstado('manual');
        } else {
            mostrarEstado('auto');
        }
    });

    // ═══════════════════════════════════════════════════════════════
    // EVENTO: Botón regenerar clave
    // ═══════════════════════════════════════════════════════════════
    $btnRegenerar.on('click', function() {
        const idCuenta = $selectCuenta.val();
        if (idCuenta) {
            obtenerSiguienteClave(idCuenta);
        }
    });

    // ═══════════════════════════════════════════════════════════════
    // INICIALIZACIÓN: Si hay cuenta preseleccionada (edición)
    // ═══════════════════════════════════════════════════════════════
    const cuentaInicial = $selectCuenta.val();
    if (cuentaInicial) {
        obtenerSiguienteClave(cuentaInicial);
        // En edición, marcar como manual si el usuario cambia algo
        if (esEdicion) {
            mostrarEstado('manual');
        }
    } else {
        $inputClave.prop('disabled', true);
        $btnRegenerar.prop('disabled', true);
    }

});
</script>
<?= $this->endSection() ?>