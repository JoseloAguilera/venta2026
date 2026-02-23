<?php
// Script to restructure project for shared hosting compatibility
// Moves contents of public/ to root/ and updates index.php

$rootDir = __DIR__;
$publicDir = $rootDir . '/public';

echo "Moving files from public/ to root/...\n";

if (!is_dir($publicDir)) {
    die("Error: public/ directory not found.\n");
}

$files = scandir($publicDir);
foreach ($files as $file) {
    if ($file === '.' || $file === '..')
        continue;

    $source = $publicDir . '/' . $file;
    $dest = $rootDir . '/' . $file;

    if (file_exists($dest)) {
        echo "Warning: Destination $file already exists. Skipping or merging...\n";
    }

    if (rename($source, $dest)) {
        echo "Moved: $file\n";
    } else {
        echo "Failed to move: $file\n";
    }
}

// Update index.php
$indexFile = $rootDir . '/index.php';
if (file_exists($indexFile)) {
    echo "Updating index.php...\n";
    $content = file_get_contents($indexFile);

    // Change path to Paths.php
    $content = str_replace(
        "require FCPATH . '../app/Config/Paths.php';",
        "require FCPATH . 'app/Config/Paths.php';",
        $content
    );

    file_put_contents($indexFile, $content);
    echo "index.php updated.\n";
} else {
    echo "Error: index.php not found in root after move.\n";
}

// Remove empty public dir
if (count(scandir($publicDir)) == 2) {
    rmdir($publicDir);
    echo "Removed empty public/ directory.\n";
} else {
    echo "public/ directory not empty, keeping it.\n";
}

echo "Restructuring complete.\n";
