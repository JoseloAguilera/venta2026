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
                <h2>
                    <?= $title ?>
                </h2>
            </div>
            <div class="topbar-actions">
                <?php if (can_insert('users')): ?>
                    <a href="<?= base_url('users/create') ?>" class="btn btn-primary">
                        ➕ Nuevo Usuario
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
                        <table id="usersTable" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Usuario</th>
                                    <th>Email</th>
                                    <th>Rol</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($users)): ?>
                                    <?php foreach ($users as $user): ?>
                                        <tr>
                                            <td><strong>
                                                    <?= esc($user['username']) ?>
                                                </strong></td>
                                            <td>
                                                <?= esc($user['email']) ?>
                                            </td>
                                            <td>
                                                <span class="badge badge-info">
                                                    <?= esc($user['role_name'] ?? 'Sin Rol') ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if (!empty($user['active'])): ?>
                                                    <span class="badge badge-success">Activo</span>
                                                <?php else: ?>
                                                    <span class="badge badge-danger">Inactivo</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (can_update('users')): ?>
                                                    <a href="<?= base_url('users/edit/' . $user['id']) ?>"
                                                        class="btn btn-sm btn-secondary" title="Editar">
                                                        ✏️
                                                    </a>
                                                <?php endif; ?>

                                                <?php if (can_delete('users')): ?>
                                                    <?php if (session()->get('user_id') != $user['id']): ?>
                                                        <a href="<?= base_url('users/delete/' . $user['id']) ?>"
                                                            class="btn btn-sm btn-danger"
                                                            onclick="return confirm('¿Está seguro de eliminar este usuario?')" title="Eliminar">
                                                            🗑️
                                                        </a>
                                                    <?php endif; ?>
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
        $('#usersTable').DataTable({});
    });
</script>
";
echo view('templates/footer', ['extraJS' => $extraJS, 'scripts' => $scripts]);
?>