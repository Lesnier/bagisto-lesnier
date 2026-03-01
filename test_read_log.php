<?php
$logFilePath = 'storage/logs/laravel.log';

if (!file_exists($logFilePath)) {
    die("Log file not found.\n");
}

$contents = file_get_contents($logFilePath);
preg_match_all('/^\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\] local\.ERROR:(.+?)(?=\n\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\]|\z)/ms', $contents, $matches, PREG_SET_ORDER);

if (empty($matches)) {
    echo "No errors found.\n";
    exit;
}

$lastError = end($matches);
echo "TIMESTAMP: " . $lastError[1] . "\n";
echo "ERROR: " . substr($lastError[2], 0, 1500) . "\n";
