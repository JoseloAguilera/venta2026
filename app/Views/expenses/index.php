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
                <a href="<?= base_url('expenses/report') ?>" class="btn btn-secondary">
                    📊 Reporte
                </a>
                <?php if (can_insert('expenses')): ?>
                    <a href="<?= base_url('expenses/create') ?>" class="btn btn-primary">
                        ➕ Nuevo Gasto
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="content-area">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success">
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <div class="card" style="margin-bottom: 1.5rem;">
                <div class="card-body">
                    <h4>Total de Gastos: <span class="text-danger">$<?= number_format($total, 2) ?></span></h4>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="expensesTable" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Categoría</th>
                                    <th>Descripción</th>
                                    <th>Monto</th>
                                    <th>Usuario</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($expenses)): ?>
                                    <?php foreach ($expenses as $expense): ?>
                                        <tr>
                                            <td><?= date('d/m/Y', strtotime($expense['date'])) ?></td>
                                            <td>
                                                <span class="badge badge-primary"><?= esc($expense['category_name']) ?></span>
                                            </td>
                                            <td><?= esc($expense['description']) ?></td>
                                            <td class="text-danger">
                                                <strong>$<?= number_format($expense['amount'], 2) ?></strong>
                                            </td>
                                            <td><?= esc($expense['username']) ?></td>
                                            <td>
                                                <?php if (can_update('expenses')): ?>
                                                    <a href="<?= base_url('expenses/edit/' . $expense['id']) ?>"
                                                        class="btn btn-sm btn-primary">
                                                        ✏️ Editar
                                                    </a>
                                                <?php endif; ?>

                                                <?php if (can_delete('expenses')): ?>
                                                    <a href="<?= base_url('expenses/delete/' . $expense['id']) ?>"
                                                        class="btn btn-sm btn-danger"
                                                        onclick="return confirm('¿Eliminar este gasto?')">
                                                        🗑️
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
        $('#expensesTable').DataTable({
            'order': [[0, 'desc']] // Sort by Date descending
        });
    });
</script>
";
echo view('templates/footer', ['extraJS' => $extraJS, 'scripts' => $scripts]);
?>