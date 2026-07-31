<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cierre de Caja #<?= $session['id'] ?></title>
    <style>
        @page {
            margin: 0;
            size: auto;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            font-weight: bold;
            color: #000000 !important;
            margin: 0;
            padding: 5px;
            width: 70mm;
            max-width: 70mm;
            box-sizing: border-box;
        }
        .header {
            text-align: center;
            margin-bottom: 10px;
            border-bottom: 1px dashed #000;
            padding-bottom: 5px;
        }
        .store-name {
            font-size: 14px;
            font-weight: bold;
            margin: 0;
        }
        .info {
            margin-bottom: 10px;
            border-bottom: 1px dashed #000;
            padding-bottom: 5px;
        }
        .info p {
            margin: 2px 0;
        }
        .totals-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5px;
        }
        .totals-table td {
            padding: 2px 0;
        }
        .text-right {
            text-align: right;
        }
        .divider {
            border-top: 1px dashed #000;
            margin: 5px 0;
        }
        .footer {
            text-align: center;
            margin-top: 15px;
            font-size: 10px;
        }
        .btn-print {
            display: block;
            width: 100%;
            padding: 10px;
            background: #333;
            color: #fff;
            border: none;
            margin-bottom: 15px;
            cursor: pointer;
            font-size: 14px;
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
    <button class="btn-print" onclick="window.print()">🖨️ IMPRIMIR COMPROBANTE</button>

    <div class="header">
        <h1 class="store-name"><?= esc($settings['company_name'] ?? 'Venta 2026') ?></h1>
        <p style="margin: 3px 0;">*** ARQUEO Y CIERRE DE CAJA ***</p>
    </div>

    <div class="info">
        <p><strong>TURNO #:</strong> <?= $session['id'] ?></p>
        <p><strong>Caja:</strong> <?= esc($session['account_name']) ?></p>
        <p><strong>Cajero:</strong> <?= esc($session['user_name']) ?></p>
        <p><strong>Apertura:</strong> <?= date('d/m/Y H:i', strtotime($session['opening_time'])) ?></p>
        <p><strong>Cierre:</strong> <?= $session['closing_time'] ? date('d/m/Y H:i', strtotime($session['closing_time'])) : 'N/A' ?></p>
    </div>

    <table class="totals-table">
        <tbody>
            <tr>
                <td>Monto Apertura:</td>
                <td class="text-right">$<?= number_format($session['opening_amount'], 2) ?></td>
            </tr>
            <tr>
                <td>(+) Ventas Efectivo:</td>
                <td class="text-right">+$<?= number_format($summary['sales_income'], 2) ?></td>
            </tr>
            <tr>
                <td>(+) Cobros Clientes:</td>
                <td class="text-right">+$<?= number_format($summary['collection_income'], 2) ?></td>
            </tr>
            <tr>
                <td>(+) Ingresos Varios:</td>
                <td class="text-right">+$<?= number_format($summary['manual_income'], 2) ?></td>
            </tr>
            <tr>
                <td>(-) Gastos:</td>
                <td class="text-right">-$<?= number_format($summary['expense_out'], 2) ?></td>
            </tr>
            <tr>
                <td>(-) Pagos Proveedor:</td>
                <td class="text-right">-$<?= number_format($summary['payment_out'] + $summary['manual_expense'], 2) ?></td>
            </tr>
        </tbody>
    </table>

    <div class="divider"></div>

    <table class="totals-table">
        <tbody>
            <tr>
                <td><strong>Esperado en Caja:</strong></td>
                <td class="text-right"><strong>$<?= number_format($expected_amount, 2) ?></strong></td>
            </tr>
            <tr>
                <td><strong>Contado (Físico):</strong></td>
                <td class="text-right"><strong>$<?= $session['closing_amount'] !== null ? number_format($session['closing_amount'], 2) : '0.00' ?></strong></td>
            </tr>
        </tbody>
    </table>

    <div class="divider"></div>

    <div style="text-align: center; margin-top: 5px;">
        <?php if ($session['closing_amount'] !== null): ?>
            <?php $disc = (float)$session['discrepancy']; ?>
            <?php if ($disc == 0): ?>
                <p style="font-size: 13px; margin: 2px 0;"><strong>CUADRE: EXACTO ($0.00)</strong></p>
            <?php elseif ($disc > 0): ?>
                <p style="font-size: 13px; margin: 2px 0;"><strong>SOBRANTE: +$<?= number_format($disc, 2) ?></strong></p>
            <?php else: ?>
                <p style="font-size: 13px; margin: 2px 0;"><strong>FALTANTE: -$<?= number_format(abs($disc), 2) ?></strong></p>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <div class="footer">
        <p style="margin-top: 25px;">________________________</p>
        <p>Firma Cajero Responsable</p>
    </div>
</body>
</html>
