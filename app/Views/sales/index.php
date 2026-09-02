<?php
$extraCSS = [
    'assets/css/dashboard.css',
    'https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css'
];
echo view('templates/header', ['title' => $title, 'extraCSS' => $extraCSS]);
helper('permission');
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
                <div class="topbar-actions">
                    <?php if (can_insert('sales')): ?>
                        <a href="<?= base_url('sales/create') ?>" class="btn btn-primary">
                            ➕ Nueva Venta
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="content-area">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success">
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">

                    <!-- Filtro por observación -->
                    <div class="mb-3 d-flex align-items-center gap-2" style="max-width: 500px;">
                        <span style="white-space:nowrap; font-weight:600;">🔍 Buscar por observación:</span>
                        <input type="text" id="obsSearch" class="form-control form-control-sm"
                            placeholder="Ej: IMEI, número de serie..." autocomplete="off">
                        <button class="btn btn-sm btn-secondary" onclick="document.getElementById('obsSearch').value=''; table.column(7).search('').draw();">✕</button>
                    </div>

                    <div class="table-responsive">
                        <table id="salesTable" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Número</th>
                                    <th>Fecha</th>
                                    <th>Cliente</th>
                                    <th>Tipo</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                    <th style="display:none;">Observaciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($sales)): ?>
                                    <?php foreach ($sales as $sale): ?>
                                        <tr class="<?= $sale['status'] === 'cancelled' ? 'table-danger' : '' ?>">
                                            <td><strong><?= esc($sale['sale_number']) ?></strong></td>
                                            <?php $saleTime = strtotime(str_replace('/', '-', $sale['date'])) ?: strtotime($sale['date']); ?>
                                            <td data-order="<?= $saleTime ?>"><?= date('d/m/Y', $saleTime) ?></td>
                                            <td><?= esc($sale['customer_name']) ?></td>
                                            <td>
                                                <span
                                                    class="badge <?= $sale['payment_type'] === 'cash' ? 'badge-success' : 'badge-warning' ?>">
                                                    <?= $sale['payment_type'] === 'cash' ? 'Contado' : 'Crédito' ?>
                                                </span>
                                            </td>
                                            <td><?= formato_moneda($sale['total']) ?></td>
                                            <td>
                                                <?php
                                                $badges = [
                                                    'paid' => 'badge-success',
                                                    'partial' => 'badge-warning',
                                                    'pending' => 'badge-secondary', // Changed pending to gray to reserve red for alert/cancelled
                                                    'cancelled' => 'badge-danger'   // User requested red for cancelled
                                                ];
                                                $labels = [
                                                    'paid' => 'Pagado',
                                                    'partial' => 'Parcial',
                                                    'pending' => 'Pendiente',
                                                    'cancelled' => 'Anulada'
                                                ];
                                                ?>
                                                <span class="badge <?= $badges[$sale['status']] ?? 'badge-secondary' ?>">
                                                    <?= $labels[$sale['status']] ?? $sale['status'] ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="<?= base_url('sales/view/' . $sale['id']) ?>"
                                                    class="btn btn-sm btn-primary" title="Ver">
                                                    👁️
                                                </a>
                                                <a href="javascript:void(0)"
                                                    onclick="openTicket('<?= base_url('sales/ticket/' . $sale['id']) ?>')"
                                                    class="btn btn-sm btn-info" title="Imprimir Ticket">
                                                    🖨️
                                                </a>
                                                <?php if (can_delete('sales') && $sale['status'] !== 'cancelled'): ?>
                                                    <a href="javascript:void(0)"
                                                        onclick="requestAnnulWithPin('<?= base_url('sales/annul/' . $sale['id']) ?>', '<?= esc($sale['sale_number']) ?>')"
                                                        class="btn btn-sm btn-danger"
                                                        title="Anular con PIN">
                                                        🚫
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                            <!-- Columna oculta: observaciones para filtro -->
                                            <td style="display:none;"><?= esc($sale['observations'] ?? '') ?></td>
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

<!-- Modal para PIN de Supervisor -->
<div class="modal fade" id="supervisorPinModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title mb-0">🔐 Autorización de Supervisor Requerida</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="annulModalMsg">Para anular esta venta, ingrese el PIN de un supervisor o administrador.</p>
                <div class="mb-3">
                    <label for="supervisorPinInput" class="form-label font-weight-bold">PIN de Supervisor</label>
                    <input type="password" id="supervisorPinInput" class="form-control form-control-lg text-center fw-bold" placeholder="****" autocomplete="off" style="letter-spacing: 0.3em; font-size: 1.5rem;">
                    <div id="pinErrorMsg" class="text-danger small mt-2" style="display: none;"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger fw-bold" id="confirmAnnulBtn">Confirmar Anulación</button>
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
    var table;
    var targetAnnulUrl = '';

    function openTicket(url) {
        window.open(url, 'Ticket', 'width=400,height=600,scrollbars=yes');
    }

    function requestAnnulWithPin(url, saleNumber) {
        targetAnnulUrl = url;
        $('#annulModalMsg').html('Se requiere la autorización de un supervisor para anular la venta <strong>#' + saleNumber + '</strong>.');
        $('#supervisorPinInput').val('');
        $('#pinErrorMsg').hide();
        $('#supervisorPinModal').modal('show');
        setTimeout(() => $('#supervisorPinInput').focus(), 400);
    }

    $(document).ready(function () {
        table = $('#salesTable').DataTable({
            'order': [[0, 'desc']],
            'columnDefs': [
                { targets: 7, visible: false, searchable: true }
            ],
            language: {
                search: 'Buscar en tabla:',
                lengthMenu: 'Mostrar _MENU_ registros',
                info: 'Mostrando _START_ a _END_ de _TOTAL_ ventas',
                paginate: { previous: 'Anterior', next: 'Siguiente' },
                zeroRecords: 'No se encontraron ventas'
            }
        });

        $('#obsSearch').on('keyup', function () {
            table.column(7).search(this.value).draw();
        });

        $('#confirmAnnulBtn').on('click', function() {
            var pin = $('#supervisorPinInput').val();
            if (!pin) {
                $('#pinErrorMsg').text('Debe ingresar el PIN de supervisor').show();
                return;
            }
            $('#pinErrorMsg').hide();

            $.post('" . base_url('auth/verify-supervisor-pin') . "', { pin: pin }, function(res) {
                if (res.success) {
                    $('#supervisorPinModal').modal('hide');
                    window.location.href = targetAnnulUrl;
                } else {
                    $('#pinErrorMsg').text(res.message || 'PIN de supervisor incorrecto').show();
                }
            }, 'json').fail(function() {
                $('#pinErrorMsg').text('Error al verificar PIN').show();
            });
        });
    });
</script>
";
echo view('templates/footer', ['extraJS' => $extraJS, 'scripts' => $scripts]);
?>