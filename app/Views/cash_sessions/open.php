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
                <a href="<?= base_url('cash-sessions') ?>" class="btn btn-secondary">
                    ⬅️ Volver a Sesiones
                </a>
            </div>
        </div>

        <div class="content-area">
            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger">
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="card shadow-sm">
                        <div class="card-header bg-success text-white">
                            <h5 class="card-title mb-0">🔓 Formulario de Apertura de Caja</h5>
                        </div>
                        <div class="card-body">
                            <form action="<?= base_url('cash-sessions/store-open') ?>" method="post">
                                <?= csrf_field() ?>

                                <div class="mb-3">
                                    <label for="account_id" class="form-label font-weight-bold">Seleccionar Caja / Cuenta de Efectivo <span class="text-danger">*</span></label>
                                    <select name="account_id" id="account_id" class="form-select form-select-lg" required>
                                        <?php if (!empty($accounts)): ?>
                                            <?php foreach ($accounts as $acc): ?>
                                                <option value="<?= $acc['id'] ?>" <?= old('account_id') == $acc['id'] ? 'selected' : '' ?>>
                                                    <?= esc($acc['name']) ?> (Saldo sistema actual: $<?= number_format($acc['balance'], 2) ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <small class="text-muted d-block mt-1">Seleccione la caja física de donde operará su turno.</small>
                                </div>

                                <div class="mb-4">
                                    <label for="opening_amount" class="form-label font-weight-bold">Monto de Apertura / Fondo de Cambio ($) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" name="opening_amount" id="opening_amount" 
                                           class="form-control form-control-lg text-success font-weight-bold" 
                                           value="<?= old('opening_amount', '0.00') ?>" required min="0" placeholder="0.00">
                                    <small class="text-muted d-block mt-1">Ingrese la cantidad en efectivo disponible en la caja física al iniciar el turno.</small>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-success btn-lg">
                                        🔓 Iniciar Turno y Abrir Caja
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= view('templates/footer') ?>
