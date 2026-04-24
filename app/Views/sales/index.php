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
                                            <td><?= date('d/m/Y', strtotime($sale['date'])) ?></td>
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
                                                    <a href="<?= base_url('sales/annul/' . $sale['id']) ?>"
                                                        class="btn btn-sm btn-danger"
                                                        onclick="return confirm('¿Anular esta venta? Esta acción revertirá el stock.')"
                                                        title="Anular">
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

<?php
$extraJS = [
    'https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js',
    'https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js'
];
$scripts = "
<script>
    var table;
    function openTicket(url) {
        window.open(url, 'Ticket', 'width=400,height=600,scrollbars=yes');
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

        // Búsqueda por observación (columna 7 oculta)
        $('#obsSearch').on('keyup', function () {
            table.column(7).search(this.value).draw();
        });
    });
</script>
";
echo view('templates/footer', ['extraJS' => $extraJS, 'scripts' => $scripts]);
?>