<?php
// vanilla_db_test.php
$host = '127.0.0.1';
$db   = 'e-campaignv2_db';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 5 // Timeout ใน 5 วินาทีถ้าติด Lock
    ]);
    echo "CONNECTED\n";
    $stmt = $pdo->query("SHOW TABLES");
    while($row = $stmt->fetch()) {
        echo $row[0] . "\n";
    }
} catch (Exception $e) {
    echo "FAIL: " . $e->getMessage();
}
?>
