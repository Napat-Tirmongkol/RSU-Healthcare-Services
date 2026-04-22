<?php
// user/diag.php — อ่านบันทึกความผิดพลาดจากฐานข้อมูล
declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once __DIR__ . '/../config.php';
$pdo = db();

echo "<h2>System Error Log Analysis</h2>";
try {
    // ดึง 10 รายการล่าสุด
    $stmt = $pdo->query("SELECT * FROM sys_error_logs ORDER BY created_at DESC LIMIT 10");
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($logs)) {
        echo "✅ No errors found in sys_error_logs table.<br>";
    } else {
        echo "<table border='1' cellpadding='8' style='border-collapse:collapse; font-size:12px;'>";
        echo "<tr style='background:#f1f1f1;'><th>Time</th><th>Level</th><th>Message</th><th>File:Line</th></tr>";
        foreach ($logs as $l) {
            echo "<tr>";
            echo "<td>{$l['created_at']}</td>";
            echo "<td>" . htmlspecialchars((string)$l['error_level']) . "</td>";
            echo "<td style='color:red; font-weight:bold;'>" . htmlspecialchars((string)$l['error_message']) . "</td>";
            echo "<td>" . htmlspecialchars((string)$l['error_file']) . ":{$l['error_line']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} catch (Exception $e) {
    echo "❌ Error reading sys_error_logs: " . $e->getMessage() . "<br>";
}

echo "<h2>Done!</h2>";
