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
                    <a href="<?= base_url('accounts/create') ?>" class="btn btn-primary">
                        ➕ Nueva Cuenta
                    </a>
                </div>
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
                        <table id="accountsTable" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Caja / Cuenta</th>
                                    <th>Tipo</th>
                                    <th>Saldo Actual</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($accounts)): ?>
                                    <?php foreach ($accounts as $account): ?>
                                        <tr>
                                            <td><strong><?= esc($account['name']) ?></strong></td>
                                            <td>
                                                <?php
                                                    if ($account['type'] === 'cash') echo 'Efectivo / Caja Fuerte';
                                                    elseif ($account['type'] === 'bank') echo 'Cuenta Bancaria';
                                                    elseif ($account['type'] === 'digital_wallet') echo 'Billetera Digital';
                                                    else echo esc($account['type']);
                                                ?>
                                            </td>
                                            <td style="font-size: 1.1em; font-weight: bold;" class="<?= $account['balance'] < 0 ? 'text-danger' : 'text-success' ?>">
                                                <?= formato_moneda($account['balance']) ?>
                                            </td>
                                            <td>
                                                <?php if($account['status'] == 1): ?>
                                                    <span class="badge bg-success">Activa</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Inactiva</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="<?= base_url('accounts/statement/' . $account['id']) ?>"
                                                    class="btn btn-sm btn-primary" title="Arqueo / Movimientos">
                                                    📊
                                                </a>
                                                <a href="<?= base_url('accounts/edit/' . $account['id']) ?>"
                                                    class="btn btn-sm btn-secondary" title="Editar">
                                                    ✏️
                                                </a>
                                                <a href="<?= base_url('accounts/delete/' . $account['id']) ?>"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="return confirm('¿Eliminar esta cuenta?')" title="Eliminar">
                                                    🗑️
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
        $('#accountsTable').DataTable({
            'order': [[0, 'asc']]
        });
    });
</script>
";
echo view('templates/footer', ['extraJS' => $extraJS, 'scripts' => $scripts]);
?>
