<?php
$extraCSS = ['assets/css/dashboard.css'];
echo view('templates/header', ['title' => $title ?? 'Dashboard', 'extraCSS' => $extraCSS]);
?>

<div class="dashboard-wrapper">
    <?= view('templates/sidebar') ?>

    <div class="main-content">
        <div class="topbar">
            <div class="topbar-title">
                <button class="menu-toggle" id="menuToggle">☰</button>
                <h2><?= $title ?? 'Dashboard' ?></h2>
            </div>
            <div class="topbar-actions">
                <span class="text-muted"><?= date('d/m/Y H:i') ?></span>
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

            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card primary">
                    <div class="stat-header">
                        <h3 class="stat-title">Ventas del Día</h3>
                        <div class="stat-icon primary">💰</div>
                    </div>
                    <p class="stat-value"><?= formato_moneda($stats['sales_today']) ?></p>
                    <p class="stat-change <?= $stats['sales_change'] >= 0 ? 'positive' : 'negative' ?>">
                        <?= $stats['sales_change'] >= 0 ? '+' : '' ?><?= formato_moneda($stats['sales_change']) ?>% vs
                        ayer
                    </p>
                </div>

                <div class="stat-card success">
                    <div class="stat-header">
                        <h3 class="stat-title">Productos</h3>
                        <div class="stat-icon success">📦</div>
                    </div>
                    <p class="stat-value"><?= $stats['products_count'] ?></p>
                    <p class="stat-change">Total en inventario</p>
                </div>

                <div class="stat-card warning">
                    <div class="stat-header">
                        <h3 class="stat-title">Cuentas por Cobrar</h3>
                        <div class="stat-icon warning">💵</div>
                    </div>
                    <p class="stat-value"><?= formato_moneda($stats['receivables']) ?></p>
                    <p class="stat-change">Pendientes</p>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h3 class="mb-0">Acciones Rápidas</h3>
                </div>
                <div class="card-body">
                    <div class="d-flex gap-2" style="flex-wrap: wrap;">
                        <!-- Control de Caja / Turno -->
                        <?php if (!empty($activeCashSession)): ?>
                            <a href="<?= base_url('cash-sessions/close/' . $activeCashSession['id']) ?>" class="btn btn-warning fw-bold">
                                🔒 Arqueo y Cierre de Caja (#<?= $activeCashSession['id'] ?>)
                            </a>
                        <?php else: ?>
                            <a href="<?= base_url('cash-sessions/open') ?>" class="btn btn-success fw-bold">
                                🔓 Abrir Turno de Caja
                            </a>
                        <?php endif; ?>

                        <!-- Operaciones principales -->
                        <a href="<?= base_url('sales/create') ?>" class="btn btn-primary">
                            💰 Nueva Venta
                        </a>
                        <a href="<?= base_url('accounts/transfer') ?>" class="btn btn-info fw-bold" style="background-color: #0284c7 !important; border-color: #0369a1 !important; color: #ffffff !important;">
                            💸 Retiro / Transferencia
                        </a>
                        <a href="<?= base_url('expenses/create') ?>" class="btn btn-danger fw-bold" style="background-color: #e11d48 !important; border-color: #be123c !important; color: #ffffff !important;">
                            💸 Registrar Gasto
                        </a>
                        <a href="<?= base_url('purchases/create') ?>" class="btn btn-primary">
                            🛒 Nueva Compra
                        </a>
                        <a href="<?= base_url('products/create') ?>" class="btn btn-secondary">
                            📦 Nuevo Producto
                        </a>
                        <a href="<?= base_url('customers/create') ?>" class="btn btn-secondary">
                            👥 Nuevo Cliente
                        </a>
                    </div>
                </div>
            </div>

            <!-- Welcome Message -->
            <div class="card" style="margin-top: 2rem;">
                <div class="card-body">
                    <h3>¡Bienvenido, <?= $user['username'] ?>!</h3>
                    <p class="text-muted">
                        Este es tu panel de control del Sistema Ventas 2026.
                        Desde aquí puedes acceder a todas las funcionalidades del sistema.
                    </p>

                    <?php if ($user['role'] === 'admin'): ?>
                        <div class="alert alert-info" style="margin-top: 1rem;">
                            <strong>Modo Administrador:</strong> Tienes acceso completo a todas las funcionalidades del
                            sistema.
                        </div>
                    <?php endif; ?>


                </div>
            </div>
        </div>
    </div>
</div>

<?php echo view('templates/footer'); ?>