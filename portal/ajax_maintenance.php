<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/includes/auth.php';

header('Content-Type: application/json');

$ALLOWED_PROJECTS = ['e_campaign', 'e_borrow'];
$FILE = __DIR__ . '/../config/maintenance.json';

function loadMaintenance(string $file): array {
    if (!file_exists($file)) return [];
    $data = json_decode(file_get_contents($file), true);
    return is_array($data) ? $data : [];
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

// GET: ดึงสถานะทั้งหมด
if ($action === 'get') {
    echo json_encode(['ok' => true, 'data' => loadMaintenance($FILE)]);
    exit;
}

// POST: อัปเดตสถานะโปรเจกต์
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'set') {
    validate_csrf_or_die();

    $project = trim($_POST['project'] ?? '');
    $active  = filter_var($_POST['active'] ?? '1', FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

    if (!in_array($project, $ALLOWED_PROJECTS, true) || $active === null) {
        http_response_code(400);
        echo json_encode(['ok' => false, 'message' => 'invalid input']);
        exit;
    }

    $data = loadMaintenance($FILE);
    $data[$project] = $active;   // true = เปิดใช้งาน, false = ปรับปรุง
    file_put_contents($FILE, json_encode($data, JSON_PRETTY_PRINT));

    $label = $active ? 'เปิดใช้งาน' : 'ปิดปรับปรุง';
    log_activity('Maintenance Toggle', "$project → $label");

    echo json_encode(['ok' => true, 'project' => $project, 'active' => $active]);
    exit;
}

http_response_code(400);
echo json_encode(['ok' => false, 'message' => 'bad request']);
