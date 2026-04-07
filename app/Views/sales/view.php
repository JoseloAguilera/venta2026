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
                <h2><?= $title ?></h2>
            </div>
            <div class="topbar-actions">
                <button type="button" class="btn btn-warning" onclick="promptEditObservations()">
                    📝 Editar Observaciones
                </button>
                <?php if ($sale['payment_type'] === 'credit' && $pending_balance > 0 && can_insert('collections')): ?>
                    <a href="<?= base_url('collections/create/' . $sale['id']) ?>" class="btn btn-success">
                        💰 Registrar Pago
                    </a>
                <?php endif; ?>
                <a href="javascript:void(0)" onclick="openTicket('<?= base_url('sales/ticket/' . $sale['id']) ?>')"
                    class="btn btn-info">
                    🖨️ Imprimir
                </a>
                <a href="<?= base_url('sales') ?>" class="btn btn-secondary">
                    ← Volver
                </a>
            </div>
        </div>

        <div class="content-area">
            <div class="card">
                <div class="card-body">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                        <div>
                            <p><strong>Número:</strong> <?= $sale['sale_number'] ?></p>
                            <p><strong>Fecha:</strong> <?= date('d/m/Y', strtotime($sale['date'])) ?></p>
                            <p><strong>Cliente:</strong> <?= esc($sale['customer_name']) ?></p>
                            <p><strong>Documento:</strong> <?= esc($sale['customer_document']) ?></p>
                        </div>
                        <div>
                            <p><strong>Tipo de Pago:</strong>
                                <span
                                    class="badge <?= $sale['payment_type'] === 'cash' ? 'badge-success' : 'badge-warning' ?>">
                                    <?= $sale['payment_type'] === 'cash' ? 'Contado' : 'Crédito' ?>
                                </span>
                            </p>
                            <p><strong>Estado:</strong>
                                <?php
                                $badges = [
                                    'paid' => 'badge-success',
                                    'partial' => 'badge-warning',
                                    'pending' => 'badge-secondary', // Consistent gray for pending
                                    'cancelled' => 'badge-danger'   // Red for cancelled
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
                            </p>
                            <?php if ($sale['payment_type'] === 'credit'): ?>
                                <p><strong>Saldo Pendiente:</strong> <span
                                        class="text-danger">$<?= number_format($pending_balance, 2) ?></span></p>
                            <?php endif; ?>
                            <p><strong>Vendedor:</strong> <?= esc($sale['user_name']) ?></p>
                        </div>
                    </div>

                    <h4>Productos</h4>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Precio</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sale['details'] as $detail): ?>
                                    <tr>
                                        <td>
                                            <a href="<?= base_url('products/view/' . $detail['product_id']) ?>"
                                                target="_blank">
                                                <?= esc($detail['product_code']) ?>
                                            </a>
                                        </td>
                                        <td>
                                            <?= esc($detail['product_name']) ?>
                                            <?php if (!empty($detail['description'])): ?>
                                                <br><small class="text-muted"><?= nl2br(esc($detail['description'])) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= $detail['quantity'] ?></td>
                                        <td>$<?= number_format($detail['price'], 2) ?></td>
                                        <td>$<?= number_format($detail['subtotal'], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-right"><strong>Subtotal:</strong></td>
                                    <td><strong>$<?= number_format($sale['subtotal'], 2) ?></strong></td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-right"><strong>Impuestos:</strong></td>
                                    <td><strong>$<?= number_format($sale['tax'], 2) ?></strong></td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-right"><strong>TOTAL:</strong></td>
                                    <td><strong class="text-primary">$<?= number_format($sale['total'], 2) ?></strong>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="editObservationsModal" class="modal"
    style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4);">
    <div class="modal-content"
        style="background-color: #fefefe; margin: 10% auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 600px; border-radius: 8px;">
        <div class="modal-header"
            style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #ddd; padding-bottom: 10px; margin-bottom: 20px;">
            <h2 style="margin: 0;">Editar Observaciones</h2>
            <span class="close" onclick="closeEditModal()"
                style="color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer;">&times;</span>
        </div>
        <form action="<?= base_url('sales/update-observations/' . $sale['id']) ?>" method="POST">
            <input type="hidden" name="auth_password" id="modal_auth_password" value="">
            <div class="modal-body" style="max-height: 50vh; overflow-y: auto;">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Observación</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sale['details'] as $detail): ?>
                                <tr>
                                    <td><?= esc($detail['product_name']) ?></td>
                                    <td>
                                        <textarea name="observations[<?= $detail['id'] ?>]" class="form-control" rows="2"
                                            style="width: 100%;"><?= esc($detail['description']) ?></textarea>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer"
                style="display: flex; justify-content: flex-end; gap: 10px; border-top: 1px solid #ddd; padding-top: 15px; margin-top: 20px;">
                <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function openTicket(url) {
        window.open(url, 'Ticket', 'width=400,height=600,scrollbars=yes');
    }

    function promptEditObservations() {
        Swal.fire({
            title: 'Autorización Requerida',
            text: 'Ingrese la contraseña de precio mínimo',
            input: 'password',
            inputAttributes: {
                autocapitalize: 'off',
                autocorrect: 'off'
            },
            showCancelButton: true,
            confirmButtonText: 'Verificar',
            cancelButtonText: 'Cancelar',
            showLoaderOnConfirm: true,
            preConfirm: (password) => {
                if (!password) {
                    Swal.showValidationMessage('La contraseña es requerida');
                    return false;
                }

                return fetch('<?= base_url('sales/validate-auth') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: 'password=' + encodeURIComponent(password)
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(response.statusText)
                        }
                        return response.json()
                    })
                    .then(data => {
                        if (!data.valid) {
                            Swal.showValidationMessage('Contraseña incorrecta');
                            return false;
                        }
                        return password;
                    })
                    .catch(error => {
                        Swal.showValidationMessage(`Error de red: ${error}`)
                    });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('modal_auth_password').value = result.value;
                document.getElementById('editObservationsModal').style.display = 'block';
            }
        });
    }

    function closeEditModal() {
        document.getElementById('editObservationsModal').style.display = 'none';
        document.getElementById('modal_auth_password').value = '';
    }

    // Close modal when clicking outside
    window.onclick = function (event) {
        let modal = document.getElementById('editObservationsModal');
        let closeBtn = document.getElementsByClassName('close')[0];
        if (event.target == modal) {
            closeEditModal();
        }
    }
</script>
<?php echo view('templates/footer'); ?>