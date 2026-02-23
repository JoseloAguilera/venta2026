<?php
$extraCSS = ['assets/css/dashboard.css'];
echo view('templates/header', ['title' => $title, 'extraCSS' => $extraCSS]);
helper('permission');
?>

<div class="dashboard-wrapper">
    <?= view('templates/sidebar') ?>

    <div class="main-content">
        <div class="topbar">
            <div class="topbar-title">
                <button class="menu-toggle" id="menuToggle">☰</button>
                <h2>
                    <?= $title ?>
                </h2>
            </div>
            <div class="topbar-actions">
                <?php if (can_update('products')): ?>
                    <a href="<?= base_url('products/edit/' . $product['id']) ?>" class="btn btn-warning">
                        ✏️ Editar
                    </a>
                <?php endif; ?>
                <a href="javascript:window.close()" class="btn btn-secondary">
                    ❌ Cerrar
                </a>
            </div>
        </div>

        <div class="content-area">
            <div class="card">
                <div class="card-body">
                    <div class="row" style="display: flex; gap: 20px;">
                        <!-- Image Column -->
                        <div class="col-md-4" style="flex: 1; max-width: 300px;">
                            <?php
                            $imagePath = 'uploads/products/' . $product['id'] . '.jpg';
                            if (file_exists(FCPATH . $imagePath)):
                                ?>
                                <img src="<?= base_url($imagePath) ?>" alt="<?= esc($product['name']) ?>"
                                    class="img-fluid rounded" style="width: 100%; height: auto; object-fit: cover;">
                            <?php else: ?>
                                <div class="bg-light d-flex align-items-center justify-content-center rounded"
                                    style="width: 100%; height: 300px; background-color: #f8f9fa;">
                                    <span class="text-muted">Sin imagen</span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Details Column -->
                        <div class="col-md-8" style="flex: 2;">
                            <h3 class="mb-3">
                                <?= esc($product['name']) ?>
                            </h3>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <p><strong>Código:</strong> <span class="badge badge-primary">
                                            <?= esc($product['code']) ?>
                                        </span></p>
                                    <p><strong>Categoría:</strong>
                                        <?= esc($product['category_name']) ?>
                                    </p>
                                    <p><strong>Stock Actual:</strong> <span
                                            class="badge <?= $product['stock'] <= 5 ? 'badge-danger' : 'badge-success' ?>">
                                            <?= $product['stock'] ?>
                                        </span></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Precio de Venta:</strong> <span class="text-success font-weight-bold">$
                                            <?= number_format($product['price'], 0, ',', '.') ?>
                                        </span></p>
                                    <p><strong>Precio Mínimo:</strong> $
                                        <?= number_format($product['min_sale_price'], 0, ',', '.') ?>
                                    </p>
                                    <p><strong>Costo:</strong> $
                                        <?= number_format($product['cost_price'], 0, ',', '.') ?>
                                    </p>
                                </div>
                            </div>

                            <hr>

                            <?php if (!empty($product['description'])): ?>
                                <div class="mb-3">
                                    <h5>Descripción</h5>
                                    <p>
                                        <?= nl2br(esc($product['description'])) ?>
                                    </p>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($product['imei1']) || !empty($product['imei2'])): ?>
                                <div class="mb-3">
                                    <h5>Números de Serie / IMEI</h5>
                                    <ul class="list-unstyled">
                                        <?php if (!empty($product['imei1'])): ?>
                                            <li><strong>IMEI 1:</strong>
                                                <?= esc($product['imei1']) ?>
                                            </li>
                                        <?php endif; ?>
                                        <?php if (!empty($product['imei2'])): ?>
                                            <li><strong>IMEI 2:</strong>
                                                <?= esc($product['imei2']) ?>
                                            </li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <div class="mt-4 text-muted small">
                                <p>Creado:
                                    <?= date('d/m/Y H:i', strtotime($product['created_at'])) ?>
                                </p>
                                <p>Última actualización:
                                    <?= date('d/m/Y H:i', strtotime($product['updated_at'])) ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php echo view('templates/footer'); ?>