<?= $this->extend('layouts/main') ?>

<?= $this->section('contenido') ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title mb-0"><i class="bi bi-journal-check me-2"></i>Historial de Arqueos</h3>
    </div>
    <div class="card-body">
        <table id="tablaArqueos" class="table table-striped table-hover w-100">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Fecha Cierre</th>
                    <th>Caja</th>
                    <th>Cajero</th>
                    <th class="text-end">Total Sistema</th>
                    <th class="text-end">Efectivo</th>
                    <th class="text-end">Diferencia</th>
                    <th class="text-center no-export">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($arqueos as $a): ?>
                    <tr>
                        <td><?= esc($a['id']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($a['fecha_arqueo'])) ?></td>
                        <td><?= esc($a['caja_nombre']) ?></td>
                        <td><?= esc($a['cajero']) ?></td>
                        <td class="text-end">$<?= number_format((float)$a['total_sistema'], 2) ?></td>
                        <td class="text-end">$<?= number_format((float)$a['efectivo_contado'], 2) ?></td>
                        <td class="text-end fw-bold <?= $a['diferencia'] == 0 ? 'text-success' : ($a['diferencia'] > 0 ? 'text-warning' : 'text-danger') ?>">
                            <?= $a['diferencia'] >= 0 ? '+' : '' ?>$<?= number_format((float)$a['diferencia'], 2) ?>
                        </td>
                        <td class="text-center no-export">
                            <a href="<?= site_url('caja/arqueo/' . $a['caja_apertura_id']) ?>" class="btn btn-sm btn-outline-primary" title="Ver Arqueo">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    initDataTable('#tablaArqueos', {
        columnDefs: [
            { orderable: false, targets: 7 },
            { searchable: false, targets: 7 }
        ],
        order: [[0, 'desc']]
    });
});
</script>
<?= $this->endSection() ?>