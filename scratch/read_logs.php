<?php
require 'config.php';
$stmt = db()->query('SELECT message, context, created_at FROM sys_error_logs ORDER BY id DESC LIMIT 10');
while($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "Time: " . $r['created_at'] . "\n";
    echo "Message: " . $r['message'] . "\n";
    echo "Context: " . $r['context'] . "\n";
    echo "-----------------------------------\n";
}
