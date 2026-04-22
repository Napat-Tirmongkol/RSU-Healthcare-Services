<?php
// user/diag.php — เช็คตาราง Database ในเซิร์ฟเวอร์จริง
declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once __DIR__ . '/../config.php';
$pdo = db();

echo "<h2>Checking Database Tables...</h2>";
try {
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "<ul>";
    foreach ($tables as $t) echo "<li>$t</li>";
    echo "</ul>";

    echo "<h3>Checking sys_faculties specifically...</h3>";
    $stmt = $pdo->query("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'sys_faculties'");
    $exists = (int)$stmt->fetchColumn() > 0;
    if ($exists) {
        echo "✅ sys_faculties EXISTS.<br>";
        $stmt = $pdo->query("SELECT COUNT(*) FROM sys_faculties");
        echo "Row count: " . $stmt->fetchColumn() . "<br>";
    } else {
        echo "❌ sys_faculties MISSING!<br>";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
}

echo "<h2>Done!</h2>";
