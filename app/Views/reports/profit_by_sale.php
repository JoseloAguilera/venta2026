<?php
$extraCSS = [
    'assets/css/dashboard.css',
    'https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css'
];
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
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="<?= base_url('reports/profit-by-sale') ?>" class="row g-3">
                        <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                            <label class="form-label">Desde</label>
                            <input type="date" name="date_from" class="form-control" value="<?= $dateFrom ?>">
                        </div>
                        <div class="col-lg-3 col-md-4 col-sm-6 col-12">
                            <label class="form-label">Hasta</label>
                            <input type="date" name="date_to" class="form-control" value="<?= $dateTo ?>">
                        </div>
                        <div class="col-lg-2 col-md-4 col-sm-12 col-12">
                            <label class="form-label" style="visibility: hidden;">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100">🔍 Filtrar</button>
                        </div>
                        <div class="col-lg-4 col-md-12 col-sm-12 col-12 d-flex align-items-end justify-content-lg-end justify-content-center mt-3 mt-lg-0">
                            <h4 class="mb-0">
                                <strong>G. Total: <span class="text-success">$<?= number_format($totalProfit ?? 0, 2) ?></span></strong>
                            </h4>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="profitTable" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Número</th>
                                    <th>Fecha</th>
                                    <th>Cliente</th>
                                    <th>Total Venta</th>
                                    <th>Costo Total</th>
                                    <th>Ganancia</th>
                                    <th>% Margen</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $grandTotal = 0;
                                $grandCost = 0;
                                $grandProfit = 0;
                                foreach ($sales as $sale): 
                                    $profit = $sale['total'] - $sale['total_cost'];
                                    $margin = $sale['total'] > 0 ? ($profit / $sale['total']) * 100 : 0;
                                    
                                    $grandTotal += $sale['total'];
                                    $grandCost += $sale['total_cost'];
                                    $grandProfit += $profit;
                                ?>
                                    <tr>
                                        <td><strong><?= esc($sale['sale_number']) ?></strong></td>
                                        <td><?= date('d/m/Y', strtotime($sale['date'])) ?></td>
                                        <td><?= esc($sale['customer_name']) ?></td>
                                        <td>$<?= number_format($sale['total'], 2) ?></td>
                                        <td>$<?= number_format($sale['total_cost'], 2) ?></td>
                                        <td class="<?= $profit >= 0 ? 'text-success' : 'text-danger' ?>">
                                            <strong>$<?= number_format($profit, 2) ?></strong>
                                        </td>
                                        <td><?= number_format($margin, 2) ?>%</td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="table-dark">
                                <tr>
                                    <td colspan="3" class="text-end"><strong>TOTALES:</strong></td>
                                    <td><strong>$<?= number_format($grandTotal, 2) ?></strong></td>
                                    <td><strong>$<?= number_format($grandCost, 2) ?></strong></td>
                                    <td><strong>$<?= number_format($grandProfit, 2) ?></strong></td>
                                    <td><strong><?= $grandTotal > 0 ? number_format(($grandProfit / $grandTotal) * 100, 2) : 0 ?>%</strong></td>
                                </tr>
                            </tfoot>
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
        $('#profitTable').DataTable({
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
