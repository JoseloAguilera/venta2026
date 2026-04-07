<!-- Purchases create view -->
<?php
$extraCSS = ['assets/css/dashboard.css'];
echo view('templates/header', ['title' => $title, 'extraCSS' => $extraCSS]);
?>

<style>
    /* Modal Styles */
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.5);
    }
    .modal-content {
        background-color: #fefefe;
        margin: 10% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
        max-width: 500px;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    .modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #eee;
        padding-bottom: 10px;
        margin-bottom: 20px;
    }
    .modal-close {
        color: #aaa;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }
    .modal-close:hover,
    .modal-close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }
</style>

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
            <div class="card">
                <div class="card-body">
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger">
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success">
                            <?= session()->getFlashdata('success') ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('purchases/store') ?>" method="POST" id="purchaseForm">
                        <?= csrf_field() ?>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">Número de Compra</label>
                                    <input type="text" class="form-control" value="<?= $purchase_number ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="warehouse_id" class="form-label">Depósito de Destino *</label>
                                    <select id="warehouse_id" name="warehouse_id" class="form-control" required>
                                        <option value="">Seleccione un depósito</option>
                                        <?php if (!empty($warehouses)): ?>
                                            <?php foreach ($warehouses as $warehouse): ?>
                                                <option value="<?= $warehouse['id'] ?>"><?= esc($warehouse['name']) ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="supplier_id" class="form-label">Proveedor *</label>
                                    <div class="d-flex gap-2">
                                        <select id="supplier_id" name="supplier_id" class="form-control" required>
                                            <option value="">Seleccione un proveedor</option>
                                            <?php foreach ($suppliers as $supplier): ?>
                                                <option value="<?= $supplier['id'] ?>"><?= esc($supplier['name']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="button" class="btn btn-secondary" id="openSupplierModal" title="Nuevo Proveedor">➕</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="payment_type" class="form-label">Tipo de Pago *</label>
                                    <select id="payment_type" name="payment_type" class="form-control" required>
                                        <option value="cash">Contado</option>
                                        <option value="credit">Crédito</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <h4>Productos</h4>

                        <div id="products-container">
                            <div class="product-row"
                                style="display: grid; grid-template-columns: 2fr 1fr 1fr 1fr auto; gap: 10px; margin-bottom: 10px; align-items: end;">
                                <div class="form-group" style="margin: 0;">
                                    <label class="form-label">Producto</label>
                                    <select class="form-control product-select" required>
                                        <option value="">Seleccione...</option>
                                        <?php foreach ($products as $product): ?>
                                            <option value="<?= $product['id'] ?>" data-price="<?= $product['price'] ?>">
                                                <?= esc($product['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group" style="margin: 0;">
                                    <label class="form-label">Cantidad</label>
                                    <input type="number" class="form-control quantity-input" min="1" value="1" required>
                                </div>
                                <div class="form-group" style="margin: 0;">
                                    <label class="form-label">Precio</label>
                                    <input type="number" class="form-control price-input" step="0.01" required>
                                </div>
                                <div class="form-group" style="margin: 0;">
                                    <label class="form-label">Subtotal</label>
                                    <input type="text" class="form-control subtotal-display" readonly>
                                </div>
                                <button type="button" class="btn btn-sm btn-danger remove-product"
                                    style="margin-top: 24px;">🗑️</button>
                            </div>
                        </div>

                        <button type="button" class="btn btn-secondary" id="addProduct">➕ Agregar Producto</button>

                        <hr>
                        <div style="text-align: right;">
                            <h3>Total: $<span id="totalAmount">0.00</span></h3>
                        </div>

                        <div class="d-flex gap-2" style="margin-top: 20px;">
                            <button type="submit" class="btn btn-primary">💾 Guardar Compra</button>
                            <a href="<?= base_url('purchases') ?>" class="btn btn-secondary">❌ Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Supplier Modal -->
<div id="supplierModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Nuevo Proveedor</h3>
            <span class="modal-close" id="closeSupplierModal">&times;</span>
        </div>
        <div class="modal-body">
            <form id="supplierForm">
                <div class="form-group">
                    <label class="form-label">Nombre *</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">RUC / NIT / Documento</label>
                    <input type="text" name="document" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="phone" class="form-control">
                </div>
                <div id="supplierError" class="alert alert-danger" style="display:none; margin-top: 10px;"></div>
                <div class="d-flex justify-content-end gap-2 mt-3">
                    <button type="button" class="btn btn-secondary" id="cancelSupplier">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Proveedor</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const container = document.getElementById('products-container');
        const addBtn = document.getElementById('addProduct');
        const form = document.getElementById('purchaseForm');

        // Modal Elements
        const supplierModal = document.getElementById('supplierModal');
        const openSupplierBtn = document.getElementById('openSupplierModal');
        const closeSupplierBtn = document.getElementById('closeSupplierModal');
        const cancelSupplierBtn = document.getElementById('cancelSupplier');
        const supplierForm = document.getElementById('supplierForm');
        const supplierSelect = document.getElementById('supplier_id');

        openSupplierBtn.onclick = () => {
            supplierModal.style.display = "block";
            supplierForm.reset();
            document.getElementById('supplierError').style.display = 'none';
        };

        closeSupplierBtn.onclick = () => supplierModal.style.display = "none";
        cancelSupplierBtn.onclick = () => supplierModal.style.display = "none";

        window.onclick = (event) => {
            if (event.target == supplierModal) supplierModal.style.display = "none";
        };

        supplierForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch('<?= base_url('suppliers/ajax-store') ?>', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const option = new Option(data.supplier.name, data.supplier.id);
                        supplierSelect.add(option, undefined);
                        supplierSelect.value = data.supplier.id;
                        
                        // If using select2, update it
                        if ($(supplierSelect).hasClass('select2-hidden-accessible')) {
                            $(supplierSelect).trigger('change.select2');
                        }

                        supplierModal.style.display = "none";
                        alert('Proveedor creado exitosamente');
                    } else {
                        const errorDiv = document.getElementById('supplierError');
                        errorDiv.innerHTML = Object.values(data.errors).join('<br>');
                        errorDiv.style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error de conexión');
                });
        });

        addBtn.addEventListener('click', function () {
            const firstRow = container.querySelector('.product-row');
            const newRow = firstRow.cloneNode(true);

            // Clean up Select2 artifacts from the clone
            $(newRow).find('.select2-container').remove();
            const select = $(newRow).find('select');
            select.removeClass('select2-hidden-accessible');
            select.removeAttr('data-select2-id');
            select.removeAttr('tabindex');
            select.removeAttr('aria-hidden');
            select.find('option').removeAttr('data-select2-id');
            select.val(''); // Reset selection

            // Reset other inputs
            newRow.querySelectorAll('input').forEach(input => {
                input.value = input.type === 'number' ? '1' : '';
            });

            container.appendChild(newRow);

            // Re-initialize Select2
            select.select2({
                language: "es",
                width: '100%'
            });

            attachRowEvents(newRow);
        });

        function attachRowEvents(row) {
            const productSelect = row.querySelector('.product-select');
            const quantityInput = row.querySelector('.quantity-input');
            const priceInput = row.querySelector('.price-input');
            const subtotalDisplay = row.querySelector('.subtotal-display');
            const removeBtn = row.querySelector('.remove-product');

            productSelect.addEventListener('change', function () {
                const option = this.options[this.selectedIndex];
                priceInput.value = option.dataset.price || '';
                calculateSubtotal();
            });

            quantityInput.addEventListener('input', calculateSubtotal);
            priceInput.addEventListener('input', calculateSubtotal);

            removeBtn.addEventListener('click', function () {
                if (container.querySelectorAll('.product-row').length > 1) {
                    row.remove();
                    calculateTotal();
                }
            });

            function calculateSubtotal() {
                const qty = parseFloat(quantityInput.value) || 0;
                const price = parseFloat(priceInput.value) || 0;
                const subtotal = qty * price;
                subtotalDisplay.value = '$' + subtotal.toFixed(2);
                calculateTotal();
            }
        }

        function calculateTotal() {
            let total = 0;
            container.querySelectorAll('.product-row').forEach(row => {
                const qty = parseFloat(row.querySelector('.quantity-input').value) || 0;
                const price = parseFloat(row.querySelector('.price-input').value) || 0;
                total += qty * price;
            });
            document.getElementById('totalAmount').textContent = total.toFixed(2);
        }

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            const products = [];
            container.querySelectorAll('.product-row').forEach(row => {
                const productId = row.querySelector('.product-select').value;
                const quantity = row.querySelector('.quantity-input').value;
                const price = row.querySelector('.price-input').value;

                if (productId && quantity && price) {
                    products.push({
                        product_id: productId,
                        quantity: quantity,
                        price: price
                    });
                }
            });

            if (products.length === 0) {
                alert('Debe agregar al menos un producto');
                return;
            }

            products.forEach((product, index) => {
                Object.keys(product).forEach(key => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = `products[${index}][${key}]`;
                    input.value = product[key];
                    form.appendChild(input);
                });
            });

            form.submit();
        });

        attachRowEvents(container.querySelector('.product-row'));
    });
</script>

<?php echo view('templates/footer'); ?>