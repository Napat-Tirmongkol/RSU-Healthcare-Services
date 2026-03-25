<?php
// includes/check_session.php
// สำหรับ "หน้าหลัก" (HTML Pages) ของระบบยืมคืนเดิม
// ปรับปรุงใหม่: รองรับ SSO จาก Hub Portal และระบบพนักงานกลาง

@session_start();

// --- [NEW] SSO Sync from Hub Portal ---
// เมื่อมีการ Login ผ่านระบบใหม่ ให้ Sync สิทธิ์เข้ามาที่ระบบยืมคืนโดยอัตโนมัติ
if (!isset($_SESSION['user_id']) && (isset($_SESSION['admin_logged_in']) || isset($_SESSION['admin_id']))) {
    try {
        require_once __DIR__ . '/../../../config/db_connect.php';
        $p = db();
        $uname = $_SESSION['admin_username'] ?? '';
        
        // ค้นหาใน sys_staff ว่ามีคนนี้ไหม
        $s = $p->prepare("SELECT id, full_name, role FROM sys_staff WHERE username = :u LIMIT 1");
        $s->execute([':u' => $uname]);
        $row = $s->fetch();
        
        if ($row) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['full_name'] = $row['full_name'];
            $_SESSION['role'] = $row['role'];
        } else {
            // กรณีเป็นแอดมินใน Portal แต่ไม่มีรายชื่อในโครงการยืมคืน (Staff) 
            // ให้ใช้สสิทธิ์ Admin เสมือนเพื่อให้ดูประวัติได้
            $_SESSION['user_id'] = $_SESSION['admin_id'] ?? 999;
            $_SESSION['full_name'] = $_SESSION['admin_username'] ?? 'Administrator';
            $_SESSION['role'] = 'admin';
        }
    } catch (Exception $e) {
        // เงียบไว้หาก DB ไม่พร้อม
    }
}

// 1. ระบบ Timeout (วินาที)
$timeout_duration = 36000; // เพิ่มเวลาเป็น 10 ชม. เพื่อความสะดวก

// 2. ตรวจสอบเงื่อนไข Timeout
if (isset($_SESSION['LAST_ACTIVITY'])) {
    if ((time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
        session_unset();     
        session_destroy();
        header("Location: ../admin/login.php?timeout=1"); 
        exit;
    }
}

// 3. รับรู้กิจกรรมล่าสุด
$_SESSION['LAST_ACTIVITY'] = time();

// 4. บังคับย้อนกลับไป Login หากไม่มีตัวตนในเซสชัน
if (!isset($_SESSION['user_id'])) {
    header("Location: ../admin/login.php");
    exit;
}
?>