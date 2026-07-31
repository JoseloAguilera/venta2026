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
                <a href="<?= base_url('cash-sessions/ticket/' . $session['id']) ?>" class="btn btn-secondary" target="_blank">
                    🖨️ Imprimir Ticket
                </a>
                <a href="<?= base_url('cash-sessions') ?>" class="btn btn-primary">
                    ⬅️ Volver a Lista
                </a>
            </div>
        </div>

        <div class="content-area">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success">
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <!-- Card de Resumen -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Informe de Arqueo y Cierre de Caja - Turno #<?= $session['id'] ?></h5>
                    <span class="badge <?= $session['status'] === 'closed' ? 'bg-secondary' : 'bg-success' ?> fs-6">
                        <?= $session['status'] === 'closed' ? 'CERRADA' : 'ABIERTA' ?>
                    </span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 border-end">
                            <small class="text-muted d-block">Caja / Cuenta:</small>
                            <h6 class="fw-bold"><?= esc($session['account_name']) ?></h6>
                            
                            <small class="text-muted d-block mt-2">Cajero Responsable:</small>
                            <h6 class="fw-bold"><?= esc($session['user_name']) ?></h6>
                        </div>

                        <div class="col-md-3 border-end">
                            <small class="text-muted d-block">Fecha/Hora Apertura:</small>
                            <h6 class="fw-bold"><?= date('d/m/Y H:i', strtotime($session['opening_time'])) ?></h6>

                            <small class="text-muted d-block mt-2">Fecha/Hora Cierre:</small>
                            <h6 class="fw-bold"><?= $session['closing_time'] ? date('d/m/Y H:i', strtotime($session['closing_time'])) : 'En proceso' ?></h6>
                        </div>

                        <div class="col-md-3 border-end">
                            <small class="text-muted d-block">Monto Apertura:</small>
                            <h6 class="fw-bold text-primary">$<?= number_format($session['opening_amount'], 2) ?></h6>

                            <small class="text-muted d-block mt-2">Esperado en Caja:</small>
                            <h6 class="fw-bold text-dark">$<?= number_format($expected_amount, 2) ?></h6>
                        </div>

                        <div class="col-md-3">
                            <small class="text-muted d-block">Efectivo Real Contado:</small>
                            <h6 class="fw-bold text-success">$<?= $session['closing_amount'] !== null ? number_format($session['closing_amount'], 2) : '-' ?></h6>

                            <small class="text-muted d-block mt-2">Diferencia (Arqueo):</small>
                            <?php if ($session['closing_amount'] !== null): ?>
                                <?php $disc = (float)$session['discrepancy']; ?>
                                <?php if ($disc == 0): ?>
                                    <h6 class="fw-bold text-success">$0.00 (Exacto)</h6>
                                <?php elseif ($disc > 0): ?>
                                    <h6 class="fw-bold text-info">+$<?= number_format($disc, 2) ?> (Sobrante)</h6>
                                <?php else: ?>
                                    <h6 class="fw-bold text-danger">-$<?= number_format(abs($disc), 2) ?> (Faltante)</h6>
                                <?php endif; ?>
                            <?php else: ?>
                                <h6 class="fw-bold text-muted">-</h6>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Movimientos de la Sesión -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Detalle de Movimientos Registrados en la Sesión</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="transactionsTable" class="table table-striped table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Fecha / Hora</th>
                                    <th>Tipo</th>
                                    <th>Origen / Ref.</th>
                                    <th>Descripción</th>
                                    <th>Monto</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($transactions)): ?>
                                    <?php foreach ($transactions as $tx): ?>
                                        <tr>
                                            <td><?= date('d/m/Y H:i:s', strtotime($tx['date'])) ?></td>
                                            <td>
                                                <?php if ($tx['type'] === 'income'): ?>
                                                    <span class="badge bg-success">INGRESO</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">EGRESO</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-outline-dark border text-dark">
                                                    <?= esc(strtoupper($tx['reference_type'])) ?>
                                                    <?= $tx['reference_id'] ? '#' . $tx['reference_id'] : '' ?>
                                                </span>
                                            </td>
                                            <td><?= esc($tx['description']) ?></td>
                                            <td class="fw-bold <?= $tx['type'] === 'income' ? 'text-success' : 'text-danger' ?>">
                                                <?= $tx['type'] === 'income' ? '+' : '-' ?>$<?= number_format($tx['amount'], 2) ?>
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
        $('#transactionsTable').DataTable({
            'order': [[0, 'asc']]
        });
    });
</script>
";
echo view('templates/footer', ['extraJS' => $extraJS, 'scripts' => $scripts]);
?>
