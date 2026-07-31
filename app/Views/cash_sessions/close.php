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
                    ⬅️ Cancelar
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

            <div class="row">
                <!-- Columna Izquierda: Resumen del Sistema -->
                <div class="col-lg-6 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header bg-dark text-white">
                            <h5 class="card-title mb-0">📊 Resumen Calculado por Sistema (Turno #<?= $session['id'] ?>)</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered align-middle">
                                <tbody>
                                    <tr>
                                        <td><strong>Caja / Cuenta:</strong></td>
                                        <td class="text-end"><?= esc($session['account_name']) ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Cajero Responsable:</strong></td>
                                        <td class="text-end"><?= esc($session['user_name']) ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Fecha/Hora Apertura:</strong></td>
                                        <td class="text-end"><?= date('d/m/Y H:i', strtotime($session['opening_time'])) ?></td>
                                    </tr>
                                    <tr class="table-light">
                                        <td><strong>(+) Monto de Apertura:</strong></td>
                                        <td class="text-end font-weight-bold">$<?= number_format($session['opening_amount'], 2) ?></td>
                                    </tr>
                                    <tr>
                                        <td class="ps-4">(+) Ingresos por Ventas:</td>
                                        <td class="text-end text-success">+$<?= number_format($summary['sales_income'], 2) ?></td>
                                    </tr>
                                    <tr>
                                        <td class="ps-4">(+) Ingresos por Cobranzas de Clientes:</td>
                                        <td class="text-end text-success">+$<?= number_format($summary['collection_income'], 2) ?></td>
                                    </tr>
                                    <tr>
                                        <td class="ps-4">(+) Ingresos Manuales / Varios:</td>
                                        <td class="text-end text-success">+$<?= number_format($summary['manual_income'], 2) ?></td>
                                    </tr>
                                    <tr>
                                        <td class="ps-4">(-) Egresos por Gastos:</td>
                                        <td class="text-end text-danger">-$<?= number_format($summary['expense_out'], 2) ?></td>
                                    </tr>
                                    <tr>
                                        <td class="ps-4">(-) Pagos a Proveedores / Salidas:</td>
                                        <td class="text-end text-danger">-$<?= number_format($summary['payment_out'] + $summary['manual_expense'], 2) ?></td>
                                    </tr>
                                    <tr class="table-primary fs-5 fw-bold">
                                        <td>(=) Efectivo Esperado en Caja:</td>
                                        <td class="text-end" id="expectedAmountVal">$<?= number_format($expected_amount, 2) ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Columna Derecha: Formulario de Conteo y Arqueo -->
                <div class="col-lg-6 mb-4">
                    <div class="card h-100 shadow-sm border-warning">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="card-title mb-0">🔒 Arqueo Físico y Cierre de Caja</h5>
                        </div>
                        <div class="card-body d-flex flex-column justify-content-between">
                            <form action="<?= base_url('cash-sessions/store-close/' . $session['id']) ?>" method="post" id="closeForm">
                                <?= csrf_field() ?>

                                <p class="text-muted">
                                    Por favor realice el recuento del dinero físico que se encuentra en la caja registradora e ingrese el total a continuación.
                                </p>

                                <div class="mb-4">
                                    <label for="closing_amount" class="form-label font-weight-bold fs-5">Monto de Cierre (Efectivo Físico Contado) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" name="closing_amount" id="closing_amount" 
                                           class="form-control form-control-lg text-primary font-weight-bold" 
                                           style="font-size: 1.5rem;"
                                           value="<?= old('closing_amount', $expected_amount) ?>" required min="0" placeholder="0.00">
                                </div>

                                <!-- Box de Resultado en vivo -->
                                <div class="card bg-light mb-4 text-center p-3" id="discrepancyBox">
                                    <span class="text-muted d-block mb-1">Diferencia de Arqueo Calculada:</span>
                                    <h3 class="mb-0 fw-bold" id="discrepancyText">$0.00</h3>
                                    <small id="discrepancyStatus" class="fw-bold mt-1 text-success">Cuadre Exacto</small>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-warning btn-lg fw-bold" onclick="return confirm('¿Confirmar el cierre definitivo de esta caja?')">
                                        🔒 Confirmar y Cerrar Caja
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

<?php
$expectedVal = $expected_amount;
$scripts = "
<script>
    const expectedAmount = {$expectedVal};
    const closingInput = document.getElementById('closing_amount');
    const discrepancyText = document.getElementById('discrepancyText');
    const discrepancyStatus = document.getElementById('discrepancyStatus');
    const discrepancyBox = document.getElementById('discrepancyBox');

    function calculateDiscrepancy() {
        const closingVal = parseFloat(closingInput.value) || 0;
        const diff = closingVal - expectedAmount;

        discrepancyText.innerText = (diff >= 0 ? '+$' : '-$') + Math.abs(diff).toFixed(2);

        if (Math.abs(diff) < 0.01) {
            discrepancyText.className = 'mb-0 fw-bold text-success';
            discrepancyStatus.className = 'fw-bold mt-1 text-success';
            discrepancyStatus.innerText = 'Exacto (Sin diferencia)';
            discrepancyBox.className = 'card bg-light mb-4 text-center p-3 border-success';
        } else if (diff > 0) {
            discrepancyText.className = 'mb-0 fw-bold text-info';
            discrepancyStatus.className = 'fw-bold mt-1 text-info';
            discrepancyStatus.innerText = 'Sobrante en Caja';
            discrepancyBox.className = 'card bg-light mb-4 text-center p-3 border-info';
        } else {
            discrepancyText.className = 'mb-0 fw-bold text-danger';
            discrepancyStatus.className = 'fw-bold mt-1 text-danger';
            discrepancyStatus.innerText = 'Faltante en Caja';
            discrepancyBox.className = 'card bg-light mb-4 text-center p-3 border-danger';
        }
    }

    closingInput.addEventListener('input', calculateDiscrepancy);
    calculateDiscrepancy();
</script>
";
echo view('templates/footer', ['scripts' => $scripts]);
?>
