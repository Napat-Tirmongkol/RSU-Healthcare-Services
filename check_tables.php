<?php
require_once __DIR__ . '/config.php';
$pdo = db();
try {
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "TABLES_FOUND: " . implode(", ", $tables);
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
?>
