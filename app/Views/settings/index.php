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
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success">
                            <?= session()->getFlashdata('success') ?>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('errors')): ?>
                        <div class="alert alert-danger">
                            <ul style="margin: 0; padding-left: 1.25rem;">
                                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('settings/update') ?>" method="POST">
                        <?= csrf_field() ?>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h4 class="mb-3">Datos de la Empresa</h4>
                                
                                <div class="form-group">
                                    <label for="company_name" class="form-label">Nombre de la Empresa *</label>
                                    <input 
                                        type="text" 
                                        id="company_name" 
                                        name="company_name" 
                                        class="form-control" 
                                        value="<?= old('company_name', $settings['company_name'] ?? '') ?>"
                                        required
                                    >
                                </div>

                                <div class="form-group">
                                    <label for="company_ruc" class="form-label">RUC / Documento *</label>
                                    <input 
                                        type="text" 
                                        id="company_ruc" 
                                        name="company_ruc" 
                                        class="form-control" 
                                        value="<?= old('company_ruc', $settings['company_ruc'] ?? '') ?>"
                                        required
                                    >
                                </div>

                                <div class="form-group">
                                    <label for="company_address" class="form-label">Dirección *</label>
                                    <input 
                                        type="text" 
                                        id="company_address" 
                                        name="company_address" 
                                        class="form-control" 
                                        value="<?= old('company_address', $settings['company_address'] ?? '') ?>"
                                        required
                                    >
                                </div>

                                <div class="form-group">
                                    <label for="company_email" class="form-label">Email *</label>
                                    <input 
                                        type="email" 
                                        id="company_email" 
                                        name="company_email" 
                                        class="form-control" 
                                        value="<?= old('company_email', $settings['company_email'] ?? '') ?>"
                                        required
                                    >
                                </div>

                                <div class="form-group">
                                    <label for="company_phone" class="form-label">Teléfono</label>
                                    <input 
                                        type="text" 
                                        id="company_phone" 
                                        name="company_phone" 
                                        class="form-control" 
                                        value="<?= old('company_phone', $settings['company_phone'] ?? '') ?>"
                                    >
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h4 class="mb-3">🔑 Claves de Autorización y Seguridad</h4>
                                <p class="text-muted small mb-3">Pase el cursor sobre el ícono <span class="badge bg-info text-dark">ℹ️ Info</span> o el botón para ver cuándo se solicita cada clave:</p>

                                <!-- 1. PIN Maestro / General de Supervisor -->
                                <div class="card bg-light border-0 mb-2 shadow-sm">
                                    <div class="card-body p-2 px-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                                        <h6 class="fw-bold text-danger mb-0 d-flex align-items-center">
                                            1. 🔐 PIN Maestro / General
                                            <span class="info-icon ms-2" data-bs-toggle="tooltip" data-bs-placement="top" title="¿Cuándo se solicita? Como clave maestra para autorizar cualquier anulación o acción protegida si no hay una clave específica.">ℹ️</span>
                                        </h6>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-secondary">🔒 Oculto</span>
                                            <button type="button" class="btn btn-sm btn-outline-danger fw-bold" 
                                                data-bs-toggle="tooltip" data-bs-placement="bottom" title="¿Cuándo se solicita? Como clave maestra para autorizar cualquier anulación o acción protegida si no hay una clave específica."
                                                onclick="openChangeKeyModal('supervisor_pin', 'PIN Maestro / General de Supervisor')">
                                                🔑 Cambiar
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- 2. PIN Anulación de Ventas -->
                                <div class="card bg-light border-0 mb-2 shadow-sm">
                                    <div class="card-body p-2 px-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                                        <h6 class="fw-bold text-danger mb-0 d-flex align-items-center">
                                            2. 🚫 PIN Anulación Ventas
                                            <span class="info-icon ms-2" data-bs-toggle="tooltip" data-bs-placement="top" title="¿Cuándo se solicita? Al hacer clic en Anular Venta en la lista de ventas pasadas.">ℹ️</span>
                                        </h6>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-secondary">🔒 Oculto</span>
                                            <button type="button" class="btn btn-sm btn-outline-danger fw-bold" 
                                                data-bs-toggle="tooltip" data-bs-placement="bottom" title="¿Cuándo se solicita? Al hacer clic en Anular Venta en la lista de ventas pasadas."
                                                onclick="openChangeKeyModal('pin_sale_annul', 'PIN de Anulación de Ventas')">
                                                🔑 Cambiar
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- 3. Contraseña Precio Mínimo / Descuentos -->
                                <div class="card bg-light border-0 mb-2 shadow-sm">
                                    <div class="card-body p-2 px-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                                        <h6 class="fw-bold text-warning mb-0 d-flex align-items-center">
                                            3. 🏷️ Clave Precio Mínimo
                                            <span class="info-icon ms-2" data-bs-toggle="tooltip" data-bs-placement="top" title="¿Cuándo se solicita? Cuando un vendedor intenta registrar una venta con precio menor al mínimo permitido.">ℹ️</span>
                                        </h6>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-secondary">🔒 Oculto</span>
                                            <button type="button" class="btn btn-sm btn-outline-warning text-dark fw-bold" 
                                                data-bs-toggle="tooltip" data-bs-placement="bottom" title="¿Cuándo se solicita? Cuando un vendedor intenta registrar una venta con precio menor al mínimo permitido."
                                                onclick="openChangeKeyModal('min_price_password', 'Clave de Autorización de Precio Mínimo')">
                                                🔑 Cambiar
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- 4. PIN Anulación de Cobranzas -->
                                <div class="card bg-light border-0 mb-2 shadow-sm">
                                    <div class="card-body p-2 px-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center">
                                            4. 💵 PIN Anulación Cobros
                                            <span class="info-icon ms-2" data-bs-toggle="tooltip" data-bs-placement="top" title="¿Cuándo se solicita? Al revertir o cancelar el cobro de un cliente registrado a crédito.">ℹ️</span>
                                        </h6>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-secondary">🔒 Oculto</span>
                                            <button type="button" class="btn btn-sm btn-outline-dark fw-bold" 
                                                data-bs-toggle="tooltip" data-bs-placement="bottom" title="¿Cuándo se solicita? Al revertir o cancelar el cobro de un cliente registrado a crédito."
                                                onclick="openChangeKeyModal('pin_collection_annul', 'PIN de Anulación de Cobranzas')">
                                                🔑 Cambiar
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- 5. Bloqueo por Inactividad -->
                                <div class="card bg-light border-0 mb-2 shadow-sm">
                                    <div class="card-body p-2 px-3">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <h6 class="fw-bold text-primary mb-0 d-flex align-items-center">
                                                5. 🔒 Inactividad (Auto-Lock)
                                                <span class="info-icon ms-2" data-bs-toggle="tooltip" data-bs-placement="top" title="¿Cuándo se solicita? Al pasar el tiempo configurado sin uso de teclado/mouse (exige la clave del usuario activo).">ℹ️</span>
                                            </h6>
                                        </div>
                                        <div class="row g-2 mt-1">
                                            <div class="col-md-6">
                                                <label for="autolock_enabled" class="form-label font-weight-bold small">Estado</label>
                                                <select id="autolock_enabled" name="autolock_enabled" class="form-control form-control-sm">
                                                    <option value="1" <?= (old('autolock_enabled', $settings['autolock_enabled'] ?? '1') == '1') ? 'selected' : '' ?>>✅ Activado</option>
                                                    <option value="0" <?= (old('autolock_enabled', $settings['autolock_enabled'] ?? '1') == '0') ? 'selected' : '' ?>>❌ Desactivado</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="autolock_minutes" class="form-label font-weight-bold small">Tiempo Inactivo</label>
                                                <select id="autolock_minutes" name="autolock_minutes" class="form-control form-control-sm">
                                                    <option value="1" <?= (old('autolock_minutes', $settings['autolock_minutes'] ?? '5') == '1') ? 'selected' : '' ?>>1 Minuto</option>
                                                    <option value="2" <?= (old('autolock_minutes', $settings['autolock_minutes'] ?? '5') == '2') ? 'selected' : '' ?>>2 Minutos</option>
                                                    <option value="5" <?= (old('autolock_minutes', $settings['autolock_minutes'] ?? '5') == '5') ? 'selected' : '' ?>>5 Minutos</option>
                                                    <option value="10" <?= (old('autolock_minutes', $settings['autolock_minutes'] ?? '5') == '10') ? 'selected' : '' ?>>10 Minutos</option>
                                                    <option value="15" <?= (old('autolock_minutes', $settings['autolock_minutes'] ?? '5') == '15') ? 'selected' : '' ?>>15 Minutos</option>
                                                    <option value="30" <?= (old('autolock_minutes', $settings['autolock_minutes'] ?? '5') == '30') ? 'selected' : '' ?>>30 Minutos</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- 6. Contraseñas de Usuarios -->
                                <div class="card bg-light border-0 shadow-sm">
                                    <div class="card-body p-2 px-3 d-flex justify-content-between align-items-center">
                                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center">
                                            6. 👤 Acceso Usuarios
                                            <span class="info-icon ms-2" data-bs-toggle="tooltip" data-bs-placement="top" title="¿Cuándo se solicita? Para ingresar al sistema. Se cambian individualmente desde Usuarios o desde el perfil activo.">ℹ️</span>
                                        </h6>
                                        <a href="<?= base_url('users') ?>" class="btn btn-sm btn-outline-primary fw-bold">ir a Usuarios ➡️</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">💾 Guardar Configuración</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Cambio Seguro de Claves de Autorización -->
