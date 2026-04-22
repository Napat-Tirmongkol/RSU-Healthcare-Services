<?php
// user/diag.php — เจาะลึกขั้นสุด: ระบบภาษาและหน้า Profile
declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors', '1');

echo "<h2>Step 1: Loading Core Config...</h2>";
require_once __DIR__ . '/../config.php';
echo "✅ config.php loaded.<br>";

echo "<h2>Step 2: Probing Language System...</h2>";
$lang_file = __DIR__ . '/../includes/lang.php';
if (file_exists($lang_file)) {
    require_once $lang_file;
    echo "✅ includes/lang.php loaded.<br>";
    echo "Testing __() function: " . __('profile.heading_edit') . "<br>";
} else {
    echo "❌ NOT FOUND: $lang_file<br>";
}

echo "<h2>Step 3: Probing Profile dependencies...</h2>";
// Check if any other includes in profile.php might be failing
echo "Testing check_maintenance... ";
if (function_exists('check_maintenance')) {
    echo "✅ function exists.<br>";
} else {
    echo "❌ function NOT FOUND!<br>";
}

echo "<h2>Step 4: Attempting to 'dry-run' profile.php logic...</h2>";
try {
    $lineUserId = 'test_id'; // Dummy for testing
    $pdo = db();
    $stmt = $pdo->prepare("SELECT * FROM sys_users LIMIT 1");
    $stmt->execute();
    $test_user = $stmt->fetch();
    echo "✅ Database query test successful.<br>";
} catch (Exception $e) {
    echo "❌ Logic Error: " . $e->getMessage() . "<br>";
}

echo "<h2>All checks passed!</h2>";
echo "<p>If you still see 500 on profile.php, it might be a Syntax Error in profile.php itself that prevents it from even starting.</p>";
