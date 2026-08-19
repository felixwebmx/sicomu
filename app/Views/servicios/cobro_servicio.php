<?= $this->extend('layouts/main') ?>

<?= $this->section('contenido') ?>

<div class="card shadow">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-cash-register me-2"></i>Caja - Cobro de Servicios</span>
        <span class="text-muted small">Apertura: <?= esc($apertura['id']) ?></span>
    </div>
    <div class="card-body">

        <div class="row mb-3">
            <div class="col-md-5">
                <label class="form-label fw-bold">Nombre del Contribuyente</label>
                <input type="text" id="nombre_contribuyente" class="form-control" required>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">RFC</label>
                <input type="text" id="rfc_contribuyente" class="form-control" maxlength="13">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold">Método de Pago</label>
                <select id="metodo_pago" class="form-select">
                    <option value="efectivo">Efectivo</option>
                    <option value="tarjeta">Tarjeta</option>
                    <option value="transferencia">Transferencia</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold">Fecha</label>
                <input type="text" class="form-control" value="<?= esc($fecha) ?>" disabled>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label fw-bold">Domicilio</label>
                <input type="text" id="domicilio_contribuyente" class="form-control">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold">Ext.</label>
                <input type="text" id="ext_contribuyente" class="form-control">
            </div>
            <div class="col-md-1">
                <label class="form-label fw-bold">Bis</label>
                <input type="text" id="bis_contribuyente" class="form-control">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold">Int.</label>
                <input type="text" id="int_contribuyente" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">Colonia</label>
                <input type="text" id="colonia_contribuyente" class="form-control">
            </div>
        </div>

        <hr>

        <div class="row align-items-end mb-3">
            <div class="col-md-6">
                <label class="form-label fw-bold">Concepto</label>
                <select id="concepto_select" class="form-select select2" style="width: 100%"></select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold">Cantidad</label>
                <input type="number" id="concepto_cantidad" class="form-control" value="1" min="0.01" step="0.01">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold">Monto Unit.</label>
                <input type="number" id="concepto_monto_unitario" class="form-control" min="0" step="0.01">
            </div>
            <div class="col-md-2">
                <button type="button" id="btn_agregar" class="btn btn-primary w-100">
                    <i class="bi bi-plus-circle me-1"></i>Agregar
                </button>
            </div>
        </div>

        <table class="table table-bordered table-sm" id="tabla_detalle">
            <thead class="table-light">
                <tr>
                    <th>Concepto</th>
                    <th class="text-center">Cantidad</th>
                    <th class="text-end">Monto Unit.</th>
                    <th class="text-end">Total</th>
                    <th class="text-center no-export"></th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>

        <div class="row justify-content-end align-items-end">
            <div class="col-md-4">
                <div class="d-flex justify-content-between mb-2">
                    <strong>Total a Pagar:</strong>
                    <strong id="total_display">$0.00</strong>
                </div>
                <div class="mb-2">
                    <label class="form-label fw-bold">Monto Recibido</label>
                    <input type="number" id="monto_recibido" class="form-control text-end" step="0.01" min="0">
                </div>
                <div class="d-flex justify-content-between">
                    <strong>Cambio:</strong>
                    <strong id="cambio_display" class="text-success">$0.00</strong>
                </div>
            </div>
        </div>

        <div class="row justify-content-end mt-2">
            <div class="col-md-7">
                <label class="form-label fw-bold">
                    <i class="bi bi-chat-left-text me-1"></i>Observaciones
                </label>
                <textarea id="observaciones_cobro" class="form-control" rows="2" placeholder="Notas adicionales del cobro (opcional)..."></textarea>
            </div>
        </div>

        <div class="text-end mt-3">
            <button type="button" id="btn_cobrar" class="btn btn-success btn-lg" disabled>
                <i class="bi bi-check-circle me-1"></i> Cobrar
            </button>
        </div>

    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
const CSRF_HEADER_NAME = '<?= csrf_header() ?>';
let csrfHash = '<?= csrf_hash() ?>';

