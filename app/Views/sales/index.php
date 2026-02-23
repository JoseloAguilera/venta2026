<?php
$extraCSS = [
    'assets/css/dashboard.css',
    'https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css'
];
echo view('templates/header', ['title' => $title, 'extraCSS' => $extraCSS]);
helper('permission');
?>

<div class="dashboard-wrapper">
    <?= view('templates/sidebar') ?>

    <div class="main-content">
        <div class="topbar">
            <div class="topbar-title">
                <button class="menu-toggle" id="menuToggle">☰</button>
                <h2><?= $title ?></h2>
            </div>
            <div class="topbar-actions">
                <div class="topbar-actions">
                    <?php if (can_insert('sales')): ?>
                        <a href="<?= base_url('sales/create') ?>" class="btn btn-primary">
                            ➕ Nueva Venta
                        </a>
                    <?php endif; ?>
                </div>
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
                        <table id="salesTable" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Número</th>
                                    <th>Fecha</th>
                                    <th>Cliente</th>
                                    <th>Tipo</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($sales)): ?>
                                    <?php foreach ($sales as $sale): ?>
                                        <tr class="<?= $sale['status'] === 'cancelled' ? 'table-danger' : '' ?>">
                                            <td><strong><?= esc($sale['sale_number']) ?></strong></td>
                                            <td><?= date('d/m/Y', strtotime($sale['date'])) ?></td>
                                            <td><?= esc($sale['customer_name']) ?></td>
                                            <td>
                                                <span
                                                    class="badge <?= $sale['payment_type'] === 'cash' ? 'badge-success' : 'badge-warning' ?>">
                                                    <?= $sale['payment_type'] === 'cash' ? 'Contado' : 'Crédito' ?>
                                                </span>
                                            </td>
                                            <td>$<?= number_format($sale['total'], 2) ?></td>
                                            <td>
                                                <?php
                                                $badges = [
                                                    'paid' => 'badge-success',
                                                    'partial' => 'badge-warning',
                                                    'pending' => 'badge-secondary', // Changed pending to gray to reserve red for alert/cancelled
                                                    'cancelled' => 'badge-danger'   // User requested red for cancelled
                                                ];
                                                $labels = [
                                                    'paid' => 'Pagado',
                                                    'partial' => 'Parcial',
                                                    'pending' => 'Pendiente',
                                                    'cancelled' => 'Anulada'
                                                ];
                                                ?>
                                                <span class="badge <?= $badges[$sale['status']] ?? 'badge-secondary' ?>">
                                                    <?= $labels[$sale['status']] ?? $sale['status'] ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="<?= base_url('sales/view/' . $sale['id']) ?>"
                                                    class="btn btn-sm btn-primary">
                                                    👁️ Ver
                                                </a>
                                                <a href="javascript:void(0)"
                                                    onclick="openTicket('<?= base_url('sales/ticket/' . $sale['id']) ?>')"
                                                    class="btn btn-sm btn-info" title="Imprimir Ticket">
                                                    🖨️
                                                </a>
                                                <?php if (can_delete('sales') && $sale['status'] !== 'cancelled'): ?>
                                                    <a href="<?= base_url('sales/annul/' . $sale['id']) ?>"
                                                        class="btn btn-sm btn-danger"
                                                        onclick="return confirm('¿Anular esta venta? Esta acción revertirá el stock.')">
                                                        🚫 Anular
                                                    </a>
                                                <?php endif; ?>
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
    function openTicket(url) {
        window.open(url, 'Ticket', 'width=400,height=600,scrollbars=yes');
    }

    $(document).ready(function () {
        $('#salesTable').DataTable({
            'order': [[0, 'desc']] // Order by Sale Number descending
        });
    });
</script>
";
echo view('templates/footer', ['extraJS' => $extraJS, 'scripts' => $scripts]);
?>