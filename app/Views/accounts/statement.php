<?php
$extraCSS = [
    'assets/css/dashboard.css',
    'https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css'
];
echo view('templates/header', ['title' => $title, 'extraCSS' => $extraCSS]);
?>

<style>
    .summary-card {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
        border: 1px solid #ddd;
    }
    .summary-card h3 {
        margin: 0;
        font-size: 2rem;
    }
    
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
            <div class="topbar-actions gap-2 d-flex">
                <button type="button" class="btn btn-primary" id="openTxModal">➕ Registrar Movimiento</button>
                <a href="<?= base_url('accounts') ?>" class="btn btn-secondary">Volver</a>
            </div>
        </div>

        <div class="content-area">
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success">
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>
            
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="summary-card">
                        <p class="text-muted mb-1">Balance del Periodo</p>
                        <h3 class="<?= $filtered_balance < 0 ? 'text-danger' : 'text-success' ?>">
                            <?= formato_moneda($filtered_balance) ?>
                        </h3>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card h-100">
                        <div class="card-body d-flex align-items-center">
                            <form action="<?= current_url() ?>" method="GET" class="w-100 row align-items-end">
                                <div class="col-md-4">
                                    <label class="form-label">Desde</label>
                                    <input type="date" name="start_date" class="form-control" value="<?= $start_date ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Hasta</label>
                                    <input type="date" name="end_date" class="form-control" value="<?= $end_date ?>">
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-primary w-100">🔍 Filtrar Movimientos</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <h4 class="mb-3">Historial de Movimientos</h4>
                    <div class="table-responsive">
                        <table id="statementTable" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Referencia</th>
                                    <th>Descripción</th>
                                    <th>Usuario</th>
                                    <th class="text-end">Monto</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($transactions)): ?>
                                    <?php foreach ($transactions as $tx): ?>
                                        <tr>
                                            <td data-order="<?= $tx['date'] ?>"><?= date('d/m/Y H:i', strtotime($tx['date'])) ?></td>
                                            <td>
                                                <span class="badge bg-secondary"><?= esc(strtoupper($tx['reference_type'])) ?></span>
                                                <?php if($tx['reference_id']) echo "#".$tx['reference_id']; ?>
                                            </td>
                                            <td><?= esc($tx['description']) ?></td>
                                            <td><?= esc($tx['user_name']) ?></td>
                                            <td class="text-end" style="font-weight: bold; color: <?= in_array($tx['type'], ['income', 'transfer_in']) ? 'green' : 'red' ?>">
                                                <?= in_array($tx['type'], ['income', 'transfer_in']) ? '+' : '-' ?> <?= formato_moneda($tx['amount']) ?>
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

<!-- Transaction Modal -->
<div id="txModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Registrar Movimiento Manual</h3>
            <span class="modal-close" id="closeTxModal">&times;</span>
        </div>
        <div class="modal-body">
            <form action="<?= base_url('accounts/storeTransaction/'.$account['id']) ?>" method="POST">
                <?= csrf_field() ?>
                <div class="form-group mb-3">
                    <label class="form-label">Tipo de Movimiento *</label>
                    <select name="type" class="form-control" required>
                        <option value="income">Entrada (Ingreso)</option>
                        <option value="expense">Salida (Egreso)</option>
                    </select>
                </div>
                <div class="form-group mb-3">
                    <label class="form-label">Monto ($) *</label>
                    <input type="number" step="0.01" name="amount" class="form-control" required min="0.01">
                </div>
                <div class="form-group mb-3">
                    <label class="form-label">Descripción / Motivo *</label>
                    <textarea name="description" class="form-control" required rows="3"></textarea>
                </div>
                <div class="d-flex justify-content-end gap-2 mt-3">
                    <button type="button" class="btn btn-secondary" id="cancelTxModal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Registrar</button>
                </div>
            </form>
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
        $('#statementTable').DataTable({
            'order': [[0, 'desc']]
        });
        
        // Modal logic
        const txModal = document.getElementById('txModal');
        const openBtn = document.getElementById('openTxModal');
        const closeBtn = document.getElementById('closeTxModal');
        const cancelBtn = document.getElementById('cancelTxModal');
        
        if (openBtn && txModal) {
            openBtn.onclick = () => txModal.style.display = 'block';
            closeBtn.onclick = () => txModal.style.display = 'none';
            cancelBtn.onclick = () => txModal.style.display = 'none';
            window.onclick = (event) => {
                if(event.target == txModal) txModal.style.display = 'none';
            }
        }
    });
</script>
";
echo view('templates/footer', ['extraJS' => $extraJS, 'scripts' => $scripts]);
?>
