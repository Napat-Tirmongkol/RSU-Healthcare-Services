<?php
// user/diag.php — ค้นหาจุดตายของหน้า Profile
declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors', '1');

echo "<h2>Step 1: Loading config.php...</h2>";
require_once __DIR__ . '/../config.php';
echo "✅ config.php loaded.<br>";

echo "<h2>Step 2: Loading includes/lang.php...</h2>";
require_once __DIR__ . '/../includes/lang.php';
echo "✅ includes/lang.php loaded.<br>";

echo "<h2>Step 3: Checking functions...</h2>";
if (function_exists('__')) {
    echo "✅ function __() exists.<br>";
    echo "Test translation: " . __('profile.heading_edit') . "<br>";
} else {
    echo "❌ function __() NOT FOUND!<br>";
}

if (function_exists('db')) {
    echo "✅ function db() exists.<br>";
} else {
    echo "❌ function db() NOT FOUND!<br>";
}

echo "<h2>Step 4: Database Connection...</h2>";
try {
    $pdo = db();
    echo "✅ Database connected successfully.<br>";
} catch (Exception $e) {
    echo "❌ Database Error: " . $e->getMessage() . "<br>";
}

echo "<h2>Step 5: Done!</h2>";
