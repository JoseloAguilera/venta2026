<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= esc($title) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        h2 {
            text-align: center;
            margin-bottom: 5px;
        }
        .date {
            text-align: right;
            margin-bottom: 20px;
            font-style: italic;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        @media print {
            body { margin: 0; }
            button, .no-print { display: none !important; }
        }
    </style>
</head>
<body>

    <h2><?= esc($title) ?></h2>
    <div class="date">Fecha de impresión: <?= date('d/m/Y H:i') ?></div>

    <table>
        <thead>
            <tr>
                <?php if ($type === 'stock'): ?>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th class="text-center">Stock Actual</th>
                    <th class="text-center" style="width: 150px;">Conteo Físico</th>
                <?php elseif ($type === 'price'): ?>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th class="text-right">Precio Mínimo</th>
                    <th class="text-right">Precio de Venta</th>
                <?php else: // general ?>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th class="text-right">Costo</th>
                    <th class="text-right">Venta</th>
                    <th class="text-right">Mínimo</th>
                    <th class="text-center">Stock</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($products)): ?>
                <tr>
                    <td colspan="7" class="text-center">No hay productos disponibles.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <?php if ($type === 'stock'): ?>
                            <td><?= esc($product['code']) ?></td>
                            <td><?= esc($product['name']) ?></td>
                            <td class="text-center"><?= $product['stock'] ?></td>
                            <td></td> <!-- Casilla en blanco para conteo -->
                        <?php elseif ($type === 'price'): ?>
                            <td><?= esc($product['code']) ?></td>
                            <td><?= esc($product['name']) ?></td>
                            <td class="text-right"><?= formato_moneda($product['min_sale_price'] ?? 0) ?></td>
                            <td class="text-right"><strong><?= formato_moneda($product['price']) ?></strong></td>
                        <?php else: // general ?>
                            <td><?= esc($product['code']) ?></td>
                            <td><?= esc($product['name']) ?></td>
                            <td><?= esc($product['category_name']) ?></td>
                            <td class="text-right"><?= formato_moneda($product['cost_price'] ?? 0) ?></td>
                            <td class="text-right"><strong><?= formato_moneda($product['price']) ?></strong></td>
                            <td class="text-right"><?= formato_moneda($product['min_sale_price'] ?? 0) ?></td>
                            <td class="text-center"><?= $product['stock'] ?></td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
