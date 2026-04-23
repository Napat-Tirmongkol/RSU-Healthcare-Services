<?php
/**
 * portal/ajax_identity_users.php
 * Handles server-side search and pagination for Identity & Governance Users
 */
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/includes/auth.php'; // Ensure security

header('Content-Type: application/json');

$pdo = db();
$search   = trim($_GET['search'] ?? '');
$page     = max(1, (int)($_GET['page'] ?? 1));
$pageSize = max(10, min(100, (int)($_GET['pageSize'] ?? 25)));
$offset   = ($page - 1) * $pageSize;

try {
    // 1. Count total records for pagination
    $countSql = "SELECT COUNT(*) FROM sys_users WHERE 1=1";
    $countParams = [];
    if ($search !== '') {
        $countSql .= " AND (full_name LIKE :s OR student_personnel_id LIKE :s OR citizen_id LIKE :s OR email LIKE :s)";
        $countParams[':s'] = "%$search%";
    }
    $totalRecords = (int)$pdo->prepare($countSql)->execute($countParams) ? $pdo->prepare($countSql)->execute($countParams)->fetchColumn() : 0;
    // Re-do count properly
    $stmtCount = $pdo->prepare($countSql);
    $stmtCount->execute($countParams);
    $totalRecords = (int)$stmtCount->fetchColumn();

    // 2. Fetch records with LIMIT/OFFSET
    $sql = "SELECT id, full_name, student_personnel_id, citizen_id, phone_number, email, department, gender, status, status_other, created_at 
            FROM sys_users WHERE 1=1";
    $params = [];
    if ($search !== '') {
        $sql .= " AND (full_name LIKE :s OR student_personnel_id LIKE :s OR citizen_id LIKE :s OR email LIKE :s)";
        $params[':s'] = "%$search%";
    }
    $sql .= " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
    
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit', $pageSize, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    if ($search !== '') {
        $stmt->bindValue(':s', "%$search%", PDO::PARAM_STR);
    }
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 3. Format response
    echo json_encode([
        'status' => 'success',
        'data' => $users,
        'pagination' => [
            'total' => $totalRecords,
            'page' => $page,
            'pageSize' => $pageSize,
            'totalPages' => ceil($totalRecords / $pageSize)
        ]
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
