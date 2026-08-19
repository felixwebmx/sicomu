<?= $this->extend('layouts/main') ?>

<?= $this->section('contenido') ?>

<div class="card shadow">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-people me-2"></i>Cobro de Aportaciones</span>
        <span class="text-muted small">Apertura: <?= esc($apertura['id']) ?></span>
    </div>
    <div class="card-body">

        <div class="row align-items-end mb-4">
            <div class="col-md-6">
                <label class="form-label fw-bold">Buscar Vecino</label>
                <select id="vecino_select" class="form-select select2" style="width: 100%"></select>
            </div>
            <div class="col-md-6">
                <button type="button" id="btn_buscar" class="btn btn-outline-primary w-100" disabled>
                    <i class="bi bi-search me-1"></i> Consultar Datos
                </button>
            </div>
        </div>

        <!-- Datos del vecino (aparece al seleccionar) -->
        <div id="datos_vecino" class="d-none">
            <div class="alert alert-info mb-4">
                <div class="row">
                    <div class="col-md-4">
                        <small class="text-muted d-block">Vecino</small>
                        <strong id="v_nombre" class="fs-5"></strong>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted d-block">Obra</small>
                        <strong id="v_obra"></strong>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <small class="text-muted d-block">Resto Pendiente</small>
                        <strong id="v_resto" class="fs-4 text-danger"></strong>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-3">
                        <small class="text-muted d-block">Colonia</small>
                        <span id="v_colonia"></span>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted d-block">Vialidad</small>
                        <span id="v_vialidad"></span>
                    </div>
                    <div class="col-md-2">
                        <small class="text-muted d-block">Acera</small>
                        <span id="v_acera"></span>
                    </div>
                    <div class="col-md-2">
                        <small class="text-muted d-block">Metros Lineales</small>
                        <span id="v_ml"></span>
                    </div>
                    <div class="col-md-2 text-md-end">
                        <small class="text-muted d-block">Total Aportación</small>
                        <span id="v_total"></span>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-4">
                        <small class="text-muted d-block">Pagado</small>
                        <span id="v_pagado" class="text-success"></span>
                    </div>
                </div>
            </div>

            <hr>

            <div class="row align-items-end mb-3">
                <div class="col-md-3">
                    <label class="form-label fw-bold">Monto a Pagar</label>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="number" id="monto_pagar" class="form-control text-end fw-bold" step="0.01" min="0.01">
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Método de Pago</label>
                    <select id="metodo_pago" class="form-select">
                        <option value="efectivo">Efectivo</option>
                        <option value="tarjeta">Tarjeta</option>
                        <option value="transferencia">Transferencia</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Observaciones</label>
                    <input type="text" id="observaciones" class="form-control" placeholder="Opcional">
                </div>
                <div class="col-md-2">
                    <button type="button" id="btn_cobrar" class="btn btn-success w-100" disabled>
                        <i class="bi bi-check-circle me-1"></i> Cobrar
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
const CSRF_HEADER_NAME = '<?= csrf_header() ?>';
let csrfHash = '<?= csrf_hash() ?>';
let vecinoSeleccionado = null;

$(document).ready(function () {
    $('#vecino_select').select2({
        theme: 'bootstrap-5',
        placeholder: 'Buscar vecino por nombre o obra...',
        width: '100%',
        ajax: {
            url: '<?= site_url('aportaciones/cobro/buscar-vecino') ?>',
            dataType: 'json',
            delay: 300,
            data: params => ({ q: params.term }),
            processResults: data => ({ results: data.results })
        }
    });

    $('#vecino_select').on('select2:select', function (e) {
        vecinoSeleccionado = e.params.data;
        $('#btn_buscar').prop('disabled', false);
    });

    $('#btn_buscar').on('click', function () {
        if (!vecinoSeleccionado) return;

        // Mostrar todos los datos del vecino
        $('#v_nombre').text(vecinoSeleccionado.nombre_vecino);
        $('#v_obra').text(vecinoSeleccionado.nombre_obra);
        $('#v_colonia').text(vecinoSeleccionado.nombre_colonia || '—');
        $('#v_vialidad').text(vecinoSeleccionado.nombre_vialidad || '—');
        $('#v_acera').text(vecinoSeleccionado.acera === 'D' ? 'Derecha' : (vecinoSeleccionado.acera === 'I' ? 'Izquierda' : vecinoSeleccionado.acera));
        $('#v_ml').text(vecinoSeleccionado.ml.toFixed(2) + ' ml');
        $('#v_resto').text('$' + vecinoSeleccionado.resto.toFixed(2));
        $('#v_total').text('$' + vecinoSeleccionado.total_aportacion.toFixed(2));
        $('#v_pagado').text('$' + vecinoSeleccionado.pagado.toFixed(2));

        // Precargar resto como monto sugerido (pero editable para pagos parciales)
        $('#monto_pagar').val(vecinoSeleccionado.resto.toFixed(2)).attr('max', vecinoSeleccionado.resto);
        $('#datos_vecino').removeClass('d-none');
        $('#btn_cobrar').prop('disabled', false);
    });

    $('#monto_pagar').on('input', function () {
        const resto = vecinoSeleccionado ? vecinoSeleccionado.resto : 0;
        const val = parseFloat($(this).val()) || 0;
        $('#btn_cobrar').prop('disabled', val <= 0 || val > resto);
    });

    $('#btn_cobrar').on('click', function () {
        const monto = parseFloat($('#monto_pagar').val());
        const resto = vecinoSeleccionado ? vecinoSeleccionado.resto : 0;

        if (!vecinoSeleccionado || monto <= 0 || monto > resto) {
            alert('Capture un monto válido (mayor a 0 y no mayor al resto pendiente).');
            return;
        }

        const payload = {
            '<?= csrf_token() ?>': csrfHash,
            vecino_id: vecinoSeleccionado.id,
            monto_pagado: monto,
            metodo_pago: $('#metodo_pago').val(),
            observaciones: $('#observaciones').val().trim() || null
        };

        $.ajax({
            url: '<?= site_url('aportaciones/cobro/guardar') ?>',
            method: 'POST',
            contentType: 'application/json',
            headers: { [CSRF_HEADER_NAME]: csrfHash },
            data: JSON.stringify(payload),
            success: function (resp) {
                if (resp.csrf_hash) csrfHash = resp.csrf_hash;
                if (resp.success) {
                    alert('Cobro registrado. Folio: ' + resp.data.cobro.numero_folio);
                    location.reload();
                } else {
                    alert(resp.message);
                }
            },
            error: function (xhr) {
                if (xhr.responseJSON?.csrf_hash) csrfHash = xhr.responseJSON.csrf_hash;
                alert(xhr.responseJSON?.message || 'Error al registrar el cobro.');
            }
        });
    });
});
</script>
<?= $this->endSection() ?>