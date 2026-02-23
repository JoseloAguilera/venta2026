<?php
$extraCSS = [
    'assets/css/dashboard.css'
];
echo view('templates/header', ['title' => $title, 'extraCSS' => $extraCSS]);
?>

<div class="dashboard-wrapper">
    <?= view('templates/sidebar') ?>

    <div class="main-content">
        <div class="topbar">
            <div class="topbar-title">
                <button class="menu-toggle" id="menuToggle">☰</button>
                <h2>
                    <?= $title ?>
                </h2>
            </div>
            <div class="topbar-actions">
                <a href="<?= base_url('users') ?>" class="btn btn-secondary">
                    🔙 Volver
                </a>
            </div>
        </div>

        <div class="content-area">
            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                            <li>
                                <?= esc($error) ?>
                            </li>
                        <?php endforeach ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <form action="<?= base_url('users/update/' . $user['id']) ?>" method="post">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label for="username" class="form-label">Nombre de Usuario *</label>
                            <input type="text" class="form-control" id="username" name="username"
                                value="<?= old('username', $user['username']) ?>" required minlength="3" maxlength="50">
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" class="form-control" id="email" name="email"
                                value="<?= old('email', $user['email']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="role_id" class="form-label">Rol *</label>
                            <select class="form-select" id="role_id" name="role_id" required>
                                <option value="">Seleccione un rol...</option>
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?= $role['id'] ?>" <?= old('role_id', $user['role_id']) == $role['id'] ? 'selected' : '' ?>>
                                        <?= esc($role['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña (Opcional)</label>
                            <input type="password" class="form-control" id="password" name="password" minlength="6">
                            <small class="text-muted">Dejar en blanco para mantener la contraseña actual.</small>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="active" name="active" value="1"
                                <?= old('active', $user['active'] ?? 0) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="active">Usuario Activo</label>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">💾 Actualizar Usuario</button>
                            <a href="<?= base_url('users') ?>" class="btn btn-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?= view('templates/footer') ?>