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
                <button type="button" class="btn btn-secondary me-2" onclick="document.getElementById('printModal').style.display='flex'">
                    🖨️ Imprimir
                </button>
                
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
                                            <td>
                                                <?php if (can_update('products')): ?>
                                                    <a href="<?= base_url('products/edit/' . $product['id']) ?>"
                                                        class="btn btn-sm btn-secondary" title="Editar">
                                                        ✏️
                                                    </a>
                                                <?php endif; ?>

                                                <?php if (can_delete('products')): ?>
                                                    <a href="<?= base_url('products/delete/' . $product['id']) ?>"
                                                        class="btn btn-sm btn-danger"
                                                        onclick="return confirm('¿Eliminar este producto?')" title="Eliminar">
                                                        🗑️
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

<!-- Print Modal (custom, sin dependencia de Bootstrap JS) -->
<div id="printModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;" onclick="if(event.target===this) this.style.display='none'">
    <div style="background:#fff; border-radius:12px; padding:0; width:100%; max-width:420px; box-shadow:0 20px 60px rgba(0,0,0,0.3); overflow:hidden;">
        <div style="display:flex; align-items:center; justify-content:space-between; padding:18px 24px; border-bottom:1px solid #e5e7eb;">
            <h5 style="margin:0; font-size:1.1rem; font-weight:600;">🖨️ Opciones de Impresión</h5>
            <button onclick="document.getElementById('printModal').style.display='none'" style="background:none; border:none; font-size:1.4rem; cursor:pointer; color:#6b7280; line-height:1;">&times;</button>
        </div>
        <div style="padding:24px;">
            <p style="margin:0 0 16px; color:#6b7280; font-size:0.9rem;">Seleccioná el formato que querés exportar a PDF:</p>
            <div style="display:flex; flex-direction:column; gap:10px;">
                <a href="<?= base_url('products/print?type=stock') ?>" target="_blank"
                   onclick="document.getElementById('printModal').style.display='none'"
                   style="display:block; padding:14px 18px; border:2px solid #6366f1; border-radius:8px; text-decoration:none; color:#6366f1; font-weight:500; font-size:0.95rem; transition:all 0.2s;"
                   onmouseover="this.style.background='#6366f1';this.style.color='#fff'" onmouseout="this.style.background='';this.style.color='#6366f1'">
                    📦 Listado de Stock
                    <span style="display:block; font-size:0.78rem; font-weight:400; opacity:0.75;">Código, nombre, stock y columna de conteo manual</span>
                </a>
                <a href="<?= base_url('products/print?type=price') ?>" target="_blank"
                   onclick="document.getElementById('printModal').style.display='none'"
                   style="display:block; padding:14px 18px; border:2px solid #10b981; border-radius:8px; text-decoration:none; color:#10b981; font-weight:500; font-size:0.95rem; transition:all 0.2s;"
                   onmouseover="this.style.background='#10b981';this.style.color='#fff'" onmouseout="this.style.background='';this.style.color='#10b981'">
                    💰 Listado de Precios
                    <span style="display:block; font-size:0.78rem; font-weight:400; opacity:0.75;">Código, nombre, precio de venta y precio mínimo</span>
                </a>
                <a href="<?= base_url('products/print?type=general') ?>" target="_blank"
                   onclick="document.getElementById('printModal').style.display='none'"
                   style="display:block; padding:14px 18px; border:2px solid #6b7280; border-radius:8px; text-decoration:none; color:#6b7280; font-weight:500; font-size:0.95rem; transition:all 0.2s;"
                   onmouseover="this.style.background='#6b7280';this.style.color='#fff'" onmouseout="this.style.background='';this.style.color='#6b7280'">
                    📋 Listado General
                    <span style="display:block; font-size:0.78rem; font-weight:400; opacity:0.75;">Vista completa: categoría, costo, precios y stock</span>
                </a>
            </div>
        </div>
        <div style="padding:12px 24px 18px; text-align:right; border-top:1px solid #e5e7eb;">
            <button onclick="document.getElementById('printModal').style.display='none'" style="padding:8px 20px; border:1px solid #d1d5db; border-radius:6px; background:#f9fafb; color:#374151; cursor:pointer; font-size:0.9rem;">Cancelar</button>
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