<?php
/**
 * portal/queries/identity_queries.php
 * Fetches data for Identity & Governance (Users, Admins, Staff)
 */

$idSearch = $_GET['id_search'] ?? '';

// (0b) IDENTITY SECTION — USER QUERY
// [REFACTORED] Now handled via AJAX in portal/ajax_identity_users.php to prevent performance issues
$idUsers = []; 
$totalIdUsers = (int) $pdo->query("SELECT COUNT(*) FROM sys_users")->fetchColumn();

$idActiveCount = (int) $pdo->query("
    SELECT COUNT(DISTINCT id) FROM sys_users
    WHERE id IN (SELECT student_id FROM camp_bookings WHERE student_id IS NOT NULL)
")->fetchColumn();

// (0c) IDENTITY SECTION — ADMINS & STAFF QUERY (Superadmin Only)
$allAdmins = [];
$allStaff  = [];

if ($adminRole === 'superadmin') {
    $allAdmins = $pdo->query("SELECT * FROM sys_admins ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
    try {
        $allStaff = $pdo->query("
            SELECT id, username, full_name, role, account_status, linked_line_user_id,
                   IFNULL(access_ecampaign, 0) AS access_ecampaign,
                   IFNULL(ecampaign_role, 'admin') AS ecampaign_role
            FROM sys_staff ORDER BY role ASC, full_name ASC
        ")->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) { /* silent */ }
}
