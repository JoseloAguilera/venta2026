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
                    <?php if (can_insert('customers')): ?>
                        <a href="<?= base_url('customers/create') ?>" class="btn btn-primary">
                            ➕ Nuevo Cliente
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
                        <table id="customersTable" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Documento</th>
                                    <th>Teléfono</th>
                                    <th>Email</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($customers)): ?>
                                    <?php foreach ($customers as $customer): ?>
                                        <tr>
                                            <td><strong><?= esc($customer['name']) ?></strong></td>
                                            <td><?= esc($customer['document']) ?></td>
                                            <td><?= esc($customer['phone']) ?></td>
                                            <td><?= esc($customer['email']) ?></td>
                                            <td>
                                                <a href="<?= base_url('customers/account/' . $customer['id']) ?>"
                                                    class="btn btn-sm btn-primary">
                                                    💰 Cuenta
                                                </a>
                                                <?php if (can_update('customers')): ?>
                                                    <a href="<?= base_url('customers/edit/' . $customer['id']) ?>"
                                                        class="btn btn-sm btn-secondary">
                                                        ✏️ Editar
                                                    </a>
                                                <?php endif; ?>

                                                <?php if (can_delete('customers')): ?>
                                                    <a href="<?= base_url('customers/delete/' . $customer['id']) ?>"
                                                        class="btn btn-sm btn-danger"
                                                        onclick="return confirm('¿Eliminar este cliente?')">
                                                        🗑️ Eliminar
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
        $('#customersTable').DataTable({
            'order': [[0, 'asc']]
        });
    });
</script>
";
echo view('templates/footer', ['extraJS' => $extraJS, 'scripts' => $scripts]);
?>