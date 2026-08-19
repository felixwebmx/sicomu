<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Diario - <?= esc($caja['nombre'] ?? 'Caja') ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 12px;
            color: #000;
            background: #fff;
            padding: 20px 30px;
        }
        .reporte {
            max-width: 900px;
            margin: 0 auto;
        }

        /* ─── Header ─── */
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .logo-escudo {
            width: 70px;
            height: 70px;
            object-fit: contain;
        }
        .logo-der {
            width: 100px;
            height: auto;
            object-fit: contain;
        }
        .header-center {
            text-align: center;
            flex: 1;
            padding: 0 15px;
        }
        .header-center h1 {
            font-size: 15px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 2px;
            letter-spacing: 0.5px;
        }
        .header-center h2 {
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
        }

        /* ─── Subheader ─── */
        .subheader {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 15px;
            padding: 0 5px;
        }
        .fecha-box { font-size: 12px; }
        .fecha-box strong {
            border: 1px solid #000;
            padding: 2px 8px;
            font-weight: normal;
        }
        .titulo-reporte {
            border: 1px solid #000;
            padding: 3px 25px;
            font-size: 13px;
            font-weight: bold;
            text-align: center;
        }
        .caja-box {
            font-size: 12px;
            font-weight: bold;
        }

        /* ─── Tablas ─── */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        thead th {
            border-bottom: 1px solid #000;
            padding: 4px 6px;
            text-align: left;
            font-weight: normal;
            font-size: 11px;
        }
        tbody tr { border-bottom: 1px solid #000; }
        tbody td {
            padding: 3px 6px;
            vertical-align: top;
        }
        .fila-contribuyente td { padding-top: 8px; }
        .fila-contribuyente .col-nombre {
            font-weight: bold;
            text-transform: uppercase;
        }
        .fila-concepto td { padding-left: 6px; }
        .fila-concepto .col-codigo {
            font-size: 11px;
            white-space: nowrap;
        }
        .fila-concepto .col-concepto { font-size: 11px; }
        .text-end { text-align: right; }
        .fila-total td {
            border-bottom: 1px solid #000;
            padding-bottom: 6px;
        }
        .fila-total .label-total {
            text-align: right;
            padding-right: 20px;
        }

        /* ─── Total General ─── */
        .total-general {
            margin-top: 15px;
            display: flex;
            justify-content: flex-end;
        }
        .total-caja {
            border: 1px solid #000;
            padding: 5px 15px;
            font-size: 13px;
            font-weight: bold;
            min-width: 140px;
            text-align: right;
        }
        .total-label {
            font-weight: bold;
            margin-right: 10px;
            align-self: center;
        }

        /* ─── Salto de página ─── */
        .pagina-nueva {
            page-break-before: always;
            padding-top: 20px;
        }

        /* ═══════════════════════════════════════════════
           ESTILOS ESPECÍFICOS PARA APORTACIONES
           ═══════════════════════════════════════════════ */
        .aportacion-bloque {
            margin-bottom: 8px;
            border-bottom: 1px solid #000;
            padding-bottom: 6px;
        }
        .aportacion-linea1 {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 2px;
        }
        .aportacion-linea2 {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            margin-bottom: 2px;
            padding-left: 20px;
        }
        .aportacion-linea3 {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            font-size: 12px;
            gap: 15px;
            padding-left: 20px;
        }
        .aportacion-linea3 .folio-fecha {
            flex: 1;
            text-align: center;
        }
        .aportacion-linea3 .monto {
            min-width: 80px;
            text-align: right;
        }
        .aportacion-linea3 .total-label {
            font-weight: normal;
            margin-right: 5px;
        }
        .aportacion-linea3 .total-monto {
            min-width: 80px;
            text-align: right;
        }

        /* ─── Recuadros de resumen ─── */
        .resumen-aportaciones {
            margin-top: 25px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            align-items: center;
        }
        .resumen-caja {
            border: 1px solid #000;
            padding: 6px 15px;
            display: flex;
            align-items: center;
            gap: 0;
            font-size: 12px;
            width: 100%;
            max-width: 650px;
        }
        .resumen-caja > div {
            padding: 0 12px;
            border-right: 1px solid #000;
            text-align: center;
            flex: 1;
        }
        .resumen-caja > div:last-child {
            border-right: none;
            text-align: right;
            min-width: 100px;
        }
        .resumen-caja .label {
            font-weight: bold;
        }

        /* ─── Botón imprimir ─── */
        .btn-print {
            position: fixed;
            top: 15px;
            right: 15px;
            padding: 8px 20px;
            background: #0d6efd;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            z-index: 1000;
        }
        .btn-print:hover { background: #0b5ed7; }

        @media print {
            body { padding: 10px 20px; }
            .btn-print { display: none !important; }
            .reporte { max-width: 100%; }
            .pagina-nueva { padding-top: 0; }
        }
    </style>
</head>
<body>

<button onclick="window.print()" class="btn-print no-print">🖨️ Imprimir Reporte</button>

<!-- ═══════════════════════════════════════════════════════════════
     REPORTE 1: SERVICIOS (Reporte Diario)
     ═══════════════════════════════════════════════════════════════ -->
<div class="reporte">

    <div class="header">
        <div>
            <img src="<?= base_url('assets/adminlte/img/logos/estados_unidos_mexicanos.png') ?>" alt="Escudo" class="logo-escudo" onerror="this.style.display='none'">
        </div>
        <div class="header-center">
            <h1>Presidencia Municipal de Uriangato, Gto.</h1>
            <h2>Tesoreria Municipal</h2>
        </div>
        <div>
            <img src="<?= base_url('assets/adminlte/img/logos/logo_administracion.png') ?>" alt="Uriangato" class="logo-der" onerror="this.style.display='none'">
        </div>
    </div>

    <div class="subheader">
        <div class="fecha-box">Fecha: <strong><?= esc($fecha) ?></strong></div>
        <div class="titulo-reporte">Reporte Diario</div>
        <div class="caja-box">Caja <?= esc($caja['nombre'] ?? '1') ?></div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:12%">Folio</th>
                <th style="width:43%">Nombre</th>
                <th style="width:25%">Domicilio</th>
                <th style="width:20%">Colonia</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($servicios)): ?>
                <tr>
                    <td colspan="4" style="text-align:center; padding:20px;">No hay cobros de servicios registrados.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($servicios as $c): ?>
                    <tr class="fila-contribuyente">
                        <td><?= esc($c['folio']) ?></td>
                        <td class="col-nombre"><?= esc($c['nombre']) ?></td>
                        <td><?= esc($c['domicilio']) ?></td>
                        <td><?= esc($c['colonia']) ?></td>
                    </tr>
                    <?php foreach ($c['conceptos'] as $concepto): ?>
                    <tr class="fila-concepto">
                        <td class="col-codigo"><?= esc($concepto['codigo']) ?></td>
                        <td class="col-concepto"><?= esc($concepto['nombre']) ?></td>
                        <td></td>
                        <td class="text-end"><?= number_format($concepto['monto'], 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr class="fila-total">
                        <td></td>
                        <td class="label-total">Total</td>
                        <td></td>
                        <td class="text-end"><?= number_format($c['total'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if (!empty($servicios)): ?>
    <div class="total-general">
        <span class="total-label">Total</span>
        <div class="total-caja"><?= number_format($totalServicios, 2) ?></div>
    </div>
    <?php endif; ?>

</div>

<!-- ═══════════════════════════════════════════════════════════════
     REPORTE 2: APORTACIONES (solo si hay)
     ═══════════════════════════════════════════════════════════════ -->
<?php if ($hayAportaciones): ?>

<div class="reporte pagina-nueva">

    <div class="header">
        <div>
            <img src="<?= base_url('assets/adminlte/img/logos/estados_unidos_mexicanos.png') ?>" alt="Escudo" class="logo-escudo" onerror="this.style.display='none'">
        </div>
        <div class="header-center">
            <h1>Presidencia Municipal de Uriangato, Gto.</h1>
            <h2>Tesoreria Municipal</h2>
        </div>
        <div>
            <img src="<?= base_url('assets/adminlte/img/logos/logo_administracion.png') ?>" alt="Uriangato" class="logo-der" onerror="this.style.display='none'">
        </div>
    </div>

    <div class="subheader">
        <div class="fecha-box">Fecha: <strong><?= esc($fecha) ?></strong></div>
        <div class="titulo-reporte">Reporte de Aportaciones de Vecinos</div>
        <div class="caja-box">Caja <?= esc($caja['nombre'] ?? '1') ?></div>
    </div>

    <!-- Listado de aportaciones -->
    <?php foreach ($aportaciones as $c): 
        $fechaCobro = date('d/m/Y', strtotime($c['conceptos'][0]['fecha_cobro'] ?? $apertura['fecha_apertura']));
        $numExt = !empty($c['no_exterior']) ? 'No. ' . $c['no_exterior'] : '';
        $numBis = !empty($c['no_bis']) ? ' BIS ' . $c['no_bis'] : '';
        $numInt = !empty($c['no_interior']) ? ' Int. ' . $c['no_interior'] : '';
        $domicilioCompleto = trim(($c['nombre_vialidad'] ?? '') . ' ' . $numExt . $numBis . $numInt);
        
        // Línea 1: Obra / Vialidad / Colonia
        $lineaObra = '';
        if (!empty($c['id_obra'])) {
            $lineaObra .= $c['id_obra'] . ' ';
        }
        $lineaObra .= ($c['nombre_obra'] ?? '') . ' COL. ' . ($c['colonia'] ?? '');
    ?>

    <div class="aportacion-bloque">
        <!-- Línea 1: Obra + Programa -->
        <div class="aportacion-linea1">
            <span><?= esc($lineaObra) ?></span>
            <span><?= esc(($c['nombre_programa'] ?? '') . ' - ' . ($c['anio_programa'] ?? '')) ?></span>
        </div>

        <!-- Línea 2: Vecino + Domicilio -->
        <div class="aportacion-linea2">
            <span><?= esc($c['nombre']) ?></span>
            <span><?= esc($domicilioCompleto) ?></span>
        </div>

        <!-- Línea 3: Folio, Fecha, Monto, Total -->
        <div class="aportacion-linea3">
            <span class="folio-fecha">
                Folio: <?= esc($c['folio']) ?> &nbsp;&nbsp; <?= esc($fechaCobro) ?>
            </span>
            <span class="monto"><?= number_format($c['total'], 2) ?></span>
            <span class="total-label">Total</span>
            <span class="total-monto"><?= number_format($c['total'], 2) ?></span>
        </div>
    </div>

    <?php endforeach; ?>

    <div class="resumen-aportaciones">
        <div class="resumen-caja">
            <div>
                <span class="label">Movimientos:</span> <?= count($aportaciones) ?>
            </div>
            <div>Caja <?= esc($caja['nombre'] ?? '1') ?></div>
            <div>
                <span class="label">Total Día</span> <?= esc($fecha) ?>
            </div>
            <div><?= number_format($totalAportaciones, 2) ?></div>
        </div>
    </div>

</div>

<?php endif; ?>

</body>
</html>