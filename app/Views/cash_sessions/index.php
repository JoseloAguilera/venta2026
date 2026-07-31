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
                <a href="<?= base_url('cash-sessions/audit') ?>" class="btn btn-outline-primary me-2 fw-bold">
                    📊 Auditoría de Arqueos
                </a>
                <?php if (!$activeSession): ?>
                    <a href="<?= base_url('cash-sessions/open') ?>" class="btn btn-success">
                        🔓 Abrir Caja de Turno
                    </a>
                <?php else: ?>
                    <a href="<?= base_url('cash-sessions/close/' . $activeSession['id']) ?>" class="btn btn-warning">
                        🔒 Arqueo y Cierre de Caja (#<?= $activeSession['id'] ?>)
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

            <!-- Estado de Caja Activa -->
            <div class="card mb-4 border-start border-4 <?= $activeSession ? 'border-success' : 'border-secondary' ?>">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title mb-1">
                                Mi Estado de Caja: 
                                <?php if ($activeSession): ?>
                                    <span class="badge bg-success fs-6">Caja Abierta (Turno Activo)</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary fs-6">Sin Caja Abierta</span>
                                <?php endif; ?>
                            </h5>
                            <?php if ($activeSession): ?>
                                <p class="mb-0 text-muted">
                                    Cuenta: <strong><?= esc($activeSession['account_name']) ?></strong> | 
                                    Apertura: <strong>$<?= number_format($activeSession['opening_amount'], 2) ?></strong> | 
                                    Hora de Inicio: <strong><?= date('d/m/Y H:i', strtotime($activeSession['opening_time'])) ?></strong>
                                </p>
                            <?php else: ?>
                                <p class="mb-0 text-muted">Debe abrir caja para poder registrar cobros en efectivo en el sistema.</p>
                            <?php endif; ?>
                        </div>
                        <div>
                            <?php if ($activeSession): ?>
                                <a href="<?= base_url('cash-sessions/close/' . $activeSession['id']) ?>" class="btn btn-outline-warning">
                                    Cerrar / Arqueo
                                </a>
                            <?php else: ?>
                                <a href="<?= base_url('cash-sessions/open') ?>" class="btn btn-outline-success">
                                    Abrir Turno
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabla de Histórico -->
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Historial de Turnos y Cierres de Caja</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="sessionsTable" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th># Turno</th>
                                    <th>Cajero / Usuario</th>
                                    <th>Caja / Cuenta</th>
                                    <th>Apertura</th>
                                    <th>Cierre Esperado / Real</th>
                                    <th>Diferencia</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($sessions)): ?>
                                    <?php foreach ($sessions as $s): ?>
                                        <tr>
                                            <td><strong>#<?= $s['id'] ?></strong></td>
                                            <td><?= esc($s['user_name'] ?? 'Usuario ' . $s['user_id']) ?></td>
                                            <td><?= esc($s['account_name'] ?? 'Cuenta ' . $s['account_id']) ?></td>
                                            <td>
                                                <small class="d-block text-muted"><?= date('d/m/Y H:i', strtotime($s['opening_time'])) ?></small>
                                                <strong>$<?= number_format($s['opening_amount'], 2) ?></strong>
                                            </td>
                                            <td>
                                                <?php if ($s['status'] === 'closed'): ?>
                                                    <small class="d-block text-muted"><?= date('d/m/Y H:i', strtotime($s['closing_time'])) ?></small>
                                                    <strong>$<?= number_format($s['closing_amount'], 2) ?></strong>
                                                <?php else: ?>
                                                    <span class="text-muted">En transcurso</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($s['status'] === 'closed'): ?>
                                                    <?php $disc = (float)$s['discrepancy']; ?>
                                                    <?php if ($disc == 0): ?>
                                                        <span class="badge bg-success">$0.00 (Exacto)</span>
                                                    <?php elseif ($disc > 0): ?>
                                                        <span class="badge bg-info text-dark">+$<?= number_format($disc, 2) ?> (Sobrante)</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">-$<?= number_format(abs($disc), 2) ?> (Faltante)</span>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($s['status'] === 'open'): ?>
                                                    <span class="badge bg-success">Abierta</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Cerrada</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($s['status'] === 'open'): ?>
                                                    <a href="<?= base_url('cash-sessions/close/' . $s['id']) ?>" class="btn btn-sm btn-warning" title="Realizar Arqueo y Cierre">
                                                        🔒 Cierre
                                                    </a>
                                                <?php else: ?>
                                                    <a href="<?= base_url('cash-sessions/view/' . $s['id']) ?>" class="btn btn-sm btn-info text-white" title="Ver Detalle de Arqueo">
                                                        👁️ Ver
                                                    </a>
                                                    <a href="<?= base_url('cash-sessions/ticket/' . $s['id']) ?>" class="btn btn-sm btn-secondary" target="_blank" title="Ticket de Cierre">
                                                        🖨️ Ticket
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
        $('#sessionsTable').DataTable({
            'order': [[0, 'desc']]
        });
    });
</script>
";
echo view('templates/footer', ['extraJS' => $extraJS, 'scripts' => $scripts]);
?>
