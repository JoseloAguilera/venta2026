<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket #
        <?= $sale['sale_number'] ?>
    </title>
    <style>
        @page {
            margin: 0;
            size: auto;
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            font-weight: bold;
            /* FORCE BOLD FOR DARKER PRINT */
            color: #000000 !important;
            /* PURE BLACK */
            margin: 0;
            padding: 5px;
            width: 70mm;
            max-width: 70mm;
            box-sizing: border-box;
            /* Adjusted thermal width */
        }

        .body-content {
            margin-left: 10mm;
            /* Extra margin for table body */
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
            border-bottom: 1px dashed #000;
            padding-bottom: 5px;
        }

        .store-name {
            font-size: 16px;
            font-weight: bold;
            margin: 0;
        }

        .info {
            margin-bottom: 10px;
        }

        .info p {
            margin: 2px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }

        th,
        td {
            text-align: left;
            padding: 2px 0;
        }

        .text-right {
            text-align: right;
        }

        .totals {
            border-top: 1px dashed #000;
            padding-top: 5px;
            margin-top: 5px;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 10px;
        }

        .btn-print {
            display: block;
            width: 100%;
            padding: 10px;
            background: #333;
            color: #fff;
            border: none;
            margin-bottom: 20px;
            cursor: pointer;
        }

        @media print {
            .btn-print {
                display: none;
            }

            body {
                width: auto;
                margin: 0;
                padding: 0;
            }
        }
    </style>
</head>

<body>
    <button class="btn-print" onclick="window.print()">🖨️ IMPRIMIR</button>

    <div class="header">
        <?php
        $logoPath = FCPATH . 'assets/logo.png';
        if (file_exists($logoPath)): ?>
            <img src="<?= base_url('assets/logo.png') ?>?v=<?= time() ?>" alt="Logo"
                style="width: 100px; height: auto; margin-bottom: 5px; filter: grayscale(100%);">
        <?php endif; ?>
        <h1 class="store-name">
            <?= esc($settings['company_name'] ?? 'Sistema Ventas 2026') ?>
        </h1>
        <p>
            <?= esc($settings['company_address'] ?? 'Dirección del Local') ?>
        </p>
        <p>
            <?= esc($settings['company_email'] ?? 'email@ejemplo.com') ?> | Tel:
            <?= esc($settings['company_phone'] ?? '000-000-000') ?>
        </p>
    </div>

    <div class="body-content">
        <div class="info">
            <p><strong>Ticket:</strong>
                <?= $sale['sale_number'] ?>
            </p>
            <p><strong>Fecha/Hora:</strong>
                <?= date('d/m/Y H:i', strtotime($sale['created_at'])) ?>
            </p>
            <p><strong>Cliente:</strong>
                <?= esc($sale['customer_name']) ?>
            </p>
            <?php if (!empty($sale['customer_document'])): ?>
                <p><strong>RUC/CI:</strong>
                    <?= esc($sale['customer_document']) ?>
                </p>
            <?php endif; ?>
            <p><strong>Vendedor:</strong>
                <?= esc($sale['user_name']) ?>
            </p>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 15%">Cant</th>
                    <th style="width: 55%">Producto</th>
                    <th style="width: 30%" class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sale['details'] as $detail): ?>
                    <tr>
                        <td>
                            <?= $detail['quantity'] ?>
                        </td>
                        <td>
                            <?= esc($detail['product_name']) ?>
                        </td>
                        <td class="text-right">
                            <?= number_format($detail['subtotal'], 2, ',', '.') ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="totals">
            <div style="display: flex; justify-content: space-between;">
                <span>Subtotal:</span>
                <span>
                    <?= number_format($sale['subtotal'], 2, ',', '.') ?>
                </span>
            </div>
            <div
                style="display: flex; justify-content: space-between; font-weight: bold; font-size: 14px; margin-top: 5px;">
                <span>TOTAL:</span>
                <span>
                    <?= number_format($sale['total'], 2, ',', '.') ?>
                </span>
            </div>
        </div>

        <div class="footer">
            <p>¡Gracias por su compra!</p>
        </div>
    </div> <!-- .body-content -->

    <script>
        // Auto-print only if opened as a popup (optional)
        // window.onload = function() { window.print(); }
    </script>
</body>

</html>