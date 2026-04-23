<?php

if (!function_exists('formato_moneda')) {
    /**
     * Formatea un número según la moneda configurada en el sistema.
     *
     * @param float|string $monto
     * @return string
     */
    function formato_moneda($monto) {
        static $currencyConfig = null;
        
        if ($currencyConfig === null) {
            $settings = model('App\Models\SettingsModel');
            $currencyConfig = $settings->getValue('currency', 'USD');
        }
        
        $monto = is_numeric($monto) ? (float)$monto : 0.0;
        
        if ($currencyConfig == 'PYG') {
            return 'Gs. ' . number_format($monto, 0, ',', '.');
        } else {
            return '$' . number_format($monto, 2, '.', ',');
        }
    }
}
