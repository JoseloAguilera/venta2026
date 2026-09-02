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
            <div class="topbar-actions">
                <span class="text-muted">📜 Auditoría General del Sistema</span>
            </div>
        </div>

        <div class="content-area">
            <!-- Filtros de Bitácora -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">🔍 Filtros de Búsqueda de Eventos</h5>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('audit-logs') ?>" method="get" class="row g-3">
                        <div class="col-md-3">
                            <label for="start_date" class="form-label font-weight-bold">Fecha Desde</label>
                            <input type="date" name="start_date" id="start_date" class="form-control" value="<?= esc($start_date) ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="end_date" class="form-label font-weight-bold">Fecha Hasta</label>
                            <input type="date" name="end_date" id="end_date" class="form-control" value="<?= esc($end_date) ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="module" class="form-label font-weight-bold">Módulo</label>
                            <select name="module" id="module" class="form-select">
                                <option value="">Todos los Módulos</option>
                                <option value="Seguridad" <?= $selected_module === 'Seguridad' ? 'selected' : '' ?>>Seguridad / Acceso</option>
                                <option value="Ventas" <?= $selected_module === 'Ventas' ? 'selected' : '' ?>>Ventas</option>
                                <option value="Compras" <?= $selected_module === 'Compras' ? 'selected' : '' ?>>Compras</option>
                                <option value="Caja" <?= $selected_module === 'Caja' ? 'selected' : '' ?>>Caja / Arqueos</option>
                                <option value="Inventario" <?= $selected_module === 'Inventario' ? 'selected' : '' ?>>Inventario / Productos</option>
                                <option value="Cuentas" <?= $selected_module === 'Cuentas' ? 'selected' : '' ?>>Cuentas / Bancos</option>
                                <option value="Usuarios" <?= $selected_module === 'Usuarios' ? 'selected' : '' ?>>Usuarios / Roles</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="user_id" class="form-label font-weight-bold">Usuario</label>
                            <select name="user_id" id="user_id" class="form-select">
                                <option value="">Todos los Usuarios</option>
                                <?php if (!empty($users)): ?>
                                    <?php foreach ($users as $u): ?>
                                        <option value="<?= $u['id'] ?>" <?= $selected_user == $u['id'] ? 'selected' : '' ?>>
                                            <?= esc($u['username']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-12 text-end">
                            <button type="submit" class="btn btn-primary fw-bold">
                                🔍 Filtrar Bitácora
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabla de Bitácora -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">📜 Eventos y Registro de Actividad</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="auditTable" class="table table-striped table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Fecha / Hora</th>
                                    <th>Usuario</th>
                                    <th>IP</th>
                                    <th>Módulo</th>
                                    <th>Acción</th>
                                    <th>Descripción de la Operación</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($logs)): ?>
                                    <?php foreach ($logs as $log): ?>
                                        <tr>
                                            <td data-order="<?= $log['created_at'] ?>"><small><?= date('d/m/Y H:i:s', strtotime($log['created_at'])) ?></small></td>
                                            <td><strong><?= esc($log['username'] ?? 'Sistema') ?></strong></td>
                                            <td><code class="text-dark"><?= esc($log['ip_address'] ?? '127.0.0.1') ?></code></td>
                                            <td>
                                                <span class="badge bg-secondary"><?= esc($log['module']) ?></span>
                                            </td>
                                            <td>
                                                <?php
                                                $actionClass = 'bg-primary';
                                                if (strpos(strtolower($log['action']), 'anular') !== false || strpos(strtolower($log['action']), 'eliminar') !== false || strpos(strtolower($log['action']), 'fallido') !== false) {
                                                    $actionClass = 'bg-danger';
                                                } elseif (strpos(strtolower($log['action']), 'crear') !== false || strpos(strtolower($log['action']), 'exitoso') !== false) {
                                                    $actionClass = 'bg-success';
                                                }
                                                ?>
                                                <span class="badge <?= $actionClass ?>"><?= esc($log['action']) ?></span>
                                            </td>
                                            <td><?= esc($log['description']) ?></td>
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
        $('#auditTable').DataTable({
            'order': [[0, 'desc']]
        });
    });
</script>
";
echo view('templates/footer', ['extraJS' => $extraJS, 'scripts' => $scripts]);
?>
