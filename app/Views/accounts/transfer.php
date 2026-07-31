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
                <a href="<?= base_url('accounts') ?>" class="btn btn-secondary">
                    ⬅️ Volver a Cuentas
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
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0">💸 Retiro de Efectivo / Transferencia entre Cuentas</h5>
                        </div>
                        <div class="card-body">
                            <form action="<?= base_url('accounts/store-transfer') ?>" method="post">
                                <?= csrf_field() ?>

                                <div class="mb-3">
                                    <label for="from_account_id" class="form-label font-weight-bold">Cuenta / Caja de Origen (Débito) <span class="text-danger">*</span></label>
                                    <select name="from_account_id" id="from_account_id" class="form-select form-select-lg" required>
                                        <option value="">Seleccione cuenta de origen</option>
                                        <?php if (!empty($accounts)): ?>
                                            <?php foreach ($accounts as $acc): ?>
                                                <option value="<?= $acc['id'] ?>" <?= old('from_account_id') == $acc['id'] ? 'selected' : '' ?>>
                                                    <?= esc($acc['name']) ?> (Saldo: $<?= number_format($acc['balance'], 2) ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="to_account_id" class="form-label font-weight-bold">Cuenta / Caja de Destino (Crédito) <span class="text-danger">*</span></label>
                                    <select name="to_account_id" id="to_account_id" class="form-select form-select-lg" required>
                                        <option value="">Seleccione cuenta de destino</option>
                                        <?php if (!empty($accounts)): ?>
                                            <?php foreach ($accounts as $acc): ?>
                                                <option value="<?= $acc['id'] ?>" <?= old('to_account_id') == $acc['id'] ? 'selected' : '' ?>>
                                                    <?= esc($acc['name']) ?> (Saldo: $<?= number_format($acc['balance'], 2) ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="amount" class="form-label font-weight-bold">Monto a Transferir ($) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" name="amount" id="amount" class="form-control form-control-lg text-primary font-weight-bold" value="<?= old('amount') ?>" required min="0.01" placeholder="0.00">
                                </div>

                                <div class="mb-4">
                                    <label for="description" class="form-label font-weight-bold">Motivo / Descripción <span class="text-danger">*</span></label>
                                    <input type="text" name="description" id="description" class="form-control" value="<?= old('description') ?>" required placeholder="Ej: Retiro por seguridad a caja fuerte / Depósito en banco">
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg fw-bold">
                                        💸 Confirmar Transferencia
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
