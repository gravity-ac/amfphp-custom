<?php

declare(strict_types=1);

$roots = [__DIR__ . '/../amfphp', __DIR__ . '/../Tests'];
$failed = false;

foreach ($roots as $root) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root));
    foreach ($iterator as $file) {
        if (!$file->isFile() || $file->getExtension() !== 'php') {
            continue;
        }

        $command = escapeshellarg(PHP_BINARY) . ' -d error_reporting=E_ALL -l ' . escapeshellarg($file->getPathname());
        passthru($command, $exitCode);
        $failed = $failed || $exitCode !== 0;
    }
}

exit($failed ? 1 : 0);
