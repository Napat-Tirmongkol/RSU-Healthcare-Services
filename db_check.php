<?php
require_once __DIR__ . '/config/db_connect.php';
$pdo = db();

function desc($table) {
    global $pdo;
    echo "--- Table: $table ---\n";
    $stmt = $pdo->query("DESC $table");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "{$row['Field']} ({$row['Type']})\n";
    }
    echo "\n";
}

try {
    desc('borrow_records');
    desc('borrow_items');
    desc('sys_users');
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
?>