$(document).ready(function () {
    let renglones = [];

    $('#concepto_select').select2({
        theme: 'bootstrap-5',
        placeholder: 'Buscar concepto...',
        width: '100%',
        ajax: {
            url: '<?= site_url('servicios/cobro/buscar-concepto') ?>',
            dataType: 'json',
            delay: 300,
            data: params => ({ q: params.term }),
            processResults: data => ({ results: data.results })
        }
    });

    $('#concepto_select').on('select2:select', function (e) {
        const d = e.params.data;
        $('#concepto_select').data('nombre', d.text);
        $('#concepto_select').data('cuenta', d.nombre_cuenta);
        $('#concepto_select').data('partida', d.nombre_partida);
        $('#concepto_monto_unitario').val(parseFloat(d.monto).toFixed(2));
    });

    $('#btn_agregar').on('click', function () {
        const conceptoId = $('#concepto_select').val();
        if (! conceptoId) { alert('Seleccione un concepto.'); return; }

        const cantidad = parseFloat($('#concepto_cantidad').val()) || 1;
        const montoUnit = parseFloat($('#concepto_monto_unitario').val());

        if (isNaN(montoUnit) || montoUnit < 0) {
            alert('Capture un monto unitario válido.');
            return;
        }

        const total = Math.round(cantidad * montoUnit * 100) / 100;

        renglones.push({
            concepto_id: conceptoId,
            nombre: $('#concepto_select').data('nombre'),
            cuenta: $('#concepto_select').data('cuenta'),
            partida: $('#concepto_select').data('partida'),
            cantidad: cantidad,
            monto: montoUnit,
            total: total
        });

        renderTabla();
        $('#concepto_select').val(null).trigger('change');
        $('#concepto_cantidad').val(1);
        $('#concepto_monto_unitario').val('');
    });

    function renderTabla() {
        const tbody = $('#tabla_detalle tbody').empty();
        let total = 0;

        renglones.forEach((r, i) => {
            tbody.append(`<tr>
                <td>${r.nombre}</td>
                <td class="text-center">${r.cantidad}</td>
                <td class="text-end">$${r.monto.toFixed(2)}</td>
                <td class="text-end">$${r.total.toFixed(2)}</td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger btn-quitar" data-i="${i}">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>`);
            total += r.total;
        });

        $('#total_display').text('$' + total.toFixed(2));
        $('#monto_recibido').val(total.toFixed(2));
        actualizarCambio();
    }

    $(document).on('click', '.btn_quitar, .btn-quitar', function () {
        renglones.splice($(this).data('i'), 1);
        renderTabla();
    });

    $('#monto_recibido').on('input', actualizarCambio);

    function actualizarCambio() {
        const total = renglones.reduce((s, r) => s + r.total, 0);
        const recibido = parseFloat($('#monto_recibido').val()) || 0;
        const cambio = recibido - total;
        
        const $display = $('#cambio_display');
        const $btnCobrar = $('#btn_cobrar');
        
        if (cambio < 0) {
            $display.text('Faltan: $' + Math.abs(cambio).toFixed(2))
                    .removeClass('text-success').addClass('text-danger');
            $btnCobrar.prop('disabled', true);
        } else {
            $display.text('$' + cambio.toFixed(2))
                    .removeClass('text-danger').addClass('text-success');
            $btnCobrar.prop('disabled', renglones.length === 0);
        }
    }

    $('#btn_cobrar').on('click', function () {
        if (! $('#nombre_contribuyente').val()) {
            alert('Capture el nombre del contribuyente.');
            return;
        }

        const total = renglones.reduce((s, r) => s + r.total, 0);
        const recibido = parseFloat($('#monto_recibido').val()) || 0;
        
        if (recibido < total) {
            alert('El monto recibido no puede ser menor al total a pagar.');
            return;
        }

        const payload = {
            '<?= csrf_token() ?>': csrfHash,
            nombre_contribuyente:    $('#nombre_contribuyente').val(),
            rfc_contribuyente:       $('#rfc_contribuyente').val(),
            domicilio_contribuyente: $('#domicilio_contribuyente').val(),
            ext_contribuyente:       $('#ext_contribuyente').val(),
            bis_contribuyente:       $('#bis_contribuyente').val(),
            int_contribuyente:       $('#int_contribuyente').val(),
            colonia_contribuyente:   $('#colonia_contribuyente').val(),
            metodo_pago:             $('#metodo_pago').val(),
            monto_recibido:          recibido,
            observaciones_cobro:     $('#observaciones_cobro').val().trim() || null,
            renglones: renglones.map(r => ({
                concepto_id: r.concepto_id,
                cantidad: r.cantidad,
                monto_unitario: r.monto
            }))
        };

        $.ajax({
            url: '<?= site_url('servicios/cobro/guardar') ?>',
            method: 'POST',
            contentType: 'application/json',
            headers: { [CSRF_HEADER_NAME]: csrfHash },
            data: JSON.stringify(payload),
            success: function (resp) {
				if (resp.csrf_hash) { csrfHash = resp.csrf_hash; }
				
				if (resp.success) {
					const idCobro = resp.data.cobro.cobro_id; 
					alert('Cobro registrado. Folio: ' + resp.data.cobro.numero_folio);
					const urlImpresion = '<?= site_url('servicios/cobro/imprimir/') ?>' + idCobro + '?v=1';
					window.open(urlImpresion, '_blank', 'width=800,height=600');
					location.reload();
				} else {
					alert(resp.message);
				}
			},
            error: function (xhr) {
                if (xhr.responseJSON?.csrf_hash) { csrfHash = xhr.responseJSON.csrf_hash; }
                const msg = xhr.responseJSON?.message || 'Error al registrar el cobro.';
                alert(msg);
            }
        });
    });
});
</script>
<?= $this->endSection() ?>