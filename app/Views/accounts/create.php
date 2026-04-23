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
        </div>

        <div class="content-area">
            <div class="card">
                <div class="card-body">
                    <?php if (session()->has('errors')): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach (session('errors') as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach ?>
                            </ul>
                        </div>
                    <?php endif ?>

                    <form action="<?= base_url('accounts/store') ?>" method="POST">
                        <?= csrf_field() ?>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nombre de Cuenta/Caja *</label>
                                <input type="text" name="name" class="form-control" value="<?= old('name') ?>" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tipo *</label>
                                <select name="type" class="form-control" required>
                                    <option value="cash" <?= old('type') == 'cash' ? 'selected' : '' ?>>Efectivo / Caja Fuerte</option>
                                    <option value="bank" <?= old('type') == 'bank' ? 'selected' : '' ?>>Cuenta Bancaria</option>
                                    <option value="digital_wallet" <?= old('type') == 'digital_wallet' ? 'selected' : '' ?>>Billetera Digital</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Saldo Inicial</label>
                                <input type="number" step="0.01" name="initial_balance" class="form-control" value="<?= old('initial_balance', '0') ?>">
                                <small class="text-muted">Si es una caja de efectivo, es tu balance actual. Puedes dejar en 0 e ingresar dinero después.</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Estado</label>
                                <select name="status" class="form-control">
                                    <option value="1">Activa</option>
                                    <option value="0">Inactiva</option>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">💾 Guardar</button>
                            <a href="<?= base_url('accounts') ?>" class="btn btn-secondary">❌ Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo view('templates/footer'); ?>
