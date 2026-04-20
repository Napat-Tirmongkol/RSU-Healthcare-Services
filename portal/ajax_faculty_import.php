<?php
// portal/ajax_faculty_import.php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/includes/auth.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

validate_csrf_or_die();

// ── Parse XLSX using ZipArchive + SimpleXML ───────────────────────────────────
function parseXlsx(string $filePath): array
{
    $zip = new ZipArchive();
    if ($zip->open($filePath) !== true) {
        return [];
    }

    $sharedStrings = [];
    $ssXml = $zip->getFromName('xl/sharedStrings.xml');
    if ($ssXml !== false) {
        $ss = simplexml_load_string($ssXml);
        if ($ss) {
            foreach ($ss->si as $si) {
                if (isset($si->t)) {
                    $sharedStrings[] = (string)$si->t;
                } else {
                    $text = '';
                    foreach ($si->r as $r) {
                        $text .= (string)$r->t;
                    }
                    $sharedStrings[] = $text;
                }
            }
        }
    }

    $rows  = [];
    $wsXml = $zip->getFromName('xl/worksheets/sheet1.xml');
    if ($wsXml !== false) {
        $ws = simplexml_load_string($wsXml);
        if ($ws) {
            foreach ($ws->sheetData->row as $row) {
                $rowData = [];
                foreach ($row->c as $cell) {
                    $type = (string)$cell['t'];
                    $val  = (string)$cell->v;
                    if ($type === 's') {
                        $rowData[] = $sharedStrings[(int)$val] ?? '';
                    } elseif ($type === 'inlineStr') {
                        $rowData[] = (string)$cell->is->t;
                    } else {
                        $rowData[] = $val;
                    }
                }
                if (array_filter($rowData, fn($v) => trim($v) !== '')) {
                    $rows[] = $rowData;
                }
            }
        }
    }

    $zip->close();
    return $rows;
}

function parseCsv(string $filePath): array
{
    $rows = [];
    $fh   = fopen($filePath, 'r');
    if ($fh === false) {
        return [];
    }
    while (($row = fgetcsv($fh)) !== false) {
        if (array_filter($row, fn($v) => trim($v) !== '')) {
            $rows[] = array_map('trim', $row);
        }
    }
    fclose($fh);
    return $rows;
}

