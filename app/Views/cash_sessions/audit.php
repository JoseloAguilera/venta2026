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
                <a href="<?= base_url('cash-sessions') ?>" class="btn btn-secondary">
                    ⬅️ Volver a Sesiones
                </a>
            </div>
        </div>

        <div class="content-area">
            <!-- Filtros de Auditoría -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">🔍 Filtros de Auditoría</h5>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('cash-sessions/audit') ?>" method="get" class="row g-3">
                        <div class="col-md-3">
                            <label for="start_date" class="form-label font-weight-bold">Fecha Desde</label>
                            <input type="date" name="start_date" id="start_date" class="form-control" value="<?= esc($start_date) ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="end_date" class="form-label font-weight-bold">Fecha Hasta</label>
                            <input type="date" name="end_date" id="end_date" class="form-control" value="<?= esc($end_date) ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="user_id" class="form-label font-weight-bold">Cajero / Usuario</label>
                            <select name="user_id" id="user_id" class="form-select">
                                <option value="">Todos los usuarios</option>
                                <?php if (!empty($users)): ?>
                                    <?php foreach ($users as $u): ?>
                                        <option value="<?= $u['id'] ?>" <?= $selected_user == $u['id'] ? 'selected' : '' ?>>
                                            <?= esc($u['username']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100 fw-bold">
                                📊 Filtrar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card shadow-sm border-start border-4 border-primary">
                        <div class="card-body">
                            <small class="text-muted d-block text-uppercase fw-bold">Turnos Auditados</small>
                            <h3 class="mb-0 fw-bold text-dark"><?= $metrics['total_sessions'] ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow-sm border-start border-4 border-danger">
                        <div class="card-body">
                            <small class="text-muted d-block text-uppercase fw-bold">Total Faltantes</small>
                            <h3 class="mb-0 fw-bold text-danger">-$<?= number_format($metrics['total_shortages'], 2) ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow-sm border-start border-4 border-info">
                        <div class="card-body">
                            <small class="text-muted d-block text-uppercase fw-bold">Total Sobrantes</small>
                            <h3 class="mb-0 fw-bold text-info">+$<?= number_format($metrics['total_overages'], 2) ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card shadow-sm border-start border-4 <?= $metrics['net_discrepancy'] >= 0 ? 'border-success' : 'border-danger' ?>">
                        <div class="card-body">
                            <small class="text-muted d-block text-uppercase fw-bold">Balance Neto Diferencias</small>
                            <h3 class="mb-0 fw-bold <?= $metrics['net_discrepancy'] >= 0 ? 'text-success' : 'text-danger' ?>">
                                <?= $metrics['net_discrepancy'] >= 0 ? '+' : '' ?>$<?= number_format($metrics['net_discrepancy'], 2) ?>
                            </h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Audit Table -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Detalle de Cierres y Arqueos Auditados</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="auditTable" class="table table-striped table-hover align-middle">
                            <thead>
                                <tr>
                                    <th># Turno</th>
                                    <th>Fecha / Hora Cierre</th>
                                    <th>Cajero Responsable</th>
                                    <th>Caja / Cuenta</th>
                                    <th>Saldo Esperado</th>
                                    <th>Saldo Real Contado</th>
                                    <th>Diferencia Arqueo</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($sessions)): ?>
                                    <?php foreach ($sessions as $s): ?>
                                        <?php $disc = (float)($s['discrepancy'] ?? 0); ?>
                                        <tr>
                                            <td><strong>#<?= $s['id'] ?></strong></td>
                                            <td><?= date('d/m/Y H:i', strtotime($s['closing_time'])) ?></td>
                                            <td><?= esc($s['user_name'] ?? 'Usuario #' . $s['user_id']) ?></td>
                                            <td><?= esc($s['account_name'] ?? 'Cuenta #' . $s['account_id']) ?></td>
                                            <td>$<?= number_format((float)$s['closing_amount'] - $disc, 2) ?></td>
                                            <td class="fw-bold">$<?= number_format((float)$s['closing_amount'], 2) ?></td>
                                            <td>
                                                <?php if ($disc == 0): ?>
                                                    <span class="badge bg-success">$0.00 (Exacto)</span>
                                                <?php elseif ($disc > 0): ?>
                                                    <span class="badge bg-info text-dark">+$<?= number_format($disc, 2) ?> (Sobrante)</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">-$<?= number_format(abs($disc), 2) ?> (Faltante)</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="<?= base_url('cash-sessions/view/' . $s['id']) ?>" class="btn btn-sm btn-outline-primary me-1" title="Ver Detalle">
                                                    👁️ Ver
                                                </a>
                                                <a href="<?= base_url('cash-sessions/ticket/' . $s['id']) ?>" class="btn btn-sm btn-outline-secondary" target="_blank" title="Imprimir Ticket">
                                                    🖨️ Ticket
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
        $('#auditTable').DataTable({
            'order': [[0, 'desc']]
        });
    });
</script>
";
echo view('templates/footer', ['extraJS' => $extraJS, 'scripts' => $scripts]);
?>
