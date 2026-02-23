<?php
$extraCSS = [
    'assets/css/dashboard.css',
    'https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css'
];
echo view('templates/header', ['title' => $title, 'extraCSS' => $extraCSS]);
?>

<div class="dashboard-wrapper">
    <?= view('templates/sidebar') ?>

    <div class="main-content">
        <div class="topbar">
            <div class="topbar-title">
                <button class="menu-toggle" id="menuToggle">☰</button>
                <h2><?= $title ?></h2>
            </div>
        </div>

        <div class="content-area">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success">
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="collectionsTable" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Venta</th>
                                    <th>Cliente</th>
                                    <th>Fecha</th>
                                    <th>Total</th>
                                    <th>Saldo Pendiente</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($sales)): ?>
                                    <?php foreach ($sales as $sale): ?>
                                        <tr>
                                            <td><strong><?= esc($sale['sale_number']) ?></strong></td>
                                            <td><?= esc($sale['customer_name']) ?></td>
                                            <td><?= date('d/m/Y', strtotime($sale['date'])) ?></td>
                                            <td>$<?= number_format($sale['total'], 2) ?></td>
                                            <td class="text-danger">
                                                <strong>$<?= number_format($sale['pending_balance'], 2) ?></strong>
                                            </td>
                                            <td>
                                                <a href="<?= base_url('collections/create/' . $sale['id']) ?>"
                                                    class="btn btn-sm btn-success">
                                                    💰 Registrar Pago
                                                </a>
                                                <a href="<?= base_url('sales/view/' . $sale['id']) ?>"
                                                    class="btn btn-sm btn-primary">
                                                    👁️ Ver
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
        $('#collectionsTable').DataTable({
            'order': [[2, 'desc']] // Order by Date descending
        });
    });
</script>
";
echo view('templates/footer', ['extraJS' => $extraJS, 'scripts' => $scripts]);
?>