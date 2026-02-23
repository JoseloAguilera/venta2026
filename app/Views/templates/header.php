<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema Ventas 2026">
    <title><?= isset($title) ? $title . ' - ' : '' ?>Sistema Ventas 2026</title>

    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="<?= base_url('assets/css/main.css') ?>">
    <?php if (isset($extraCSS)): ?>
        <?php foreach ($extraCSS as $css): ?>
            <?php if (strpos($css, 'http') === 0): ?>
                <link rel="stylesheet" href="<?= $css ?>">
            <?php else: ?>
                <link rel="stylesheet" href="<?= base_url($css) ?>">
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>

    <style>
        /* Fix DataTables Pagination Alignment & Styling */
        .dataTables_wrapper .dataTables_paginate {
            float: right !important;
            text-align: right !important;
            padding-top: 0.25em !important;
        }

        .dataTables_wrapper .dataTables_paginate .pagination {
            display: flex !important;
            justify-content: flex-end !important;
            padding-left: 0 !important;
            list-style: none !important;
            margin: 2px 0 !important;
            border-radius: 0.25rem !important;
        }

        .dataTables_wrapper .dataTables_paginate .page-item {
            display: inline-block !important;
            margin-left: 0.25rem !important;
        }

        .dataTables_wrapper .dataTables_paginate .page-link {
            position: relative !important;
            display: block !important;
            padding: 0.5rem 0.75rem !important;
            margin-left: -1px !important;
            line-height: 1.25 !important;
            color: #4b5563 !important;
            /* gray-600 */
            background-color: #fff !important;
            border: 1px solid #d1d5db !important;
            /* gray-300 */
            border-radius: 0.375rem !important;
            text-decoration: none !important;
            font-size: 0.875rem !important;
            font-weight: 500 !important;
            transition: all 0.2s ease-in-out !important;
        }

        .dataTables_wrapper .dataTables_paginate .page-link:hover {
            z-index: 2 !important;
            color: #6366f1 !important;
            /* primary */
            background-color: #f9fafb !important;
            /* gray-50 */
            border-color: #6366f1 !important;
        }

        .dataTables_wrapper .dataTables_paginate .page-item.active .page-link {
            z-index: 3 !important;
            color: #fff !important;
            background-color: #6366f1 !important;
            /* primary */
            border-color: #6366f1 !important;
            background-image: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%) !important;
        }

        .dataTables_wrapper .dataTables_paginate .page-item.disabled .page-link {
            color: #9ca3af !important;
            /* gray-400 */
            pointer-events: none !important;
            cursor: not-allowed !important;
            background-color: #f3f4f6 !important;
            /* gray-100 */
            border-color: #e5e7eb !important;
            /* gray-200 */
        }

        /* Mobile adjustment */
        @media (max-width: 768px) {
            .dataTables_wrapper .dataTables_paginate {
                float: none !important;
                text-align: center !important;
                justify-content: center !important;
                margin-top: 1rem !important;
            }

            .dataTables_wrapper .dataTables_paginate .pagination {
                justify-content: center !important;
            }
        }
    </style>
</head>

<body>