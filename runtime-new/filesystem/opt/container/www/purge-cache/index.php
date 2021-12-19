<?php

$dir = '/home/runtime/cache';

$it = new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS);
$files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);

foreach ($files as $file) {
    if ($file->isDir()) {
        rmdir($file->getRealPath());
    } else {
        unlink($file->getRealPath());
    }
}

echo "Cache purged!";
