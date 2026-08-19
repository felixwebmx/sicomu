<?php

namespace App\Controllers\Reportes;

use App\Controllers\BaseController;
use App\Services\ReporteService;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class ReporteCobrosController extends BaseController
{
    protected ReporteService $reporteService;

    public function __construct()
    {
        $this->reporteService = new ReporteService();
    }

    public function index()
    {
        return view('reportes/cobros', [
            'titulo' => 'Reportes de Cobros',
            'obras'  => $this->reporteService->listarObras(),
        ]);
    }

    // ═════════════════════════════════════════════════════════════
    // EXCEL: SERVICIOS
    // ═════════════════════════════════════════════════════════════
    public function excelServicios()
    {
        $fechaInicio = $this->request->getPost('fecha_inicio');
        $fechaFin    = $this->request->getPost('fecha_fin');

        if (empty($fechaInicio) || empty($fechaFin)) {
            return redirect()->back()->with('error', 'Debe seleccionar fecha inicio y fecha fin.');
        }

        $datos = $this->reporteService->reporteServiciosPorFecha($fechaInicio, $fechaFin);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Servicios');

        $headers = ['cuenta', 'partida', 'claconcep', 'nombre_concepto', 'monto', 'nombre_contribuyente', 'folio', 'fecha', 'estatus_cobro'];
        $sheet->fromArray($headers, null, 'A1');
        $sheet->getStyle('A1:I1')->getFont()->setBold(true);

        $row = 2;
        foreach ($datos as $d) {
            $sheet->setCellValue('A' . $row, $d['cuenta']);
            $sheet->setCellValue('B' . $row, $d['partida']);
            $sheet->setCellValue('C' . $row, $d['claconcep']);
            $sheet->setCellValue('D' . $row, $d['nombre_concepto']);
            $sheet->setCellValue('E' . $row, (float) $d['monto']);
            $sheet->setCellValue('F' . $row, $d['nombre_contribuyente']);
            $sheet->setCellValue('G' . $row, $d['folio']);
            $sheet->setCellValue('H' . $row, $d['fecha']);
            $sheet->setCellValue('I' . $row, $d['estatus_cobro']);
            $row++;
        }

        $sheet->getStyle('E2:E' . ($row - 1))->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'Reporte_Servicios_' . $fechaInicio . '_a_' . $fechaFin . '.xlsx';
        $this->descargarExcel($spreadsheet, $filename);
    }

    // ═════════════════════════════════════════════════════════════
    // EXCEL: APORTACIONES
    // ═════════════════════════════════════════════════════════════
    public function excelAportaciones()
    {
        $fechaInicio = $this->request->getPost('fecha_inicio');
        $fechaFin    = $this->request->getPost('fecha_fin');

        if (empty($fechaInicio) || empty($fechaFin)) {
            return redirect()->back()->with('error', 'Debe seleccionar fecha inicio y fecha fin.');
        }

        $datos = $this->reporteService->reporteAportacionesPorFecha($fechaInicio, $fechaFin);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Aportaciones');

        $headers = ['nomvec', 'foliopaq', 'obra', 'monto', 'fechault', 'status1'];
        $sheet->fromArray($headers, null, 'A1');
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);

        $row = 2;
        foreach ($datos as $d) {
            $sheet->setCellValue('A' . $row, $d['nomvec']);
            $sheet->setCellValue('B' . $row, $d['foliopaq']);
            $sheet->setCellValue('C' . $row, $d['obra']);
            $sheet->setCellValue('D' . $row, (float) $d['monto']);
            $sheet->setCellValue('E' . $row, $d['fechault']);
            $sheet->setCellValue('F' . $row, $d['status1']);
            $row++;
        }

        $sheet->getStyle('D2:D' . ($row - 1))->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);
        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'Reporte_Aportaciones_' . $fechaInicio . '_a_' . $fechaFin . '.xlsx';
        $this->descargarExcel($spreadsheet, $filename);
    }

    // ═════════════════════════════════════════════════════════════
    // EXCEL: VECINOS POR OBRA
    // ═════════════════════════════════════════════════════════════
    public function excelVecinosPorObra()
    {
        $obraId = (int) $this->request->getPost('obra_id');

        if ($obraId <= 0) {
            return redirect()->back()->with('error', 'Debe seleccionar una obra.');
        }

        $datos = $this->reporteService->reporteVecinosPorObra($obraId);
        $obraNombre = !empty($datos) ? $datos[0]['obra'] : 'Obra_' . $obraId;

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Vecinos');

        $headers = ['nombre_vecino', 'vialidad', 'no_exterior', 'no_bis', 'no_interior', 'colonia', 'ml', 'costo_ml', 'total_aportacion', 'pagado', 'resto', 'acera', 'obra', 'programa'];
        $sheet->fromArray($headers, null, 'A1');
        $sheet->getStyle('A1:N1')->getFont()->setBold(true);

        $row = 2;
        foreach ($datos as $d) {
            $sheet->setCellValue('A' . $row, $d['nomvec']);
            $sheet->setCellValue('B' . $row, $d['vialidad']);
            $sheet->setCellValue('C' . $row, $d['no_exterior']);
            $sheet->setCellValue('D' . $row, $d['no_bis']);
            $sheet->setCellValue('E' . $row, $d['no_interior']);
            $sheet->setCellValue('F' . $row, $d['colonia']);
            $sheet->setCellValue('G' . $row, (float) $d['ml']);
            $sheet->setCellValue('H' . $row, (float) $d['costo_ml']);
            $sheet->setCellValue('I' . $row, (float) $d['total_aportacion']);
            $sheet->setCellValue('J' . $row, (float) $d['pagado']);
            $sheet->setCellValue('K' . $row, (float) $d['resto']);
            $sheet->setCellValue('L' . $row, $d['acera']);
            $sheet->setCellValue('M' . $row, $d['obra']);
            $sheet->setCellValue('N' . $row, $d['programa']);
            $row++;
        }

        // Formato moneda
        foreach (['H', 'I', 'J', 'K'] as $col) {
            $sheet->getStyle($col . '2:' . $col . ($row - 1))->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);
        }

        foreach (range('A', 'N') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'Vecinos_' . preg_replace('/[^a-zA-Z0-9]/', '_', $obraNombre) . '.xlsx';
        $this->descargarExcel($spreadsheet, $filename);
    }

    // ═════════════════════════════════════════════════════════════
    // EXCEL: RESUMEN DIARIO POR CAJA (ARQUEOS)
    // ═════════════════════════════════════════════════════════════
    public function excelArqueos()
    {
        $fechaInicio = $this->request->getPost('fecha_inicio_arqueo');
        $fechaFin    = $this->request->getPost('fecha_fin_arqueo');

        if (empty($fechaInicio) || empty($fechaFin)) {
            return redirect()->back()->with('error', 'Debe seleccionar fecha inicio y fecha fin para el arqueo.');
        }

        $datos = $this->reporteService->reporteArqueosPorFecha($fechaInicio, $fechaFin);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Arqueos');

        $headers = ['fecha', 'caja', 'cajero_apertura', 'cajero_cierre', 'folio_inicial', 'folio_final', 'total_servicios', 'total_aportaciones', 'total_sistema', 'efectivo_contado', 'diferencia', 'observaciones'];
        $sheet->fromArray($headers, null, 'A1');
        $sheet->getStyle('A1:L1')->getFont()->setBold(true);

        $row = 2;
        foreach ($datos as $d) {
            $sheet->setCellValue('A' . $row, $d['fecha']);
            $sheet->setCellValue('B' . $row, $d['caja_nombre']);
            $sheet->setCellValue('C' . $row, $d['cajero_apertura']);
            $sheet->setCellValue('D' . $row, $d['cajero_cierre']);
            $sheet->setCellValue('E' . $row, $d['folio_inicial']);
            $sheet->setCellValue('F' . $row, $d['folio_final']);
            $sheet->setCellValue('G' . $row, (float) $d['total_servicios']);
            $sheet->setCellValue('H' . $row, (float) $d['total_aportaciones']);
            $sheet->setCellValue('I' . $row, (float) $d['total_sistema']);
            $sheet->setCellValue('J' . $row, (float) $d['efectivo_contado']);
            $sheet->setCellValue('K' . $row, (float) $d['diferencia']);
            $sheet->setCellValue('L' . $row, $d['observaciones']);
            $row++;
        }

        // Formato moneda
        foreach (['G', 'H', 'I', 'J', 'K'] as $col) {
            $sheet->getStyle($col . '2:' . $col . ($row - 1))->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2);
        }

        foreach (range('A', 'L') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'Resumen_Arqueos_' . $fechaInicio . '_a_' . $fechaFin . '.xlsx';
        $this->descargarExcel($spreadsheet, $filename);
    }

    /**
     * Helper para descargar el Excel.
     */
    private function descargarExcel(Spreadsheet $spreadsheet, string $filename): void
    {
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}