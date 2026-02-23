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
                <?php if (can_insert('warehouses')): ?>
                    <a href="<?= base_url('warehouses/create') ?>" class="btn btn-primary">
                        ➕ Nuevo Depósito
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

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger">
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="warehousesTable" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Dirección</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($warehouses as $warehouse): ?>
                                    <tr>
                                        <td><?= $warehouse['id'] ?></td>
                                        <td>
                                            <strong><?= esc($warehouse['name']) ?></strong><br>
                                            <small class="text-muted"><?= esc($warehouse['description']) ?></small>
                                        </td>
                                        <td><?= esc($warehouse['address']) ?></td>
                                        <td>
                                            <?php if ($warehouse['is_active']): ?>
                                                <span class="badge badge-success">Activo</span>
                                            <?php else: ?>
                                                <span class="badge badge-secondary">Inactivo</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (can_update('warehouses')): ?>
                                                <a href="<?= base_url('warehouses/edit/' . $warehouse['id']) ?>"
                                                    class="btn btn-sm btn-warning">
                                                    ✏️
                                                </a>
                                            <?php endif; ?>

                                            <?php if (can_delete('warehouses')): ?>
                                                <a href="<?= base_url('warehouses/delete/' . $warehouse['id']) ?>"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="return confirm('¿Está seguro de eliminar este depósito?')">
                                                    🗑️
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
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
        $('#warehousesTable').DataTable({});
    });
</script>
";
echo view('templates/footer', ['extraJS' => $extraJS, 'scripts' => $scripts]);
?>