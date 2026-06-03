<?php
header('Content-Type: text/plain');

$logFile = '/app/storage/logs/laravel.log';
if (!file_exists($logFile)) {
    die("No log file found at $logFile\n");
}

$lines = file($logFile);
$lastLines = array_slice($lines, -50);
echo "Last 50 lines of laravel.log:\n";
echo implode("", $lastLines);
?>
