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
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success">
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger">
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <div style="margin-bottom: 1.5rem;">
                        <label for="warehouseSelect" style="display: block; margin-bottom: 0.5rem; font-weight: 600;">
                            Seleccionar Depósito:
                        </label>
                        <select id="warehouseSelect" class="form-control" style="max-width: 400px;"
                            onchange="loadWarehouseStock(this.value)">
                            <option value="">-- Seleccione un depósito --</option>
                            <?php foreach ($warehouses as $warehouse): ?>
                                <option value="<?= $warehouse['id'] ?>" <?= $selectedWarehouse == $warehouse['id'] ? 'selected' : '' ?>>
                                    <?= esc($warehouse['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <?php if ($selectedWarehouse): ?>
                        <div class="table-responsive">
                            <table id="productStockTable" class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Nombre</th>
                                        <th>Categoría</th>
                                        <th>Costo</th>
                                        <th>Venta</th>
                                        <th>Mínimo</th>
                                        <th>Stock</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($products)): ?>
                                        <?php foreach ($products as $product): ?>
                                            <tr>
                                                <td><code><?= esc($product['code']) ?></code></td>
                                                <td><strong><?= esc($product['name']) ?></strong></td>
                                                <td><?= esc($product['category_name']) ?></td>
                                                <td class="text-muted"><?= formato_moneda($product['cost_price'] ?? 0) ?></td>
                                                <td class="text-primary font-weight-bold">
                                                    <?= formato_moneda($product['price']) ?></td>
                                                <td class="text-muted small">
                                                    <?= formato_moneda($product['min_sale_price'] ?? 0) ?></td>
                                                <td>
                                                    <span
                                                        class="badge <?= $product['stock'] <= 10 ? 'badge-danger' : 'badge-success' ?>">
                                                        <?= $product['stock'] ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center text-muted" style="padding: 2rem;">
                            <p style="font-size: 1.1rem;">📦 Seleccione un depósito para ver el stock de productos</p>
                        </div>
                    <?php endif; ?>
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
    function loadWarehouseStock(warehouseId) {
        if (warehouseId) {
            window.location.href = '" . base_url('product-stock/warehouse/') . "' + warehouseId;
        } else {
            window.location.href = '" . base_url('product-stock') . "';
        }
    }

    $(document).ready(function () {
        " . (($selectedWarehouse && !empty($products)) ? "
            $('#productStockTable').DataTable({
                'order': [[1, 'asc']], // Sort by Name by default
                'pageLength': 25
            });
        " : "") . "
    });
</script>
";
echo view('templates/footer', ['extraJS' => $extraJS, 'scripts' => $scripts]);
?>