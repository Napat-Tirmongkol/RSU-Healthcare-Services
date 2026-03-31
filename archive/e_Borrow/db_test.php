<?php
// ไฟล์ทดสอบชั่วคราว — ลบทิ้งหลังใช้งาน
$DB_HOST = "localhost";
$DB_USER = "healthy";
$DB_PASS = "61r_pl6NmNoviy3aB";
$DB_NAME = "e_Borrow";
$DB_PORT = 3306;

try {
    $dsn = "mysql:host=$DB_HOST;dbname=$DB_NAME;port=$DB_PORT;charset=utf8mb4";
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    echo "✅ เชื่อมต่อ DB สำเร็จ (host=$DB_HOST, db=$DB_NAME, user=$DB_USER)";

    // ทดสอบ query ตาราง sys_staff
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM sys_staff");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<br>✅ ตาราง sys_staff มีข้อมูล: " . $row['total'] . " แถว";

} catch (PDOException $e) {
    echo "❌ เชื่อมต่อไม่ได้: " . $e->getMessage();
}
?>