// ── Handle: delete all ───────────────────────────────────────────────────────
if (isset($_POST['action']) && $_POST['action'] === 'clear_all') {
    try {
        $pdo = db();
        $pdo->exec("DELETE FROM sys_faculties");
        log_activity('faculty_import', 'ลบข้อมูลคณะ/หน่วยงานทั้งหมด');
        echo json_encode(['status' => 'ok', 'message' => 'ลบข้อมูลทั้งหมดเรียบร้อย']);
    } catch (PDOException $e) {
        error_log("Faculty clear error: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
    }
    exit;
}

// ── Handle: file upload ──────────────────────────────────────────────────────
if (!isset($_FILES['faculty_file']) || $_FILES['faculty_file']['error'] !== UPLOAD_ERR_OK) {
    $errCode = $_FILES['faculty_file']['error'] ?? -1;
    echo json_encode(['status' => 'error', 'message' => "ไม่พบไฟล์หรืออัพโหลดล้มเหลว (code: $errCode)"]);
    exit;
}

$file     = $_FILES['faculty_file'];
$ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$tmpPath  = $file['tmp_name'];
$maxBytes = 5 * 1024 * 1024; // 5 MB

if ($file['size'] > $maxBytes) {
    echo json_encode(['status' => 'error', 'message' => 'ไฟล์ใหญ่เกิน 5 MB']);
    exit;
}

if (!in_array($ext, ['xlsx', 'csv'], true)) {
    echo json_encode(['status' => 'error', 'message' => 'รองรับเฉพาะไฟล์ .xlsx และ .csv เท่านั้น']);
    exit;
}

$rows = $ext === 'xlsx' ? parseXlsx($tmpPath) : parseCsv($tmpPath);

if (empty($rows)) {
    echo json_encode(['status' => 'error', 'message' => 'ไม่พบข้อมูลในไฟล์ หรือไฟล์มีรูปแบบไม่ถูกต้อง']);
    exit;
}

// Skip header row if first cell looks like a column name (not a faculty name)
$skipFirst = false;
$firstCell = strtolower(trim($rows[0][0] ?? ''));
if (in_array($firstCell, ['code', 'รหัส', 'name', 'ชื่อ', 'faculty', 'คณะ', 'department', 'หน่วยงาน', 'no', 'ลำดับ'], true)) {
    $skipFirst = true;
}
if ($skipFirst) {
    array_shift($rows);
}

if (empty($rows)) {
    echo json_encode(['status' => 'error', 'message' => 'ไม่พบข้อมูลหลังจากข้ามแถว Header']);
    exit;
}

// ── Ensure table exists ──────────────────────────────────────────────────────
try {
    $pdo = db();
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS sys_faculties (
            id         INT AUTO_INCREMENT PRIMARY KEY,
            code       VARCHAR(50)  NULL,
            name_th    VARCHAR(255) NOT NULL,
            name_en    VARCHAR(255) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY uk_name_th (name_th)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");
} catch (PDOException $e) {
    error_log("Faculty table create error: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'ไม่สามารถสร้างตารางได้: ' . $e->getMessage()]);
    exit;
}

// ── Insert / update rows ──────────────────────────────────────────────────────
// Expected columns: [code, name_th] or [name_th] or [name_th, name_en] or [code, name_th, name_en]
$stmt = $pdo->prepare("
    INSERT INTO sys_faculties (code, name_th, name_en)
    VALUES (:code, :name_th, :name_en)
    ON DUPLICATE KEY UPDATE
        code    = VALUES(code),
        name_en = VALUES(name_en),
        updated_at = CURRENT_TIMESTAMP
");

$inserted = 0;
$skipped  = 0;
$errors   = [];

foreach ($rows as $i => $row) {
    $cols = count($row);

    if ($cols === 0) { $skipped++; continue; }

    // Detect column layout
    if ($cols === 1) {
        $code    = null;
        $nameTh  = trim($row[0]);
        $nameEn  = null;
    } elseif ($cols === 2) {
        // Could be [code, name_th] or [name_th, name_en]
        // Heuristic: if first col looks like a code (short, alphanumeric) treat as code
        $isCode  = strlen(trim($row[0])) <= 20 && !preg_match('/[\x{0E00}-\x{0E7F}]/u', $row[0]);
        $code    = $isCode ? (trim($row[0]) ?: null) : null;
        $nameTh  = $isCode ? trim($row[1]) : trim($row[0]);
        $nameEn  = $isCode ? null : trim($row[1]);
    } else {
        $code    = trim($row[0]) ?: null;
        $nameTh  = trim($row[1]);
        $nameEn  = trim($row[2]) ?: null;
    }

    if ($nameTh === '') { $skipped++; continue; }

    try {
        $stmt->execute([':code' => $code, ':name_th' => $nameTh, ':name_en' => $nameEn]);
        $inserted++;
    } catch (PDOException $e) {
        $skipped++;
        if (count($errors) < 5) {
            $errors[] = "แถว " . ($i + 1) . ": " . $e->getMessage();
        }
    }
}

log_activity('faculty_import', "นำเข้าคณะ/หน่วยงาน {$inserted} รายการ จากไฟล์ " . htmlspecialchars($file['name']));

echo json_encode([
    'status'   => 'ok',
    'inserted' => $inserted,
    'skipped'  => $skipped,
    'errors'   => $errors,
    'message'  => "นำเข้าสำเร็จ {$inserted} รายการ" . ($skipped > 0 ? " (ข้ามไป {$skipped} รายการ)" : ''),
]);
