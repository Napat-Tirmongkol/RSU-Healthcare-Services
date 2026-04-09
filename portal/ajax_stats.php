<?php
/**
 * portal/ajax_stats.php — lightweight JSON stats endpoint for dashboard polling
 * Returns immediately (no long-lived connection).
 */
declare(strict_types=1);
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(401);
    exit;
}

require_once __DIR__ . '/../config.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

try {
    $pdo = db();

    $users = (int)$pdo->query("SELECT COUNT(*) FROM sys_users")->fetchColumn();
    $camps = (int)$pdo->query("SELECT COUNT(*) FROM camp_list WHERE status = 'active'")->fetchColumn();

    $borrows = 0;
    if ($pdo->query("SHOW TABLES LIKE 'borrow_records'")->rowCount() > 0) {
        $borrows = (int)$pdo->query(
            "SELECT COUNT(*) FROM borrow_records WHERE approval_status = 'pending'"
        )->fetchColumn();
    }

    $activity = [];
    try {
        $activity = $pdo->query(
            "SELECT l.action, l.description, l.timestamp,
                    COALESCE(a.full_name, 'System') AS admin_name
             FROM   sys_activity_logs l
             LEFT   JOIN sys_admins a ON l.user_id = a.id
             ORDER  BY l.timestamp DESC
             LIMIT  5"
        )->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) { /* table may not exist */ }

    echo json_encode([
        'ok'       => true,
        'users'    => $users,
        'camps'    => $camps,
        'borrows'  => $borrows,
        'activity' => $activity,
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    echo json_encode(['ok' => false]);
}
