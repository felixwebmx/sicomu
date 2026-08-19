<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recibo Folio <?= esc($cobro['numero_folio'] ?? '') ?></title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: Arial, sans-serif; }
        body { width: 216mm; height: 279mm; margin: 0 auto; background: #fff; color: #000; font-size: 11px; }

        .mitad-recibo { height: 50%; padding: 10mm 15mm; position: relative; border-bottom: 1px dashed #ccc; }
        .mitad-recibo:last-child { border-bottom: none; }

        .bg-verde-oscuro { background-color: #277e4c !important; color: white !important; font-weight: bold; }
        .borde-verde { border: 1px solid #277e4c; }
        .texto-verde { color: #277e4c; }
        .texto-rojo { color: #d32f2f; }

        .header { display: flex; justify-content: space-between; margin-bottom: 2px; }
        .header-central { text-align: center; flex-grow: 1; padding: 0 10px; }
        .header-central h1 { font-size: 13px; margin-bottom: 2px; }
        .header-central h2 { font-size: 11px; font-weight: normal; margin-bottom: 2px; }
        .header-central p { font-size: 8px; line-height: 1.1; }
        
        .cajas-superiores { display: flex; flex-direction: column; width: 250px; }
        .fecha-caja { display: flex; width: 100%; border: 1px solid #277e4c; }
        .fecha-col { width: 33.33%; text-align: center; }
        .fecha-tit { font-size: 8px; padding: 1px; border-bottom: 1px solid #277e4c; }
        .fecha-val { font-size: 10px; padding: 2px; }
        .fecha-col:not(:last-child) { border-right: 1px solid #277e4c; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
        td, th { border: 1px solid #277e4c; padding: 3px 5px; }
        
        .td-label { background-color: #277e4c; color: white; font-size: 9px; font-weight: bold; text-align: center; }
        .td-dato { text-transform: uppercase; font-size: 10px; }

        .tabla-conceptos { min-height: 110px; }
        .tabla-conceptos th { font-size: 9px; text-align: center; }
        .col-codigo { width: 12%; text-align: center; }
        .col-concepto { width: 68%; }
        .col-importe { width: 20%; text-align: right; }
        .fila-vacia td { border-top: none; border-bottom: none; height: 80px; }

        .footer-recibo { font-size: 8px; display: flex; justify-content: space-between; margin-top: 6px; align-items: flex-end; }
        .footer-legal { width: 80%; }
        .leyenda-factura { background-color: #277e4c; color: white; padding: 3px; display: inline-block; margin-top: 3px; font-weight: bold; font-size: 7.5px;}
        .footer-tipo { font-weight: bold; font-size: 11px; }

        @media print {
            @page { size: letter; margin: 0; }
            body { margin: 0; }
        }
    </style>
</head>
<body onload="window.print()">

<?php foreach (['ORIGINAL', 'COPIA'] as $tipoRecibo): ?>
    
    <div class="mitad-recibo">
        
        <!-- HEADER -->
        <div class="header">
            <div style="width: 80px;"></div>
            
            <div class="header-central texto-verde">
                <h1>MUNICIPIO DE URIANGATO, GTO.</h1>
                <h2>TESORERIA MUNICIPAL</h2>
                <p>Morelos No. 1 Centro Uriangato, Gto. C.P. 38980</p>
                <p>R.F.C. MUR-920101-G15 Tels. 445 457 50 22 Ext. 109</p>
                <p>445 458 98 00 al 29 Email: ingresosuriangato@gmail.com</p>
            </div>
            
            <!-- CAJAS SUPERIORES (FOLIOS Y FECHA) -->
            <div style="display: flex; gap: 4px; align-items: stretch;">
                <!-- 1. FOLIO (IMPRENTA) -->
                <div style="width: 110px; display: flex; flex-direction: column;">
                    <div class="bg-verde-oscuro" style="text-align: center; font-size: 8px; padding: 2px;">FOLIO</div>
                    <div class="texto-rojo borde-verde" style="font-size: 13px; font-weight: bold; text-align: center; padding: 4px 2px; background: #fff; flex-grow: 1; display: flex; align-items: center; justify-content: center;">
                        Nº 000000
                    </div>
                </div>
                <!-- 2. NO. DE RECIBO (SISTEMA) -->
                <div style="width: 90px; display: flex; flex-direction: column;">
                    <div class="bg-verde-oscuro" style="text-align: center; font-size: 8px; padding: 2px;">NO. DE RECIBO</div>
                    <div class="borde-verde" style="font-size: 13px; font-weight: bold; text-align: center; padding: 4px 2px; background: #fff; color: #000; flex-grow: 1; display: flex; align-items: center; justify-content: center;">
                        <?= str_pad(esc($cobro['numero_folio'] ?? 0), 6, '0', STR_PAD_LEFT) ?>
                    </div>
                </div>

                <!-- 3. FECHA (DÍA, MES, AÑO - IGUALADA EN TAMAÑO) -->
                <div style="display: flex; width: 130px; border: 1px solid #277e4c; background: #fff;">
                    <div style="width: 33.33%; display: flex; flex-direction: column; text-align: center;">
                        <div style="font-size: 8px; padding: 2px;" class="bg-verde-oscuro">DÍA</div>
                        <div style="font-size: 12px; font-weight: bold; padding: 4px 0; flex-grow: 1; display: flex; align-items: center; justify-content: center;"><?= date('d', strtotime($cobro['fecha_cobro'] ?? 'now')) ?></div>
                    </div>
                    <div style="width: 33.33%; display: flex; flex-direction: column; text-align: center; border-left: 1px solid #277e4c; border-right: 1px solid #277e4c;">
                        <div style="font-size: 8px; padding: 2px;" class="bg-verde-oscuro">MES</div>
                        <div style="font-size: 12px; font-weight: bold; padding: 4px 0; flex-grow: 1; display: flex; align-items: center; justify-content: center;"><?= date('m', strtotime($cobro['fecha_cobro'] ?? 'now')) ?></div>
                    </div>
                    <div style="width: 33.33%; display: flex; flex-direction: column; text-align: center;">
                        <div style="font-size: 8px; padding: 2px;" class="bg-verde-oscuro">AÑO</div>
                        <div style="font-size: 12px; font-weight: bold; padding: 4px 0; flex-grow: 1; display: flex; align-items: center; justify-content: center;"><?= date('Y', strtotime($cobro['fecha_cobro'] ?? 'now')) ?></div>
                    </div>
                </div>

            </div>
        </div>

        <!-- DATOS DEL CONTRIBUYENTE (Mapeados con los campos de tu BD) -->
        <table>
            <tr>
                <td class="td-label" style="width: 60px;">CUENTA</td>
                <td class="td-dato" style="width: 15%;">-</td>
                <td class="td-label" style="width: 100px;">NOMBRE DEL<br>CONTRIBUYENTE</td>
                <td class="td-dato"><?= esc($cobro['nombre_contribuyente'] ?? '') ?></td>
                <td class="td-label" style="width: 45px;">R.F.C.</td>
                <td class="td-dato" style="width: 18%;"><?= esc($cobro['rfc_contribuyente'] ?? '') ?></td>
            </tr>
        </table>

        <!-- DOMICILIO -->
        <table>
            <tr>
                <td class="bg-verde-oscuro" style="writing-mode: vertical-lr; transform: rotate(180deg); text-align: center; width: 25px; padding: 8px 0; font-size: 7.5px;">UBICACION / DOMICILIO</td>
                <td style="padding: 0; border: none;">
                    <table style="margin: 0; border: none; height: 100%;">
                        <tr>
                            <td class="texto-verde" style="width: 75px; border:none; border-bottom: 1px solid #277e4c; border-right: 1px solid #277e4c; font-size: 8px;">CALLE<br>COLONIA<br>POBLACION</td>
                            <td style="border:none; border-bottom: 1px solid #277e4c;" class="td-dato">
                                <?php 
                                    $dom = trim($cobro['domicilio_contribuyente'] ?? '');
                                    if (!empty($cobro['ext_contribuyente'])) $dom .= ' Ext. ' . $cobro['ext_contribuyente'];
                                    if (!empty($cobro['int_contribuyente'])) $dom .= ' Int. ' . $cobro['int_contribuyente'];
                                    if (!empty($cobro['bis_contribuyente'])) $dom .= ' ' . $cobro['bis_contribuyente'];
                                    if (!empty($cobro['colonia_contribuyente'])) $dom .= ', Col. ' . $cobro['colonia_contribuyente'];
                                    echo esc($dom);
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="texto-verde" style="border:none; border-right: 1px solid #277e4c; font-size: 8px;">CALLE<br>COLONIA<br>POBLACION</td>
                            <td style="border:none;" class="td-dato"></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- CONCEPTOS -->
        <table class="tabla-conceptos">
            <tr class="bg-verde-oscuro">
                <th class="col-codigo">CODIGO</th>
                <th class="col-concepto">CONCEPTO</th>
                <th class="col-importe">IMPORTE</th>
            </tr>
            <?php 
            $total = 0;
            if(!empty($detalles)):
                foreach($detalles as $det): 
                    $total += $det['importe'];
            ?>
            <tr>
                <td class="col-codigo td-dato"><?= esc($det['clave_concepto'] ?? '') ?></td>
                <td class="col-concepto td-dato"><?= esc($det['descripcion'] ?? '') ?></td>
                <td class="col-importe td-dato">$ <?= number_format($det['importe'], 2) ?></td>
            </tr>
            <?php 
                endforeach;
            endif; 
            ?>
            <tr class="fila-vacia">
                <td></td><td></td><td></td>
            </tr>
            <tr>
                <td colspan="2" style="border: none; padding: 0;">
                    <table style="margin:0; width: 100%;">
                        <tr>
                            <td class="td-label" style="width: 85px;">OBSERVACION</td>
                            <td style="border-right: none;" class="td-dato"><?= esc($cobro['observaciones_cobro'] ?? '') ?></td>
                        </tr>
                    </table>
                </td>
                <td class="col-importe" style="border-top: 1px solid #277e4c;">
                    <div style="display: flex; justify-content: space-between;">
                        <span class="td-label" style="background: none; color: #277e4c; font-size: 10px;">TOTAL</span>
                        <strong>$ <?= number_format($total, 2) ?></strong>
                    </div>
                </td>
            </tr>
        </table>

        <!-- FOOTER -->
        <div class="footer-recibo">
            <div class="footer-legal">
                <p>EL PRESENTE RECIBO SOLO SERA VALIDO CON LA PROTECCION DE LA MAQUINA REGISTRADORA O SELLO FECHADOR DE LA OFICINA DONDE SE HAGA EL PAGO.</p>
                <div class="leyenda-factura">"SI REQUIERE FACTURA (CFDI), FAVOR DE SOLICITARLA EL MISMO DIA EN QUE REALICE EL PAGO"</div>
            </div>
            <div class="footer-tipo">
                <?= $tipoRecibo ?>
            </div>
        </div>

    </div>

<?php endforeach; ?>

</body>
</html>