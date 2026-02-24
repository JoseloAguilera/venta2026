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
        </div>

        <div class="content-area">
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="<?= base_url('reports/sales') ?>" class="row g-3">
                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12">
                            <label class="form-label">Desde</label>
                            <input type="date" name="date_from" class="form-control" value="<?= $dateFrom ?>">
                        </div>
                        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 col-12">
                            <label class="form-label">Hasta</label>
                            <input type="date" name="date_to" class="form-control" value="<?= $dateTo ?>">
                        </div>
                        <div class="col-xl-3 col-lg-3 col-md-4 col-sm-12 col-12">
                            <label class="form-label">Vendedor</label>
                            <select name="seller_id" class="form-control">
                                <option value="">Todos los vendedores</option>
                                <?php foreach ($sellers as $seller): ?>
                                    <option value="<?= $seller['id'] ?>" <?= $sellerId == $seller['id'] ? 'selected' : '' ?>>
                                        <?= esc($seller['username']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-xl-2 col-lg-3 col-md-12 col-sm-12 col-12">
                            <label class="form-label" style="visibility: hidden;">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100">🔍 Filtrar</button>
                        </div>
                        <div class="col-xl-3 col-lg-12 col-md-12 col-sm-12 col-12 d-flex align-items-end justify-content-xl-end justify-content-center mt-3 mt-xl-0">
                            <h4 class="mb-0">
                                <strong>Total: <span class="text-success">$<?= number_format($totalSales ?? 0, 2) ?></span></strong>
                            </h4>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="reportsTable" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Número</th>
                                    <th>Fecha</th>
                                    <th>Cliente</th>
                                    <th>Vendedor</th>
                                    <th>Tipo</th>
                                    <th>Total</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sales as $sale): ?>
                                    <tr class="<?= $sale['status'] === 'cancelled' ? 'table-danger' : '' ?>">
                                        <td><strong><?= esc($sale['sale_number']) ?></strong></td>
                                        <td><?= date('d/m/Y', strtotime($sale['date'])) ?></td>
                                        <td><?= esc($sale['customer_name']) ?></td>
                                        <td><?= esc($sale['seller_name']) ?></td>
                                        <td>
                                            <span class="badge <?= $sale['payment_type'] === 'cash' ? 'badge-success' : 'badge-warning' ?>">
                                                <?= $sale['payment_type'] === 'cash' ? 'Contado' : 'Crédito' ?>
                                            </span>
                                        </td>
                                        <td>$<?= number_format($sale['total'], 2) ?></td>
                                        <td>
                                            <?php
                                            $badges = [
                                                'paid' => 'badge-success',
                                                'partial' => 'badge-warning',
                                                'pending' => 'badge-secondary',
                                                'cancelled' => 'badge-danger'
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
                                    </tr>
                                <?php endforeach; ?>
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
    $(document).ready(function () {
        $('#reportsTable').DataTable({
            'order': [[0, 'desc']],
            'language': {
                'url': '//cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json'
            }
        });
    });
</script>
";
echo view('templates/footer', ['extraJS' => $extraJS, 'scripts' => $scripts]);
?>
