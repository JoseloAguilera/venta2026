<?php
$extraCSS = ['assets/css/dashboard.css', 'https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css'];
echo view('templates/header', ['title' => $title, 'extraCSS' => $extraCSS]);
?>

<div class="dashboard-wrapper">
    <?= view('templates/sidebar') ?>

    <div class="main-content">
        <div class="topbar">
            <div class="topbar-title">
                <button class="menu-toggle" id="menuToggle">☰</button>
                <h2>
                    <?= $title ?>
                </h2>
            </div>
            <div class="topbar-actions">
                <a href="<?= base_url('inventory-transfers/create') ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nueva Transferencia
                </a>
            </div>
        </div>

        <div class="content-area">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success">
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger">
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="transfersTable" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Fecha</th>
                                    <th>Origen</th>
                                    <th>Destino</th>
                                    <th>Usuario</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($transfers)): ?>
                                    <?php foreach ($transfers as $transfer): ?>
                                        <tr>
                                            <td>
                                                <?= esc($transfer['transfer_code']) ?>
                                            </td>
                                            <td>
                                                <?= date('d/m/Y H:i', strtotime($transfer['created_at'])) ?>
                                            </td>
                                            <td>
                                                <?= esc($transfer['source_warehouse']) ?>
                                            </td>
                                            <td>
                                                <?= esc($transfer['destination_warehouse']) ?>
                                            </td>
                                            <td>
                                                <?= esc($transfer['username']) ?>
                                            </td>
                                            <td>
                                                <?php if ($transfer['status'] == 'completed'): ?>
                                                    <span class="badge bg-success">Completado</span>
                                                <?php elseif ($transfer['status'] == 'pending'): ?>
                                                    <span class="badge bg-warning">Pendiente</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Cancelado</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="<?= base_url('inventory-transfers/view/' . $transfer['id']) ?>"
                                                    class="btn btn-sm btn-info" title="Ver Detalles">
                                                    👁️
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$extraJS = [
    'https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js',
    'https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js'
];
$scripts = "
<script>
    $(document).ready(function () {
        $('#transfersTable').DataTable({
            'order': [[1, 'desc']]
        });
    });
</script>
";
echo view('templates/footer', ['extraJS' => $extraJS, 'scripts' => $scripts]);
?>