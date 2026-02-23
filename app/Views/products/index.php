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
                <?php if (can_insert('products')): ?>
                    <a href="<?= base_url('products/create') ?>" class="btn btn-primary">
                        ➕ Nuevo Producto
                    </a>
                <?php else: ?>
                    <span class="badge badge-warning">Sin permiso Insertar (<?= session()->get('role_name') ?>)</span>
                <?php endif; ?>
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
                    <div class="table-responsive">
                        <table id="productsTable" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Nombre</th>
                                    <th>Categoría</th>
                                    <th>IMEI 1</th>
                                    <th>IMEI 2</th>
                                    <th>Costo</th>
                                    <th>Venta</th>
                                    <th>Mínimo</th>
                                    <th>Stock</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($products)): ?>
                                    <!-- Empty state handled by DataTables or kept here -->
                                <?php else: ?>
                                    <?php foreach ($products as $product): ?>
                                        <tr>
                                            <td><code><?= esc($product['code']) ?></code></td>
                                            <td><strong><?= esc($product['name']) ?></strong></td>
                                            <td><?= esc($product['category_name']) ?></td>
                                            <td class="small"><?= esc($product['imei1'] ?? '-') ?></td>
                                            <td class="small"><?= esc($product['imei2'] ?? '-') ?></td>
                                            <td class="text-muted">$<?= number_format($product['cost_price'] ?? 0, 2) ?></td>
                                            <td class="text-primary font-weight-bold">
                                                $<?= number_format($product['price'], 2) ?></td>
                                            <td class="text-muted small">
                                                $<?= number_format($product['min_sale_price'] ?? 0, 2) ?></td>
                                            <td>
                                                <span
                                                    class="badge <?= $product['stock'] <= 10 ? 'badge-danger' : 'badge-success' ?>">
                                                    <?= $product['stock'] ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if (can_update('products')): ?>
                                                    <a href="<?= base_url('products/edit/' . $product['id']) ?>"
                                                        class="btn btn-sm btn-secondary">
                                                        ✏️ Editar
                                                    </a>
                                                <?php endif; ?>

                                                <?php if (can_delete('products')): ?>
                                                    <a href="<?= base_url('products/delete/' . $product['id']) ?>"
                                                        class="btn btn-sm btn-danger"
                                                        onclick="return confirm('¿Eliminar este producto?')">
                                                        🗑️ Eliminar
                                                    </a>
                                                <?php endif; ?>
                                            </td>
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
    $(document).ready(function () {
        $('#productsTable').DataTable({
            'order': [[1, 'asc']],
            'pageLength': 25
        });
    });
</script>
";
echo view('templates/footer', ['extraJS' => $extraJS, 'scripts' => $scripts]);
?>