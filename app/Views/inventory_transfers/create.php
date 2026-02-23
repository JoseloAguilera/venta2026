<?php
$extraCSS = ['assets/css/dashboard.css', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'];
echo view('templates/header', ['title' => $title, 'extraCSS' => $extraCSS]);
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
        </div>

        <div class="content-area">
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger">
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('inventory-transfers/store') ?>" method="POST" id="transferForm">
                <?= csrf_field() ?>

                <div class="row">
                    <!-- Header Information -->
                    <div class="col-md-12 mb-4">
                        <div class="card">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Datos de la Transferencia</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="source_warehouse_id" class="form-label">Depósito Origen *</label>
                                        <select id="source_warehouse_id" name="source_warehouse_id" class="form-control"
                                            required>
                                            <option value="">Seleccione origen...</option>
                                            <?php foreach ($warehouses as $w): ?>
                                                <option value="<?= $w['id'] ?>">
                                                    <?= esc($w['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="destination_warehouse_id" class="form-label">Depósito Destino
                                            *</label>
                                        <select id="destination_warehouse_id" name="destination_warehouse_id"
                                            class="form-control" required>
                                            <option value="">Seleccione destino...</option>
                                            <?php foreach ($warehouses as $w): ?>
                                                <option value="<?= $w['id'] ?>">
                                                    <?= esc($w['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-12">
                                        <label for="notes" class="form-label">Notas / Observaciones</label>
                                        <textarea name="notes" id="notes" class="form-control" rows="2"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Products -->
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Productos a Transferir</h5>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3 align-items-end">
                                    <div class="col-md-8">
                                        <label class="form-label">Buscar Producto (en depósito origen)</label>
                                        <select id="product_search" class="form-control" disabled>
                                            <option value="">Seleccione un depósito de origen primero...</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">Cantidad</label>
                                        <input type="number" id="add_quantity" class="form-control" min="1" value="1">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" id="btn_add_product" class="btn btn-success w-100"
                                            disabled>
                                            <i class="fas fa-plus"></i> Agregar
                                        </button>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-bordered" id="itemsTable">
                                        <thead>
                                            <tr>
                                                <th>Código</th>
                                                <th>Producto</th>
                                                <th width="150">Stock Origen</th>
                                                <th width="150">Cantidad</th>
                                                <th width="100">Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr id="noItemsRow">
                                                <td colspan="5" class="text-center text-muted">No hay productos
                                                    agregados</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer bg-white text-end">
                                <a href="<?= base_url('inventory-transfers') ?>" class="btn btn-secondary">Cancelar</a>
                                <button type="submit" class="btn btn-primary" id="btnSubmit" disabled>
                                    💾 Confirmar Transferencia
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function () {
        let transferItems = [];

        // Initialize Select2
        $('#product_search').select2({
            placeholder: "Seleccione un producto...",
            allowClear: true
        });

        // Warehouse Change
        $('#source_warehouse_id').change(function () {
            const warehouseId = $(this).val();
            if (warehouseId) {
                $('#product_search').prop('disabled', false);
                // Verify destination is different
                checkWarehouses();
                // Reset items if warehouse changes? Ideally yes or validate stock again.
                // For simplicity, we'll warn and clear.
                if (transferItems.length > 0) {
                    Swal.fire({
                        title: 'Cambio de depósito',
                        text: 'Al cambiar el depósito de origen se limpiará la lista de productos.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, cambiar',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            clearItems();
                        } else {
                            // Revert selection (complex without old value tracking, skipping for now)
                        }
                    });
                }
            } else {
                $('#product_search').prop('disabled', true);
            }
        });

        $('#destination_warehouse_id').change(checkWarehouses);

        function checkWarehouses() {
            const source = $('#source_warehouse_id').val();
            const dest = $('#destination_warehouse_id').val();

            if (source && dest && source == dest) {
                Swal.fire('Error', 'El depósito de origen y destino no pueden ser el mismo.', 'error');
                $('#destination_warehouse_id').val('');
            }
        }

        // Populate Product Search (Client-side filtering of all products for now, or AJAX?)
        // Using the products array passed from controller
        const allProducts = <?= json_encode($products) ?>;

        function populateProducts() {
            // Just keep standard select options for now, Select2 handles search
            let options = '<option value="">Seleccione un producto...</option>';
            allProducts.forEach(p => {
                options += `<option value="${p.id}" data-code="${p.code}" data-name="${p.name}">${p.code} - ${p.name}</option>`;
            });
            $('#product_search').html(options);
        }
        populateProducts();

        // Add Product
        $('#btn_add_product').click(function () {
            const productId = $('#product_search').val();
            const warehouseId = $('#source_warehouse_id').val();
            const quantity = parseInt($('#add_quantity').val());

            if (!productId || !warehouseId || quantity <= 0) {
                Swal.fire('Error', 'Seleccione un depósito, un producto y una cantidad válida.', 'warning');
                return;
            }

            // Check if already added
            if (transferItems.some(item => item.id == productId)) {
                Swal.fire('Error', 'Este producto ya está en la lista.', 'warning');
                return;
            }

            // AJAX Check Stock
            $.get('<?= base_url('inventory-transfers/getStock') ?>', {
                product_id: productId,
                warehouse_id: warehouseId
            }, function (response) {
                if (response.error) {
                    Swal.fire('Error', 'Error al consultar stock.', 'error');
                    return;
                }

                const currentStock = response.stock;

                if (quantity > currentStock) {
                    Swal.fire('Stock Insuficiente', `Solo hay ${currentStock} unidades disponibles en el depósito de origen.`, 'error');
                    return;
                }

                const productData = allProducts.find(p => p.id == productId);

                addItemToTable(productData, quantity, currentStock);
            });
        });

        function addItemToTable(product, quantity, currentStock) {
            transferItems.push({
                id: product.id,
                quantity: quantity
            });

            $('#noItemsRow').hide();

            const row = `
            <tr data-id="${product.id}">
                <td>${product.code}</td>
                <td>${product.name}</td>
                <td>${currentStock}</td>
                <td>
                    <input type="hidden" name="products[]" value="${product.id}">
                    <input type="number" name="quantities[]" class="form-control form-control-sm" value="${quantity}" readonly>
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm btn-remove">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;

            $('#itemsTable tbody').append(row);
            $('#product_search').val('').trigger('change');
            $('#add_quantity').val(1);
            $('#btn_add_product').prop('disabled', true); // Re-disable until selection
            $('#btnSubmit').prop('disabled', false);

            // Bind remove event
            $('.btn-remove').last().click(function () {
                const tr = $(this).closest('tr');
                const id = tr.data('id');
                transferItems = transferItems.filter(item => item.id != id);
                tr.remove();

                if (transferItems.length === 0) {
                    $('#noItemsRow').show();
                    $('#btnSubmit').prop('disabled', true);
                }
            });
        }

        $('#product_search').on('select2:select', function (e) {
            $('#btn_add_product').prop('disabled', false);
        });

        function clearItems() {
            transferItems = [];
            $('#itemsTable tbody tr:not(#noItemsRow)').remove();
            $('#noItemsRow').show();
            $('#btnSubmit').prop('disabled', true);
        }
    });
</script>

<?php echo view('templates/footer'); ?>