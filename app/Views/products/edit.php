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
</style><div class="dashboard-wrapper">
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
                    <?php if (session()->getFlashdata('errors')): ?>
                        <div class="alert alert-danger">
                            <ul style="margin: 0; padding-left: 1.25rem;">
                                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('products/update/' . $product['id']) ?>" method="POST">
                        <?= csrf_field() ?>

                        <div class="form-group">
                            <label for="category_id" class="form-label">Categoría *</label>
                            <div class="d-flex gap-2">
                                <select id="category_id" name="category_id" class="form-control" required>
                                    <option value="">Seleccione una categoría</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= $category['id'] ?>" <?= old('category_id', $product['category_id']) == $category['id'] ? 'selected' : '' ?>>
                                            <?= esc($category['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="button" class="btn btn-secondary" id="openCategoryModal" title="Nueva Categoría">➕</button>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="code" class="form-label">Código *</label>
                            <input type="text" id="code" name="code" class="form-control"
                                value="<?= old('code', $product['code']) ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="name" class="form-label">Nombre *</label>
                            <input type="text" id="name" name="name" class="form-control"
                                value="<?= old('name', $product['name']) ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="description" class="form-label">Descripción</label>
                            <textarea id="description" name="description"
                                class="form-control"><?= old('description', $product['description']) ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="cost_price" class="form-label">Precio Costo *</label>
                                    <input type="number" id="cost_price" name="cost_price" class="form-control"
                                        step="0.01" min="0"
                                        value="<?= old('cost_price', $product['cost_price'] ?? 0) ?>" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="price" class="form-label">Precio Venta *</label>
                                    <input type="number" id="price" name="price" class="form-control" step="0.01"
                                        min="0" value="<?= old('price', $product['price']) ?>" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="min_sale_price" class="form-label">Precio Mínimo *</label>
                                    <input type="number" id="min_sale_price" name="min_sale_price" class="form-control"
                                        step="0.01" min="0"
                                        value="<?= old('min_sale_price', $product['min_sale_price'] ?? 0) ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="stock" class="form-label">Stock Actual *</label>
                            <input type="number" id="stock" name="stock" class="form-control" min="0"
                                value="<?= old('stock', $product['stock']) ?>" readonly
                                style="background-color: #f3f4f6; cursor: not-allowed;">
                            <small class="text-muted">El stock solo se modifica mediante ajustes de inventario</small>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                💾 Actualizar
                            </button>
                            <a href="<?= base_url('products') ?>" class="btn btn-secondary">
                                ❌ Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Category Modal -->
<div id="categoryModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Nueva Categoría</h3>
            <span class="modal-close" id="closeCategoryModal">&times;</span>
        </div>
        <div class="modal-body">
            <form id="categoryForm">
                <div class="form-group">
                    <label class="form-label">Nombre *</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Descripción</label>
                    <textarea name="description" class="form-control"></textarea>
                </div>
                <div id="categoryError" class="alert alert-danger" style="display:none; margin-top: 10px;"></div>
                <div class="d-flex justify-content-end gap-2 mt-3">
                    <button type="button" class="btn btn-secondary" id="cancelCategory">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Categoría</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const categoryModal = document.getElementById('categoryModal');
    const openCategoryBtn = document.getElementById('openCategoryModal');
    const closeCategoryBtn = document.getElementById('closeCategoryModal');
    const cancelCategoryBtn = document.getElementById('cancelCategory');
    const categoryForm = document.getElementById('categoryForm');
    const categorySelect = document.getElementById('category_id');

    if(openCategoryBtn) {
        openCategoryBtn.onclick = () => {
            categoryModal.style.display = "block";
            categoryForm.reset();
            document.getElementById('categoryError').style.display = 'none';
        };
    }

    if(closeCategoryBtn) closeCategoryBtn.onclick = () => categoryModal.style.display = "none";
    if(cancelCategoryBtn) cancelCategoryBtn.onclick = () => categoryModal.style.display = "none";

    window.onclick = (event) => {
        if (event.target == categoryModal) categoryModal.style.display = "none";
    };

    if(categoryForm) {
        categoryForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch('<?= base_url('categories/ajax-store') ?>', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const option = new Option(data.category.name, data.category.id);
                    categorySelect.add(option, undefined);
                    categorySelect.value = data.category.id;
                    
                    if ($(categorySelect).hasClass('select2-hidden-accessible')) {
                        $(categorySelect).trigger('change.select2');
                    }

                    categoryModal.style.display = "none";
                    alert('Categoría creada exitosamente');
                } else {
                    const errorDiv = document.getElementById('categoryError');
                    errorDiv.innerHTML = Object.values(data.errors).join('<br>');
                    errorDiv.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error de conexión');
            });
        });
    }
});
</script>

<?php echo view('templates/footer'); ?>