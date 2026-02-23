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
                    <?php if (can_insert('purchases')): ?>
                        <a href="<?= base_url('purchases/create') ?>" class="btn btn-primary">
                            ➕ Nueva Compra
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
                        <table id="purchasesTable" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Número</th>
                                    <th>Fecha</th>
                                    <th>Proveedor</th>
                                    <th>Tipo</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($purchases)): ?>
                                    <?php foreach ($purchases as $purchase): ?>
                                        <tr>
                                            <td><strong><?= esc($purchase['purchase_number']) ?></strong></td>
                                            <td><?= date('d/m/Y', strtotime($purchase['date'])) ?></td>
                                            <td><?= esc($purchase['supplier_name']) ?></td>
                                            <td>
                                                <span
                                                    class="badge <?= $purchase['payment_type'] === 'cash' ? 'badge-success' : 'badge-warning' ?>">
                                                    <?= $purchase['payment_type'] === 'cash' ? 'Contado' : 'Crédito' ?>
                                                </span>
                                            </td>
                                            <td>$<?= number_format($purchase['total'], 2) ?></td>
                                            <td>
                                                <?php
                                                $badges = [
                                                    'paid' => 'badge-success',
                                                    'partial' => 'badge-warning',
                                                    'pending' => 'badge-danger',
                                                    'cancelled' => 'badge-secondary'
                                                ];
                                                $labels = [
                                                    'paid' => 'Pagado',
                                                    'partial' => 'Parcial',
                                                    'pending' => 'Pendiente',
                                                    'cancelled' => 'Anulada'
                                                ];
                                                ?>
                                                <span class="badge <?= $badges[$purchase['status']] ?? 'badge-secondary' ?>">
                                                    <?= $labels[$purchase['status']] ?? $purchase['status'] ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="<?= base_url('purchases/view/' . $purchase['id']) ?>"
                                                    class="btn btn-sm btn-primary">
                                                    👁️ Ver
                                                </a>
                                                <?php if (can_delete('purchases') && $purchase['status'] !== 'cancelled'): ?>
                                                    <a href="<?= base_url('purchases/annul/' . $purchase['id']) ?>"
                                                        class="btn btn-sm btn-danger"
                                                        onclick="return confirm('¿Anular esta compra? Esta acción revertirá el stock.')">
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
    $(document).ready(function () {
        $('#purchasesTable').DataTable({
            'order': [[0, 'desc']] // Order by Purchase Number descending
        });
    });
</script>
";
echo view('templates/footer', ['extraJS' => $extraJS, 'scripts' => $scripts]);
?>