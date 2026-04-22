<?php
// user/diag.php — เจาะลึกจุดตายใน config.php
declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors', '1');

echo "<h2>Step 1: Probing config.php dependencies...</h2>";

echo "1.1 Checking db_connect.php... ";
$file = __DIR__ . '/../config/db_connect.php';
if (file_exists($file)) {
    require_once $file;
    echo "✅ Loaded.<br>";
} else {
    echo "❌ NOT FOUND: $file<br>";
}

echo "1.2 Checking csrf.php... ";
$file = __DIR__ . '/../includes/csrf.php';
if (file_exists($file)) {
    require_once $file;
    echo "✅ Loaded.<br>";
} else {
    echo "❌ NOT FOUND: $file<br>";
}

echo "1.3 Checking error_logger.php... ";
$file = __DIR__ . '/../includes/error_logger.php';
if (file_exists($file)) {
    require_once $file;
    echo "✅ Loaded.<br>";
} else {
    echo "❌ NOT FOUND: $file<br>";
}

echo "1.4 Checking sentry.php... ";
$file = __DIR__ . '/../config/sentry.php';
if (file_exists($file)) {
    require_once $file;
    echo "✅ Loaded.<br>";
} else {
    echo "❌ NOT FOUND: $file<br>";
}

echo "<h2>Step 2: Loading the rest of config.php...</h2>";
require_once __DIR__ . '/../config.php';
echo "✅ Full config.php loaded.<br>";

echo "<h2>All good!</h2>";
