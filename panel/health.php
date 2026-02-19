<?php
/**
 * Health Check Endpoint
 * 
 * Returns system health status for monitoring.
 * 
 * @package Panel
 * @author Chamalead
 * @version 1.0.0
 */

require_once 'auth.php';
require_once 'EvolutionApiService.php';

header('Content-Type: application/json');

$checks = [
    'timestamp' => date('c'),
    'status' => 'healthy',
    'checks' => []
];

// Database check
try {
    global $pdo;
    $pdo->query('SELECT 1');
    $checks['checks']['database'] = [
        'status' => 'ok',
        'response_time_ms' => 0
    ];
} catch (Exception $e) {
    $checks['checks']['database'] = [
        'status' => 'error',
        'message' => $e->getMessage()
    ];
    $checks['status'] = 'unhealthy';
}

// Evolution API check
$start = microtime(true);
try {
    $api = new EvolutionApiService();
    $apiHealthy = $api->healthCheck();
    $responseTime = round((microtime(true) - $start) * 1000, 2);
    
    $checks['checks']['evolution_api'] = [
        'status' => $apiHealthy ? 'ok' : 'error',
        'response_time_ms' => $responseTime
    ];
    
    if (!$apiHealthy) {
        $checks['status'] = 'degraded';
    }
} catch (Exception $e) {
    $checks['checks']['evolution_api'] = [
        'status' => 'error',
        'message' => $e->getMessage()
    ];
    $checks['status'] = 'unhealthy';
}

// Disk space check
$freeSpace = disk_free_space(__DIR__);
$totalSpace = disk_total_space(__DIR__);
$usedPercent = round((($totalSpace - $freeSpace) / $totalSpace) * 100, 2);

$checks['checks']['disk'] = [
    'status' => $usedPercent > 90 ? 'warning' : 'ok',
    'free_gb' => round($freeSpace / 1024 / 1024 / 1024, 2),
    'total_gb' => round($totalSpace / 1024 / 1024 / 1024, 2),
    'used_percent' => $usedPercent
];

if ($usedPercent > 95) {
    $checks['status'] = 'unhealthy';
} elseif ($usedPercent > 90 && $checks['status'] === 'healthy') {
    $checks['status'] = 'degraded';
}

// Memory check (if available)
if (function_exists('memory_get_usage')) {
    $memoryUsage = memory_get_usage(true);
    $memoryLimit = ini_get('memory_limit');
    $checks['checks']['memory'] = [
        'status' => 'ok',
        'usage_mb' => round($memoryUsage / 1024 / 1024, 2),
        'limit' => $memoryLimit
    ];
}

// PHP version check
$checks['checks']['php'] = [
    'status' => 'ok',
    'version' => PHP_VERSION
];

// Session status
$checks['checks']['session'] = [
    'status' => session_status() === PHP_SESSION_ACTIVE ? 'ok' : 'inactive',
    'authenticated' => is_authenticated()
];

// Response time
$checks['response_time_ms'] = round((microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']) * 1000, 2);

// HTTP status code
$httpStatus = $checks['status'] === 'healthy' ? 200 : ($checks['status'] === 'degraded' ? 200 : 503);
http_response_code($httpStatus);

echo json_encode($checks, JSON_PRETTY_PRINT);
