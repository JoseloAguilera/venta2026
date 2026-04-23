<?php
$directory = new RecursiveDirectoryIterator('c:/wamp64/www/venta2026/app/Views');
$iterator = new RecursiveIteratorIterator($directory);
foreach ($iterator as $info) {
    if ($info->getExtension() == 'php') {
        $content = file_get_contents($info->getPathname());
        $original = $content;
        
        $pattern = '/\\$?\\s*<\\?=\\s*number_format\\(([^,]+)(?:,\\s*[0-9]+.*?|)\\)\\s*\\?>/';
        $replacement = '<?=' . ' formato_moneda($1) ' . '?>';
        
        $content = preg_replace($pattern, $replacement, $content);
        
        if ($content !== $original) {
            file_put_contents($info->getPathname(), $content);
            echo "Updated: " . $info->getPathname() . "\n";
        }
    }
}
