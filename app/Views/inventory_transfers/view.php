<?php 
$extraCSS = ['assets/css/dashboard.css'];
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
                <a href="<?= base_url('inventory-transfers') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
                <button class="btn btn-primary" onclick="window.print()">
                    <i class="fas fa-print"></i> Imprimir
                </button>
            </div>
        </div>

        <div class="content-area">
            <div class="row">
                <div class="col-md-12">
                    <!-- Transfer Info -->
                    <div class="card mb-4">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Información General</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="fw-bold text-muted">Código</label>
                                    <p class="fs-5"><?= esc($transfer['transfer_code']) ?></p>
                                </div>
                                <div class="col-md-3">
                                    <label class="fw-bold text-muted">Fecha</label>
                                    <p class="fs-5"><?= date('d/m/Y H:i', strtotime($transfer['created_at'])) ?></p>
                                </div>
                                <div class="col-md-3">
                                    <label class="fw-bold text-muted">Usuario</label>
                                    <p class="fs-5"><?= esc($transfer['username']) ?></p>
                                </div>
                                <div class="col-md-3">
                                    <label class="fw-bold text-muted">Estado</label>
                                    <p>
                                        <?php if($transfer['status'] == 'completed'): ?>
                                            <span class="badge bg-success fs-6">Completado</span>
                                        <?php elseif($transfer['status'] == 'pending'): ?>
                                            <span class="badge bg-warning fs-6">Pendiente</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger fs-6">Cancelado</span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="p-3 border rounded bg-light">
                                        <label class="fw-bold text-muted d-block mb-2">Origen</label>
                                        <h4 class="text-danger"><i class="fas fa-warehouse me-2"></i><?= esc($transfer['source_warehouse']) ?></h4>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-3 border rounded bg-light">
                                        <label class="fw-bold text-muted d-block mb-2">Destino</label>
                                        <h4 class="text-success"><i class="fas fa-warehouse me-2"></i><?= esc($transfer['destination_warehouse']) ?></h4>
                                    </div>
                                </div>
                            </div>
                            <?php if(!empty($transfer['notes'])): ?>
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <label class="fw-bold text-muted">Notas</label>
                                        <p class="border p-2 rounded"><?= nl2br(esc($transfer['notes'])) ?></p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Items -->
                    <div class="card">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Productos Transferidos</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Código</th>
                                            <th>Producto</th>
                                            <th class="text-center">Cantidad</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($items as $item): ?>
                                            <tr>
                                                <td><?= esc($item['code']) ?></td>
                                                <td><?= esc($item['name']) ?></td>
                                                <td class="text-center fw-bold"><?= esc($item['quantity']) ?></td>
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
    </div>
</div>

<?php echo view('templates/footer'); ?>
