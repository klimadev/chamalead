<?php
declare(strict_types=1);

header('Cache-Control: private, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Vary: Accept-Encoding');

require __DIR__ . '/partials/asset.php';
require __DIR__ . '/partials/head.php';
require __DIR__ . '/partials/layout.php';
require __DIR__ . '/partials/app-script.php';
