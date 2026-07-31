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
        max-width: 800px;
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

    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 15px;
        max-height: 400px;
        overflow-y: auto;
    }

    .product-card {
        border: 1px solid #ddd;
        padding: 10px;
        border-radius: 5px;
        cursor: pointer;
        transition: all 0.2s;
        background: white;
    }

    .product-card:hover {
        border-color: var(--primary-color);
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .product-card.low-stock {
        border-left: 3px solid #dc3545;
    }

    .search-box {
        width: 100%;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #ddd;
        border-radius: 5px;
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
            <?php if (empty($activeSession)): ?>
                <div class="alert alert-warning d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <strong>⚠️ Atención:</strong> No tienes una sesión de caja abierta en este turno.
                    </div>
                    <a href="<?= base_url('cash-sessions/open') ?>" class="btn btn-sm btn-dark">
                        🔓 Abrir Caja Ahora
                    </a>
                </div>
            <?php if (!empty($activeSession)): ?>
                <div class="alert alert-success py-2 mb-3">
                    <small>✔️ Turno de Caja Activo: <strong><?= esc($activeSession['account_name']) ?></strong> (#<?= $activeSession['id'] ?>)</small>
                </div>
            <?php endif; ?>

            <?php if (!empty($cashLimitAlert)): ?>
                <div class="alert alert-warning d-flex justify-content-between align-items-center shadow-sm mb-3" style="border-left: 4px solid #f59e0b;">
                    <div>
                        <strong>⚠️ Alerta de Retiro Recomendado:</strong>
                        La caja acumula actualmente <strong><?= formato_moneda($currentCashBalance) ?></strong> en efectivo.
                        Se recomienda realizar un <strong>Retiro de Caja</strong> a la Caja Fuerte o Banco por seguridad.
                    </div>
                    <a href="<?= base_url('accounts/transfer') ?>" class="btn btn-dark btn-sm fw-bold">
                        💸 Realizar Retiro Ahora
                    </a>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger">
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('sales/store') ?>" method="POST" id="saleForm">
                        <?= csrf_field() ?>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">Número de Venta</label>
                                    <input type="text" class="form-control" value="<?= $sale_number ?>" readonly>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="warehouse_id" class="form-label">Depósito *</label>
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
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="customer_id" class="form-label">Cliente *</label>
                                    <div class="d-flex gap-2">
                                        <select id="customer_id" name="customer_id" class="form-control" required>
                                            <option value="">Seleccione un cliente</option>
                                            <?php foreach ($customers as $customer): ?>
                                                <option value="<?= $customer['id'] ?>"><?= esc($customer['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="button" class="btn btn-secondary" id="openCustomerModal"
                                            title="Nuevo Cliente">➕</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4>Productos</h4>
                            <button type="button" class="btn btn-primary" id="openProductModal" disabled>
                                ➕ Agregar Producto
                            </button>
                        </div>
                        <p class="text-muted small" id="warehouse-hint">Seleccione un depósito primero para agregar
                            productos.</p>

                        <div class="table-responsive">
                            <table class="table table-bordered" id="productsTable">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th width="150">Cantidad</th>
                                        <th width="150">Precio</th>
                                        <th width="200">Observación</th>
                                        <th width="150">Subtotal</th>
                                        <th width="50"></th>
                                    </tr>
                                </thead>
                                <tbody id="products-container">
                                    <!-- Products will be added here -->
                                </tbody>
                            </table>
                        </div>

                        <div id="empty-message" class="text-center text-muted p-4">
                            No hay productos agregados a la venta
                        </div>

                        <!-- Hidden inputs for auth -->
                        <input type="hidden" id="auth_password" name="auth_password">

                        <hr>
                        <div style="text-align: right;">
                            <h3>Total: $<span id="totalAmount">0.00</span></h3>
                        </div>

                        <div class="d-flex gap-2" style="margin-top: 20px;">
                            <button type="button" class="btn btn-primary" id="openCheckoutBtn">💳 Guardar Venta</button>
                            <a href="<?= base_url('sales') ?>" class="btn btn-secondary">❌ Cancelar</a>
                        </div>

                        <!-- Checkout Modal (Inside form to submit data) -->
                        <div id="checkoutModal" class="modal">
                            <div class="modal-content" style="max-width: 600px;">
                                <div class="modal-header">
                                    <h3>💳 Finalizar Venta y Cobro</h3>
                                    <span class="modal-close" id="closeCheckoutModal">&times;</span>
                                </div>
                                <div class="modal-body">
                                    <div class="text-center mb-3">
                                        <h5 class="text-muted mb-1">Total de la Venta</h5>
                                        <h1 class="text-success display-4 fw-bold" id="checkoutDisplayTotal">
                                            $0.00
                                        </h1>
                                    </div>
                                    <hr>
                                    <div class="form-group mb-3">
                                        <label for="payment_type" class="form-label font-weight-bold">Condición de Venta *</label>
                                        <select id="payment_type" name="payment_type" class="form-select" required>
                                            <option value="cash">Contado (Cobro Inmediato - Multimedio)</option>
                                            <option value="credit">Crédito / Cuenta Corriente (A cobrar luego)</option>
                                        </select>
                                    </div>

                                    <!-- Sección de Pagos Mixtos / Cajas -->
                                    <div id="mixedPaymentsContainer" class="mt-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label class="form-label font-weight-bold mb-0">Desglose de Cobro (Medios de Pago)</label>
                                            <button type="button" class="btn btn-sm btn-outline-success" id="addPaymentRowBtn">
                                                ➕ Agregar Medio de Pago
                                            </button>
                                        </div>

                                        <table class="table table-bordered align-middle" id="paymentRowsTable">
                                            <thead>
                                                <tr class="table-light">
                                                    <th>Caja / Cuenta Destino</th>
                                                    <th width="160">Monto ($)</th>
                                                    <th width="40"></th>
                                                </tr>
                                            </thead>
                                            <tbody id="paymentRowsBody">
                                                <!-- Fila de pago por defecto -->
                                                <tr class="payment-row">
                                                    <td>
                                                        <select name="payments[0][account_id]" class="form-select payment-account-select" required>
                                                            <?php if (!empty($accounts)): ?>
                                                                <?php foreach ($accounts as $account): ?>
                                                                    <option value="<?= $account['id'] ?>"><?= esc($account['name']) ?></option>
                                                                <?php endforeach; ?>
                                                            <?php endif; ?>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="number" step="0.01" min="0.01" name="payments[0][amount]" class="form-control payment-amount-input" required placeholder="0.00">
                                                    </td>
                                                    <td class="text-center">
                                                        <button type="button" class="btn btn-sm btn-outline-danger remove-payment-row-btn">&times;</button>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>

                                        <div class="d-flex justify-content-between align-items-center p-2 bg-light rounded mb-3">
                                            <span>Cobrado en Medios: <strong id="totalPaidDisplay" class="text-success">$0.00</strong></span>
                                            <span>Falta Asignar: <strong id="remainingToPayDisplay" class="text-danger">$0.00</strong></span>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-end gap-2 mt-4">
                                        <button type="button" class="btn btn-secondary btn-lg" id="cancelCheckout">Volver</button>
                                        <button type="submit" class="btn btn-success btn-lg fw-bold" id="confirmSaleSubmitBtn">✅ Confirmar Venta</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Product Selection Modal -->
<div id="productModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Seleccionar Producto</h3>
            <span class="modal-close" id="closeProductModal">&times;</span>
        </div>
        <div class="modal-body">
            <input type="text" id="productSearch" class="search-box"
                placeholder="Buscar producto por nombre o código..." autocomplete="off">

            <div class="product-grid" id="productGrid">
                <!-- Products will be loaded via AJAX -->
                <div class="text-center p-3 text-muted">Cargando productos...</div>
            </div>
        </div>
    </div>
</div>

<!-- Auth Modal -->
<div id="authModal" class="modal">
    <div class="modal-content" style="max-width: 400px;">
        <div class="modal-header">
            <h3 class="text-warning">⚠️ Autorización Requerida</h3>
            <span class="modal-close" id="closeAuthModal">&times;</span>
        </div>
        <div class="modal-body">
            <p>El precio ingresado es menor al precio mínimo permitido. Ingrese la contraseña de autorización para
                continuar.</p>
            <div class="form-group">
                <input type="password" id="modalAuthPassword" class="form-control" placeholder="Contraseña">
                <div id="authError" class="text-danger small mt-1" style="display:none;">Contraseña incorrecta</div>
            </div>
            <div class="d-flex justify-content-end gap-2 mt-3">
                <button type="button" class="btn btn-secondary" id="cancelAuth">Cancelar</button>
                <button type="button" class="btn btn-primary" id="confirmAuth">Autorizar</button>
            </div>
        </div>
    </div>
</div>

<!-- Customer Modal -->
<div id="customerModal" class="modal">
    <div class="modal-content" style="max-width: 500px;">
        <div class="modal-header">
            <h3>Nuevo Cliente</h3>
            <span class="modal-close" id="closeCustomerModal">&times;</span>
        </div>
        <div class="modal-body">
            <form id="customerForm">
                <div class="form-group">
                    <label class="form-label">Nombre *</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">RUC / CI</label>
                    <input type="text" name="document" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="phone" class="form-control">
                </div>
                <div id="customerError" class="alert alert-danger" style="display:none; margin-top: 10px;"></div>
                <div class="d-flex justify-content-end gap-2 mt-3">
                    <button type="button" class="btn btn-secondary" id="cancelCustomer">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Cliente</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Modal Elements
        const productModal = document.getElementById('productModal');
        const authModal = document.getElementById('authModal');
        const customerModal = document.getElementById('customerModal');

        const checkoutModal = document.getElementById('checkoutModal');

        const openProductBtn = document.getElementById('openProductModal');
        const openCustomerBtn = document.getElementById('openCustomerModal');
        const openCheckoutBtn = document.getElementById('openCheckoutBtn');

        const closeProductBtn = document.getElementById('closeProductModal');
        const closeAuthBtn = document.getElementById('closeAuthModal');
        const closeCustomerBtn = document.getElementById('closeCustomerModal');
        const closeCheckoutModal = document.getElementById('closeCheckoutModal');

        const cancelAuthBtn = document.getElementById('cancelAuth');
        const cancelCustomerBtn = document.getElementById('cancelCustomer');
        const cancelCheckout = document.getElementById('cancelCheckout');

        const confirmAuthBtn = document.getElementById('confirmAuth');

        // Search & Grid
        const productSearch = document.getElementById('productSearch');
        const productGrid = document.getElementById('productGrid');
        const productCards = document.querySelectorAll('.product-card');

        // Sale Form
        const productsContainer = document.getElementById('products-container');
        const emptyMessage = document.getElementById('empty-message');
        const totalAmountSpan = document.getElementById('totalAmount');
        const form = document.getElementById('saleForm');
        const customerForm = document.getElementById('customerForm');
        const customerSelect = document.getElementById('customer_id');

        // Warehouse
        const warehouseSelect = document.getElementById('warehouse_id');
        const warehouseHint = document.getElementById('warehouse-hint');

        // State
        let currentEditingInput = null;
        let originalPrice = 0;
        let previousWarehouseValue = warehouseSelect.value;

        // --- Helper to update button state (Modified: Button always enabled, just visual cue) ---
        function updateProductButtonState() {
            // We no longer disable the button, so users can click and get the validation message
            if (warehouseSelect.value && warehouseSelect.value !== "") {
                openProductBtn.classList.remove('btn-secondary');
                openProductBtn.classList.add('btn-primary');
                warehouseHint.style.display = 'none';
            } else {
                openProductBtn.classList.remove('btn-primary');
                openProductBtn.classList.add('btn-secondary');
                warehouseHint.style.display = 'block';
            }
        }

        // --- Initial Check ---
        // Ensure button is enabled to allow validation click
        openProductBtn.disabled = false;
        updateProductButtonState();

        // Track previous value for reverting
        $(warehouseSelect).on('select2:opening', function (e) {
            previousWarehouseValue = warehouseSelect.value;
        });
        // Fallback for native focus if select2 hidden input interaction varies
        warehouseSelect.addEventListener('focus', function () {
            previousWarehouseValue = this.value;
        });

        // --- Warehouse Logic ---
        function handleWarehouseChange(newValue, revertCallback) {
            if (productsContainer.children.length > 0) {
                if (confirm('⚠️ ADVERTENCIA: Al cambiar de depósito se eliminarán los productos agregados a la venta.\n\n¿Está seguro que desea continuar?')) {
                    // User confirmed: Clear products
                    productsContainer.innerHTML = '';
                    emptyMessage.style.display = 'block';
                    calculateTotal();
                    previousWarehouseValue = newValue;
                    updateProductButtonState();
                } else {
                    // User cancelled: Revert
                    revertCallback();
                }
            } else {
                // No products, just update
                previousWarehouseValue = newValue;
                updateProductButtonState();
            }
        }

        // Listen to jQuery change (Select2)
        $(warehouseSelect).on('change', function (e) {
            // Did the value actually change?
            if (this.value === previousWarehouseValue) return;

            const newValue = this.value;
            const self = this;

            handleWarehouseChange(newValue, function () {
                // Revert logic for Select2
                $(self).val(previousWarehouseValue).trigger('change.select2');
            });
        });

        // Note: We remove the native 'change' listener to avoid double-firing with Select2
        // warehouseSelect.addEventListener('change', ...) -> Removed in favor of Select2 handler which covers both
        // --- Search Logic ---
        let debounceTimer;
        productSearch.addEventListener('keyup', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                fetchProducts(this.value);
            }, 300);
        });

        function fetchProducts(term = '') {
            const warehouseId = warehouseSelect.value;
            if (!warehouseId) {
                return;
            }

            productGrid.innerHTML = '<div class="text-center p-3 text-muted">Cargando...</div>';

            // Pass warehouse_id to get specific stock
            fetch(`<?= base_url('sales/search-products') ?>?term=${encodeURIComponent(term)}&warehouse_id=${warehouseId}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => response.json())
                .then(products => {
                    productGrid.innerHTML = '';

                    if (products.length === 0) {
                        productGrid.innerHTML = '<div class="text-center p-3 text-muted">No se encontraron productos</div>';
                        return;
                    }

                    products.forEach(product => {
                        const card = document.createElement('div');
                        card.className = 'product-card';
                        if (parseInt(product.stock) <= 10) card.classList.add('low-stock');

                        // Data attributes
                        card.dataset.id = product.id;
                        card.dataset.name = product.name;
                        card.dataset.price = product.price;
                        card.dataset.minPrice = product.min_sale_price;
                        card.dataset.stock = product.stock; // This is now warehouse_stock thanks to controller override

                        card.innerHTML = `
                    <div style="font-weight: bold;">${escapeHtml(product.name)}</div>
                    <div class="text-muted small">Code: ${escapeHtml(product.code)}</div>
                    ${product.imei1 ? `<div class="text-muted small">IMEI 1: ${escapeHtml(product.imei1)}</div>` : ''}
                    ${product.imei2 ? `<div class="text-muted small">IMEI 2: ${escapeHtml(product.imei2)}</div>` : ''}
                    <div class="d-flex justify-content-between mt-2">
                        <span class="text-primary">$${parseFloat(product.price).toFixed(2)}</span>
                        <span class="badge ${parseInt(product.stock) <= 10 ? 'badge-danger' : 'badge-success'}">
                            Stock: ${product.stock}
                        </span>
                    </div>
                `;

                        card.onclick = () => addProductToSale(card);
                        productGrid.appendChild(card);
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    productGrid.innerHTML = '<div class="text-center p-3 text-danger">Error al cargar productos</div>';
                });
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // --- Modal Logic ---
        openProductBtn.onclick = () => {
            if (!warehouseSelect.value) {
                // Remove alert, use visual feedback
                // alert('Seleccione un depósito primero');

                // Highlight the select
                warehouseSelect.classList.add('is-invalid');
                warehouseSelect.focus();

                // Try to open Select2 if present
                if ($(warehouseSelect).data('select2')) {
                    $(warehouseSelect).select2('open');
                }

                // Remove highlight after a few seconds
                setTimeout(() => {
                    warehouseSelect.classList.remove('is-invalid');
                }, 2000);

                return;
            }
            productModal.style.display = "block";
            productSearch.value = ''; // Clear search
            fetchProducts(); // Load initial products
        };
        closeProductBtn.onclick = () => productModal.style.display = "none";

        if (openCustomerBtn) {
            openCustomerBtn.onclick = () => {
                customerModal.style.display = "block";
                customerForm.reset();
                document.getElementById('customerError').style.display = 'none';
            };
        }

        closeCustomerBtn.onclick = () => customerModal.style.display = "none";
        cancelCustomerBtn.onclick = () => customerModal.style.display = "none";

        closeAuthBtn.onclick = cancelAuth;
        cancelAuthBtn.onclick = cancelAuth;

        closeCheckoutModal.onclick = () => checkoutModal.style.display = "none";
        cancelCheckout.onclick = () => checkoutModal.style.display = "none";

        window.onclick = (event) => {
            if (event.target == productModal) productModal.style.display = "none";
            if (event.target == authModal) cancelAuth();
            if (event.target == customerModal) customerModal.style.display = "none";
            if (event.target == checkoutModal) checkoutModal.style.display = "none";
        }

        // --- Customer Logic ---
        customerForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData(this);

            fetch('<?= base_url('customers/ajax-store') ?>', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Add new option and select it
                        const option = new Option(data.customer.name, data.customer.id);
                        customerSelect.add(option, undefined);
                        customerSelect.value = data.customer.id;

                        customerModal.style.display = "none";
                        alert('Cliente creado exitosamente');
                    } else {
                        const errorDiv = document.getElementById('customerError');
                        errorDiv.innerHTML = Object.values(data.errors).join('<br>');
                        errorDiv.style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error de conexión');
                });
        });



        // --- Product Logic ---
        window.addProductToSale = function (card) {
            const id = card.dataset.id;
            const name = card.dataset.name;
            const price = parseFloat(card.dataset.price);
            const minPrice = parseFloat(card.dataset.minPrice);
            const stock = parseInt(card.dataset.stock);

            if (stock <= 0) {
                alert('Este producto no tiene stock disponible en el depósito seleccionado');
                return;
            }

            // Check if already added
            if (document.querySelector(`tr[data-id="${id}"]`)) {
                alert('Este producto ya está en la lista');
                return;
            }

            const row = document.createElement('tr');
            row.dataset.id = id;
            row.innerHTML = `
            <td>
                <strong>${name}</strong>
                <input type="hidden" name="products[${id}][product_id]" value="${id}">
            </td>
            <td>
                <input type="number" name="products[${id}][quantity]" class="form-control quantity-input" value="1" min="1" max="${stock}">
            </td>
            <td>
                <input type="number" name="products[${id}][price]" class="form-control price-input" value="${price.toFixed(2)}" step="0.01" data-min="${minPrice}">
            </td>
            <td>
                <textarea name="products[${id}][description]" class="form-control" rows="2" style="font-size: 0.85rem;">IMEI: </textarea>
            </td>
            <td>
                <input type="text" class="form-control subtotal-display" value="$${price.toFixed(2)}" readonly>
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-danger remove-btn">🗑️</button>
            </td>
        `;

            productsContainer.appendChild(row);
            emptyMessage.style.display = 'none';
            productModal.style.display = 'none';

            attachRowEvents(row);
            calculateTotal();
        };

        function attachRowEvents(row) {
            const qtyInput = row.querySelector('.quantity-input');
            const priceInput = row.querySelector('.price-input');
            const removeBtn = row.querySelector('.remove-btn');

            qtyInput.addEventListener('input', () => updateSubtotal(row));

            // Price Validation Logic
            priceInput.addEventListener('focus', function () {
                originalPrice = this.value;
            });

            priceInput.addEventListener('change', function () {
                const newPrice = parseFloat(this.value) || 0;
                const minPrice = parseFloat(this.dataset.min);

                if (newPrice < minPrice) {
                    currentEditingInput = this;
                    showAuthModal();
                } else {
                    updateSubtotal(row);
                }
            });

            removeBtn.addEventListener('click', function () {
                row.remove();
                if (productsContainer.children.length === 0) {
                    emptyMessage.style.display = 'block';
                }
                calculateTotal();
            });
        }

        function updateSubtotal(row) {
            const qty = parseFloat(row.querySelector('.quantity-input').value) || 0;
            const price = parseFloat(row.querySelector('.price-input').value) || 0;
            const subtotal = qty * price;
            row.querySelector('.subtotal-display').value = '$' + subtotal.toFixed(2);
            calculateTotal();
        }

        function calculateTotal() {
            let total = 0;
            document.querySelectorAll('#products-container tr').forEach(row => {
                const qty = parseFloat(row.querySelector('.quantity-input').value) || 0;
                const price = parseFloat(row.querySelector('.price-input').value) || 0;
                total += qty * price;
            });
            totalAmountSpan.textContent = total.toFixed(2);
        }

        // --- Auth Logic ---
        function showAuthModal() {
            authModal.style.display = "block";
            document.getElementById('modalAuthPassword').value = '';
            document.getElementById('authError').style.display = 'none';
            document.getElementById('modalAuthPassword').focus();
        }

        function cancelAuth() {
            authModal.style.display = "none";
            if (currentEditingInput) {
                currentEditingInput.value = originalPrice; // Revert price
                // Trigger update to fix subtotal
                const row = currentEditingInput.closest('tr');
                updateSubtotal(row);
                currentEditingInput = null;
            }
        }

        confirmAuthBtn.addEventListener('click', function () {
            const password = document.getElementById('modalAuthPassword').value;

            // AJAX Validation
            fetch('<?= base_url('sales/validate-auth') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: 'password=' + encodeURIComponent(password)
            })
                .then(response => response.json())
                .then(data => {
                    if (data.valid) {
                        authModal.style.display = "none";
                        document.getElementById('auth_password').value = password; // Store for backend validation

                        // Update subtotal with the new (lower) price
                        if (currentEditingInput) {
                            const row = currentEditingInput.closest('tr');
                            updateSubtotal(row);

                            // Visual feedback
                            currentEditingInput.style.backgroundColor = '#d4edda';
                            currentEditingInput.style.borderColor = '#c3e6cb';
                        }
                        currentEditingInput = null;
                    } else {
                        document.getElementById('authError').style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error de conexión');
                });
        });

        // --- Multi-Payment Logic ---
        const paymentTypeSelect = document.getElementById('payment_type');
        const mixedPaymentsContainer = document.getElementById('mixedPaymentsContainer');
        const paymentRowsBody = document.getElementById('paymentRowsBody');
        const addPaymentRowBtn = document.getElementById('addPaymentRowBtn');
        const totalPaidDisplay = document.getElementById('totalPaidDisplay');
        const remainingToPayDisplay = document.getElementById('remainingToPayDisplay');
        let paymentRowIndex = 1;

        function getSaleTotal() {
            return parseFloat(totalAmountSpan.textContent) || 0;
        }

        function calculatePaymentTotals() {
            const saleTotal = getSaleTotal();
            let totalPaid = 0;

            document.querySelectorAll('.payment-amount-input').forEach(input => {
                totalPaid += parseFloat(input.value) || 0;
            });

            const remaining = Math.max(0, saleTotal - totalPaid);

            totalPaidDisplay.textContent = '$' + totalPaid.toFixed(2);
            remainingToPayDisplay.textContent = '$' + remaining.toFixed(2);

            if (Math.abs(saleTotal - totalPaid) < 0.01) {
                remainingToPayDisplay.className = 'text-success';
            } else {
                remainingToPayDisplay.className = 'text-danger';
            }
        }

        paymentTypeSelect.addEventListener('change', function () {
            if (this.value === 'credit') {
                mixedPaymentsContainer.style.display = 'none';
                document.querySelectorAll('.payment-amount-input').forEach(i => i.required = false);
            } else {
                mixedPaymentsContainer.style.display = 'block';
                document.querySelectorAll('.payment-amount-input').forEach(i => i.required = true);
                calculatePaymentTotals();
            }
        });

        addPaymentRowBtn.addEventListener('click', function () {
            const firstRowSelect = document.querySelector('.payment-account-select');
            if (!firstRowSelect) return;

            const optionsHtml = firstRowSelect.innerHTML;
            const saleTotal = getSaleTotal();
            let currentPaid = 0;
            document.querySelectorAll('.payment-amount-input').forEach(i => currentPaid += (parseFloat(i.value) || 0));
            const remaining = Math.max(0, saleTotal - currentPaid);

            const tr = document.createElement('tr');
            tr.className = 'payment-row';
            tr.innerHTML = `
                <td>
                    <select name="payments[${paymentRowIndex}][account_id]" class="form-select payment-account-select" required>
                        ${optionsHtml}
                    </select>
                </td>
                <td>
                    <input type="number" step="0.01" min="0.01" name="payments[${paymentRowIndex}][amount]" class="form-control payment-amount-input" value="${remaining > 0 ? remaining.toFixed(2) : ''}" required placeholder="0.00">
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-payment-row-btn">&times;</button>
                </td>
            `;

            paymentRowsBody.appendChild(tr);
            paymentRowIndex++;

            tr.querySelector('.payment-amount-input').addEventListener('input', calculatePaymentTotals);
            tr.querySelector('.remove-payment-row-btn').addEventListener('click', function () {
                tr.remove();
                calculatePaymentTotals();
            });

            calculatePaymentTotals();
        });

        paymentRowsBody.addEventListener('input', function (e) {
            if (e.target.classList.contains('payment-amount-input')) {
                calculatePaymentTotals();
            }
        });

        paymentRowsBody.addEventListener('click', function (e) {
            if (e.target.classList.contains('remove-payment-row-btn')) {
                const rows = paymentRowsBody.querySelectorAll('.payment-row');
                if (rows.length > 1) {
                    e.target.closest('.payment-row').remove();
                    calculatePaymentTotals();
                } else {
                    alert('Debe mantener al menos un medio de pago para ventas al contado');
                }
            }
        });

        // Checkout Interceptor
        openCheckoutBtn.addEventListener('click', function () {
            if (!warehouseSelect.value) {
                alert('Seleccione un depósito antes de finalizar');
                window.scrollTo(0, 0);
                return;
            }

            if (productsContainer.children.length === 0) {
                alert('Debe agregar al menos un producto a la venta');
                return;
            }

            if (!customerSelect.value) {
                alert('Seleccione un cliente antes de finalizar');
                window.scrollTo(0, 0);
                return;
            }

            const saleTotal = getSaleTotal();
            document.getElementById('checkoutDisplayTotal').textContent = '$' + saleTotal.toFixed(2);

            // Auto-llenar primera fila con el total si está vacía
            const firstAmountInput = document.querySelector('.payment-amount-input');
            if (firstAmountInput && (!firstAmountInput.value || parseFloat(firstAmountInput.value) === 0)) {
                firstAmountInput.value = saleTotal.toFixed(2);
            }

            calculatePaymentTotals();
            checkoutModal.style.display = 'block';
        });

        // Form Validation fallback (still fires after Checkout Confirm)
        form.addEventListener('submit', function (e) {
            if (!warehouseSelect.value || productsContainer.children.length === 0) {
                e.preventDefault();
                alert('Faltan completar datos del depósito o productos');
                return;
            }

            if (paymentTypeSelect.value === 'cash') {
                let totalPaid = 0;
                document.querySelectorAll('.payment-amount-input').forEach(input => {
                    totalPaid += parseFloat(input.value) || 0;
                });
                const saleTotal = getSaleTotal();
                if (Math.abs(saleTotal - totalPaid) > 0.01) {
                    e.preventDefault();
                    alert('El total asignado a los medios de pago ($' + totalPaid.toFixed(2) + ') debe ser igual al total de la venta ($' + saleTotal.toFixed(2) + ').');
                }
            }
        });
    });
</script>

<?php echo view('templates/footer'); ?>