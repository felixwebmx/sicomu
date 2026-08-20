<?= $this->extend('layouts/main') ?>

<?= $this->section('css') ?>
<style>
    /* Select2 en AdminLTE v4 */
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
    /* Estados del campo clave */
    #clave_concepto {
        transition: all 0.3s ease;
    }
    #clave_concepto.auto-generada {
        background-color: #e8f5e9;
        border-color: #4caf50;
    }
    #clave_concepto.manual {
        background-color: #fff3e0;
        border-color: #ff9800;
    }
    #clave_concepto:disabled {
        background-color: #f5f5f5;
    }
    /* Select deshabilitado visual */
    .select-disabled {
        opacity: 0.6;
        pointer-events: none;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('contenido') ?>

<div class="card card-info card-outline mb-4">
    <div class="card-header">
        <h3 class="card-title mb-0">
            <i class="bi bi-file-text me-2"></i><?= esc($titulo) ?>
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

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
        <?php endif; ?>

        <form action="<?= site_url('catalogos/conceptos/guardar' . ($concepto ? '/' . $concepto['id_concepto'] : '')) ?>" method="post" id="formConcepto">
            <?= csrf_field() ?>

            <div class="row">
				<div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">
                        <i class="bi bi-upc-scan me-1"></i>Código SAP
                    </label>
                    <select name="cuenta_sap_id" id="select_cuenta_sap" class="form-select">
                        <option value="">-- Seleccione un código SAP --</option>
                        <?php foreach ($cuentasSap as $sap): ?>
                            <option value="<?= $sap['id'] ?>"
                                <?= (old('cuenta_sap_id', $concepto['cuenta_sap_id'] ?? '') == $sap['id']) ? 'selected' : '' ?>>
                                [<?= esc($sap['codigo_sap']) ?>] <?= esc($sap['descripcion']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="form-text text-muted">Opcional. Varios conceptos pueden compartir el mismo código SAP.</div>
                </div>
			</div>
            <div class="row">
                <!-- SELECT DE CUENTA (Select2) -->
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">
                        <i class="bi bi-folder me-1"></i>Cuenta
                    </label>
                    <select id="select_cuenta" class="form-select">
                        <option value="">-- Seleccione una cuenta --</option>
                        <?php foreach ($cuentas as $c): ?>
                            <option value="<?= $c['id_cuenta'] ?>"
                                data-clave-cuenta="<?= esc($c['clave_cuenta']) ?>"
                                <?= ($idCuentaPreseleccionada == $c['id_cuenta']) ? 'selected' : '' ?>>
                                [<?= esc($c['clave_cuenta']) ?>] <?= esc($c['nombre_cuenta']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="form-text text-muted">
                        Seleccione la cuenta para filtrar las partidas disponibles.
                    </div>
                </div>

                <!-- SELECT DE PARTIDA (Select2 - dependiente de cuenta) -->
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">
                        <i class="bi bi-bookmark me-1"></i>Partida
                        <span id="spinner-partida" class="spinner-border spinner-border-sm text-primary ms-1" style="display:none;"></span>
                    </label>
                    <select name="id_partida" id="select_partida" class="form-select" required>
                        <?php if ($concepto && !empty($partidasActuales)): ?>
                            <?php foreach ($partidasActuales as $p): ?>
                                <option value="<?= $p['id_partida'] ?>"
                                    data-clave-partida="<?= esc($p['clave_partida']) ?>"
                                    <?= ($p['id_partida'] == $concepto['id_partida']) ? 'selected' : '' ?>>
                                    [<?= esc($p['clave_partida']) ?>] <?= esc($p['nombre_partida']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="">-- Seleccione primero una cuenta --</option>
                        <?php endif; ?>
                    </select>
                    <div class="form-text" id="mensaje-partida">
                        <?php if ($concepto): ?>
                            <span class="text-info"><i class="bi bi-pencil-square me-1"></i>Partida actual seleccionada.</span>
                        <?php else: ?>
                            <span class="text-muted">Seleccione una cuenta primero.</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- CLAVE DE CONCEPTO (Autogenerada) -->
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">
                        <i class="bi bi-hash me-1"></i>Clave de concepto
                        <span id="badge-auto" class="badge bg-success ms-1" style="display:none;">Auto</span>
                        <span id="badge-manual" class="badge bg-warning ms-1" style="display:none;">Manual</span>
                    </label>
                    <div class="input-group">
                        <span class="input-group-text bg-light">
                            <i class="bi bi-key"></i>
                        </span>
                        <input type="number" 
                               name="clave_concepto" 
                               id="clave_concepto" 
                               class="form-control"
                               value="<?= esc(old('clave_concepto', $concepto['clave_concepto'] ?? '')) ?>" 
                               required
                               min="1"
                               placeholder="Seleccione una partida primero">
                        <button type="button" 
                                class="btn btn-outline-secondary" 
                                id="btn-regenerar"
                                title="Regenerar clave automática"
                                disabled>
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                    </div>
                    <div class="form-text" id="mensaje-clave">
                        <?php if ($concepto): ?>
                            <span class="text-info"><i class="bi bi-pencil-square me-1"></i>Modo edición: puede mantener o cambiar la clave.</span>
                        <?php else: ?>
                            <span class="text-muted">La clave se generará automáticamente al seleccionar una partida.</span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- MONTO -->
                <div class="col-md-4 mb-3">
                    <label class="form-label fw-bold">
                        <i class="bi bi-currency-dollar me-1"></i>Monto
                    </label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" 
                               step="0.01" 
                               name="monto_concepto" 
                               class="form-control"
                               value="<?= esc(old('monto_concepto', $concepto['monto_concepto'] ?? '')) ?>" 
                               required
                               min="0"
                               placeholder="0.00">
                    </div>
                </div>
            </div>

            <!-- NOMBRE DEL CONCEPTO -->
            <div class="mb-3">
                <label class="form-label fw-bold">
                    <i class="bi bi-tag me-1"></i>Nombre del concepto
                </label>
                <input type="text" 
                       name="nombre_concepto" 
                       class="form-control" 
                       value="<?= esc(old('nombre_concepto', $concepto['nombre_concepto'] ?? '')) ?>" 
                       required
                       maxlength="100"
                       placeholder="Ej: Permiso de Construcción, Recolección de Basura, etc.">
            </div>
			
			<div class="col-md-4 mb-3">
				<label class="form-label">Estatus</label>
				<select name="estatus" class="form-select" required>
					<?php $actual = old('estatus', $cuenta['estatus'] ?? '1'); ?>
					<option value="1" <?= $actual == '1' ? 'selected' : '' ?>>Activo</option>
					<option value="0" <?= $actual == '0' ? 'selected' : '' ?>>Inactivo</option>
				</select>
			</div>

            <!-- BOTONES -->
            <div class="d-flex gap-2 mt-4">
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-save me-1"></i> Guardar
                </button>
                <a href="<?= site_url('catalogos/conceptos') ?>" class="btn btn-danger">
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
    // REFERENCIAS A ELEMENTOS
    // ═══════════════════════════════════════════════════════════════
    const $selectCuenta = $('#select_cuenta');
    const $selectPartida = $('#select_partida');
    const $inputClave = $('#clave_concepto');
    const $btnRegenerar = $('#btn-regenerar');
    const $mensajeClave = $('#mensaje-clave');
    const $mensajePartida = $('#mensaje-partida');
    const $spinnerPartida = $('#spinner-partida');
    const $badgeAuto = $('#badge-auto');
    const $badgeManual = $('#badge-manual');
    
    // ═══════════════════════════════════════════════════════════════
    // VARIABLES DE ESTADO
    // ═══════════════════════════════════════════════════════════════
    let claveAutogenerada = null;
    let esEdicion = <?= $concepto ? 'true' : 'false' ?>;
    let partidaActual = <?= $concepto ? $concepto['id_partida'] : 'null' ?>;

    // ═══════════════════════════════════════════════════════════════
    // INICIALIZAR SELECT2: CUENTA SAP
    // ═══════════════════════════════════════════════════════════════
    $('#select_cuenta_sap').select2({
        theme: 'bootstrap-5',
        placeholder: '-- Seleccione un código SAP --',
        allowClear: true,
        width: '100%'
    });
	// ═══════════════════════════════════════════════════════════════
    // INICIALIZAR SELECT2: CUENTA
    // ═══════════════════════════════════════════════════════════════
    $selectCuenta.select2({
        theme: 'bootstrap-5',
        placeholder: '-- Seleccione una cuenta --',
        allowClear: true,
        width: '100%',
        templateResult: formatCuenta,
        templateSelection: formatCuenta
    });

    // ═══════════════════════════════════════════════════════════════
    // INICIALIZAR SELECT2: PARTIDA (inicialmente deshabilitado si no hay cuenta)
    // ═══════════════════════════════════════════════════════════════
    const cuentaInicial = $selectCuenta.val();
    if (!cuentaInicial) {
        $selectPartida.prop('disabled', true);
    }

    $selectPartida.select2({
        theme: 'bootstrap-5',
        placeholder: '-- Seleccione una partida --',
        allowClear: true,
        width: '100%',
        templateResult: formatPartida,
        templateSelection: formatPartida
    });

    // ═══════════════════════════════════════════════════════════════
    // FUNCIONES DE FORMATO PARA SELECT2
    // ═══════════════════════════════════════════════════════════════
    function formatCuenta(data) {
        if (!data.id) return data.text;
        const clave = $(data.element).data('clave-cuenta');
        return $(`<span><strong class="text-primary">[${clave}]</strong> ${data.text.split('] ')[1] || data.text}</span>`);
    }

    function formatPartida(data) {
        if (!data.id) return data.text;
        const clave = $(data.element).data('clave-partida');
        const nombre = data.text.split('] ')[1] || data.text;
        return $(`<span><strong class="text-info">[${clave}]</strong> ${nombre}</span>`);
    }

    // ═══════════════════════════════════════════════════════════════
    // FUNCIÓN: Cargar partidas por cuenta vía AJAX
    // ═══════════════════════════════════════════════════════════════
    function cargarPartidas(idCuenta, idPartidaPreseleccionada = null) {
        if (!idCuenta) {
            $selectPartida.empty().append('<option value="">-- Seleccione primero una cuenta --</option>');
            $selectPartida.prop('disabled', true);
            $selectPartida.trigger('change');
            resetClave();
            return;
        }

        $spinnerPartida.show();
        $selectPartida.prop('disabled', true);

        fetch(`<?= site_url('catalogos/ajax/partidas-por-cuenta') ?>/${idCuenta}`)
            .then(response => {
                if (!response.ok) throw new Error('Error al cargar partidas');
                return response.json();
            })
            .then(partidas => {
                $selectPartida.empty();
                
                if (partidas.length === 0) {
                    $selectPartida.append('<option value="">-- No hay partidas para esta cuenta --</option>');
                    $mensajePartida.html('<span class="text-warning"><i class="bi bi-exclamation-triangle me-1"></i>Esta cuenta no tiene partidas registradas. <a href="<?= site_url('catalogos/partidas/nuevo') ?>">Crear una partida</a>.</span>');
                } else {
                    $selectPartida.append('<option value="">-- Seleccione una partida --</option>');
                    partidas.forEach(p => {
                        const selected = (idPartidaPreseleccionada && p.id_partida == idPartidaPreseleccionada) ? 'selected' : '';
                        $selectPartida.append(
                            `<option value="${p.id_partida}" data-clave-partida="${p.clave_partida}" ${selected}>` +
                            `[${p.clave_partida}] ${p.nombre_partida}` +
                            `</option>`
                        );
                    });
                    $mensajePartida.html('<span class="text-success"><i class="bi bi-check-circle me-1"></i>Partidas cargadas. Seleccione una.</span>');
                }

                $selectPartida.prop('disabled', false);
                $selectPartida.trigger('change');
            })
            .catch(error => {
                console.error('Error:', error);
                $mensajePartida.html('<span class="text-danger"><i class="bi bi-x-circle me-1"></i>Error al cargar partidas.</span>');
            })
            .finally(() => {
                $spinnerPartida.hide();
            });
    }

    // ═══════════════════════════════════════════════════════════════
    // FUNCIÓN: Obtener siguiente clave de concepto vía AJAX
    // ═══════════════════════════════════════════════════════════════
    function obtenerSiguienteClave(idPartida) {
        if (!idPartida) {
            resetClave();
            return;
        }

        $inputClave.prop('disabled', false);
        $btnRegenerar.prop('disabled', false);

        fetch(`<?= site_url('catalogos/ajax/siguiente-clave-concepto') ?>/${idPartida}`)
            .then(response => {
                if (!response.ok) throw new Error('Error en la respuesta');
                return response.json();
            })
            .then(data => {
                claveAutogenerada = data.siguiente_clave;
                
                // Solo autogenerar si es nuevo concepto o el campo está vacío
                // O si cambió la partida respecto a la original (en edición)
                const cambioPartida = esEdicion && idPartida != partidaActual;
                
                if (!esEdicion || !$inputClave.val() || cambioPartida) {
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
    // FUNCIÓN: Resetear campo clave
    // ═══════════════════════════════════════════════════════════════
    function resetClave() {
        $inputClave.val('');
        $inputClave.prop('disabled', true);
        $inputClave.prop('placeholder', 'Seleccione una partida primero');
        $btnRegenerar.prop('disabled', true);
        claveAutogenerada = null;
        mostrarEstado('vacio');
    }

    // ═══════════════════════════════════════════════════════════════
    // FUNCIÓN: Mostrar estado visual del campo clave
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
                $mensajeClave.html('<span class="text-muted">Seleccione una partida para generar la clave.</span>');
                break;
        }
    }

    // ═══════════════════════════════════════════════════════════════
    // EVENTO: Cambio de cuenta
    // ═══════════════════════════════════════════════════════════════
    $selectCuenta.on('change', function() {
        const idCuenta = $(this).val();
        // Si es edición y la cuenta cambia, limpiamos la partida preseleccionada
        cargarPartidas(idCuenta, null);
        resetClave();
    });

    // ═══════════════════════════════════════════════════════════════
    // EVENTO: Cambio de partida
    // ═══════════════════════════════════════════════════════════════
    $selectPartida.on('change', function() {
        const idPartida = $(this).val();
        if (idPartida) {
            obtenerSiguienteClave(idPartida);
        } else {
            resetClave();
        }
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
        const idPartida = $selectPartida.val();
        if (idPartida) {
            obtenerSiguienteClave(idPartida);
        }
    });

    // ═══════════════════════════════════════════════════════════════
    // INICIALIZACIÓN: Si hay cuenta preseleccionada (edición)
    // ═══════════════════════════════════════════════════════════════
    if (cuentaInicial) {
        // En edición, cargar partidas pero mantener la partida actual seleccionada
        <?php if ($concepto && $idCuentaPreseleccionada): ?>
            cargarPartidas(cuentaInicial, <?= $concepto['id_partida'] ?>);
            // No autogenerar clave en edición inicial
            if (esEdicion) {
                mostrarEstado('manual');
            }
        <?php else: ?>
            cargarPartidas(cuentaInicial);
        <?php endif; ?>
    } else {
        resetClave();
    }

});
</script>
<?= $this->endSection() ?>