<div class="modal fade" id="changeKeyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title mb-0">🔑 Cambiar Clave de Autorización</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-3">Estableciendo nueva clave para: <strong id="modalKeyLabel" class="text-dark"></strong></p>
                <input type="hidden" id="modalKeyName">

                <div class="mb-3">
                    <label for="modalNewPin" class="form-label font-weight-bold">Nueva Clave / PIN *</label>
                    <input type="password" id="modalNewPin" class="form-control form-control-lg text-center fw-bold" placeholder="****" minlength="4" autocomplete="off" style="letter-spacing: 0.2em;">
                </div>

                <div class="mb-3">
                    <label for="modalConfirmPin" class="form-label font-weight-bold">Repetir Nueva Clave / PIN (Confirmación) *</label>
                    <input type="password" id="modalConfirmPin" class="form-control form-control-lg text-center fw-bold" placeholder="****" minlength="4" autocomplete="off" style="letter-spacing: 0.2em;">
                    <div id="modalPinError" class="text-danger small mt-2" style="display: none;"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary fw-bold" id="saveKeyBtn">💾 Guardar Nueva Clave</button>
            </div>
        </div>
    </div>
</div>

<script>
function openChangeKeyModal(keyName, keyLabel) {
    $('#modalKeyName').val(keyName);
    $('#modalKeyLabel').text(keyLabel);
    $('#modalNewPin').val('');
    $('#modalConfirmPin').val('');
    $('#modalPinError').hide();
    $('#changeKeyModal').modal('show');
    setTimeout(() => $('#modalNewPin').focus(), 400);
}

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar Tooltips de Bootstrap
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    $('#saveKeyBtn').on('click', function() {
        var key = $('#modalKeyName').val();
        var newPin = $('#modalNewPin').val();
        var confirmPin = $('#modalConfirmPin').val();

        if (!newPin || newPin.length < 4) {
            $('#modalPinError').text('La clave debe tener al menos 4 caracteres.').show();
            return;
        }

        if (newPin !== confirmPin) {
            $('#modalPinError').text('Las claves no coinciden. Por favor confirme correctamente.').show();
            return;
        }

        $('#modalPinError').hide();

        $.post('<?= base_url("settings/change-key") ?>', {
            key: key,
            new_pin: newPin,
            confirm_pin: confirmPin
        }, function(res) {
            if (res.success) {
                $('#changeKeyModal').modal('hide');
                alert('✅ ' + res.message);
            } else {
                $('#modalPinError').text(res.message || 'Error al guardar la clave').show();
            }
        }, 'json').fail(function() {
            $('#modalPinError').text('Error de conexión con el servidor.').show();
        });
    });
});
</script>

<style>
    .info-icon {
        cursor: pointer;
        font-size: 0.95rem;
        opacity: 0.85;
        transition: transform 0.2s, opacity 0.2s;
    }
    .info-icon:hover {
        opacity: 1;
        transform: scale(1.15);
    }
</style>

<?php echo view('templates/footer'); ?>
