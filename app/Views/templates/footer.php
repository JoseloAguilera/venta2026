<!-- JavaScript -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://npmcdn.com/select2@4.0.13/dist/js/i18n/es.js"></script>

<script>
    $(document).ready(function () {
        // Inicializar Select2 en todos los elementos select
        $('select').select2({
            language: "es",
            width: '100%' // Ajustar al ancho del contenedor
        });
    });
</script>
<script src="<?= base_url('assets/js/main.js') ?>"></script>
<?php if (isset($extraJS)): ?>
    <?php foreach ($extraJS as $js): ?>
        <?php if (strpos($js, 'http') === 0): ?>
            <script src="<?= $js ?>"></script>
        <?php else: ?>
            <script src="<?= base_url($js) ?>"></script>
        <?php endif; ?>
    <?php endforeach; ?>
    <script>
                // Configuración global para DataTables en español
                if ($.fn.dataTable) {
                    $.extend(true, $.fn.dataTable.defaults, {
                        language: {
                            "decimal": "",
                            "emptyTable": "No hay información",
                            "info": "Mostrando _START_ a _END_ de _TOTAL_ Entradas",
                            "infoEmpty": "Mostrando 0 to 0 of 0 Entradas",
                            "infoFiltered": "(Filtrado de _MAX_ total entradas)",
                            "infoPostFix": "",
                            "thousands": ",",
                            "lengthMenu": "Mostrar _MENU_ Entradas",
                            "loadingRecords": "Cargando...",
                            "processing": "Procesando...",
                            "search": "Buscar:",
                            "zeroRecords": "Sin resultados encontrados",
                            "paginate": {
                                "first": "Primero",
                                "last": "Ultimo",
                                "next": "›",
                                "previous": "‹"
                            }
                        }
                    });
                }
    </script>
<?php endif; ?>

<?php
$settingsModel = new \App\Models\SettingsModel();
$autolockEnabled = $settingsModel->getValue('autolock_enabled', '1');
$autolockMinutes = (int)$settingsModel->getValue('autolock_minutes', '5');
if ($autolockMinutes < 1) $autolockMinutes = 5;
?>
<?php if (session()->get('isLoggedIn') && $autolockEnabled == '1'): ?>
<!-- Modal de Bloqueo Automático por Inactividad (Auto-Lock Screen) -->
<div id="autoLockOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(17, 24, 39, 0.95); backdrop-filter: blur(12px); z-index: 99999; align-items: center; justify-content: center; color: #ffffff;">
    <div style="background: rgba(31, 41, 55, 0.9); border: 1px solid rgba(255, 255, 255, 0.15); border-radius: 16px; padding: 2.5rem; width: 90%; max-width: 420px; text-align: center; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5);">
        <div style="font-size: 3rem; margin-bottom: 0.5rem;">🔒</div>
        <h3 style="font-weight: 700; margin-bottom: 0.25rem;">Sesión Bloqueada</h3>
        <p style="color: #9ca3af; font-size: 0.9rem; margin-bottom: 1.5rem;">Pantalla bloqueada por inactividad de seguridad.</p>

        <form id="autoLockForm">
            <div style="margin-bottom: 1.25rem; text-align: left;">
                <label style="font-size: 0.82rem; font-weight: 600; color: #d1d5db; margin-bottom: 0.4rem; display: block;">Contraseña de <?= esc(session()->get('username')) ?></label>
                <input type="password" id="autoLockPassword" class="form-control" placeholder="Ingrese su contraseña..." required style="background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.2); color: #fff;">
                <div id="autoLockError" style="color: #ef4444; font-size: 0.8rem; margin-top: 0.4rem; display: none;"></div>
            </div>
            <button type="submit" class="btn btn-primary w-100 fw-bold" style="padding: 0.65rem; border-radius: 8px;">
                🔓 Desbloquear Pantalla
            </button>
        </form>
    </div>
</div>

<script>
(function() {
    let idleTimer;
    const idleTimeLimit = <?= $autolockMinutes ?> * 60 * 1000; // <?= $autolockMinutes ?> minuto(s) de inactividad
    const lockOverlay = document.getElementById('autoLockOverlay');
    const lockForm = document.getElementById('autoLockForm');
    const lockPassword = document.getElementById('autoLockPassword');
    const lockError = document.getElementById('autoLockError');

    function resetIdleTimer() {
        if (lockOverlay.style.display === 'flex') return;
        clearTimeout(idleTimer);
        idleTimer = setTimeout(lockScreen, idleTimeLimit);
    }

    function lockScreen() {
        lockOverlay.style.display = 'flex';
        lockPassword.value = '';
        lockError.style.display = 'none';
        lockPassword.focus();
    }

    // Eventos de actividad
    ['mousemove', 'keydown', 'click', 'scroll'].forEach(evt => {
        window.addEventListener(evt, resetIdleTimer, true);
    });
    resetIdleTimer();

    // Enviar solicitud de desbloqueo
    if (lockForm) {
        lockForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const password = lockPassword.value;
            lockError.style.display = 'none';

            $.post('<?= base_url("auth/unlock-screen") ?>', { password: password }, function(res) {
                if (res.success) {
                    lockOverlay.style.display = 'none';
                    resetIdleTimer();
                } else {
                    lockError.textContent = res.message || 'Contraseña incorrecta';
                    lockError.style.display = 'block';
                }
            }, 'json').fail(function() {
                lockError.textContent = 'Error de conexión con el servidor.';
                lockError.style.display = 'block';
            });
        });
    }
})();
</script>
<?php endif; ?>

<?php if (isset($scripts)): ?>
    <?= $scripts ?>
<?php endif; ?>
</body>

</html>