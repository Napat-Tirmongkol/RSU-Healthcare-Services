<?php
require_once __DIR__ . '/config.php';
$pdo = db();
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'sys_activity_logs'");
    if ($stmt->rowCount() > 0) {
        echo "TABLE_EXISTS";
    } else {
        echo "TABLE_NOT_FOUND";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
?>
