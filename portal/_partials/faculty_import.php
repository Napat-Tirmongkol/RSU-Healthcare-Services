<?php
// portal/_partials/faculty_import.php — included by portal/index.php
// Requires config.php already loaded by portal

$pdo = db();

$faculties = [];
$tableExists = false;
try {
    $check = $pdo->query("SHOW TABLES LIKE 'sys_faculties'");
    if ($check && $check->rowCount() > 0) {
        $tableExists = true;
        $faculties = $pdo->query("SELECT id, code, name_th, name_en FROM sys_faculties ORDER BY name_th ASC")->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    error_log("Faculty list fetch error: " . $e->getMessage());
}
?>
<style>
.fi-card{background:#fff;border:1.5px solid #e2e8f0;border-radius:1.25rem;padding:1.5rem;margin-bottom:1.25rem}
.fi-label{display:block;font-size:.7rem;font-weight:900;color:#94a3b8;text-transform:uppercase;letter-spacing:.1em;margin-bottom:.5rem}
#fi-drop-zone{border:2.5px dashed #cbd5e1;border-radius:1rem;padding:2.5rem 1.5rem;text-align:center;cursor:pointer;transition:all .25s;background:#f8fafc}
#fi-drop-zone.drag-over{border-color:#3b82f6;background:#eff6ff}
#fi-drop-zone.file-chosen{border-color:#10b981;background:#f0fdf4}
.fi-table{width:100%;border-collapse:separate;border-spacing:0;font-size:13px}
.fi-table th{background:#f1f5f9;font-size:11px;font-weight:800;color:#64748b;text-transform:uppercase;letter-spacing:.07em;padding:10px 14px;text-align:left}
.fi-table th:first-child{border-radius:10px 0 0 10px}
.fi-table th:last-child{border-radius:0 10px 10px 0}
.fi-table td{padding:10px 14px;border-bottom:1px solid #f1f5f9;color:#1e293b;font-weight:600;vertical-align:middle}
.fi-table tr:last-child td{border-bottom:none}
.fi-badge{display:inline-block;font-size:11px;font-weight:700;padding:2px 8px;border-radius:6px;background:#f1f5f9;color:#64748b;font-family:monospace}
</style>

<div class="p-6 max-w-4xl">
    <div class="mb-6">
        <h2 class="text-2xl font-black text-gray-900 flex items-center gap-2">
            <i class="fa-solid fa-building-columns text-purple-500"></i> นำเข้าคณะ / หน่วยงาน
        </h2>
        <p class="text-xs text-gray-400 mt-1">อัพโหลดไฟล์ Excel (.xlsx) หรือ CSV เพื่อนำเข้ารายชื่อคณะและหน่วยงานในระบบ</p>
    </div>

    <!-- Upload Card -->
    <div class="fi-card">
        <div class="fi-label"><i class="fa-solid fa-file-arrow-up mr-1"></i> อัพโหลดไฟล์</div>

        <!-- Format Guide -->
        <div class="mb-4 p-3 rounded-xl bg-blue-50 border border-blue-100 text-xs text-blue-700 font-semibold leading-relaxed">
            <strong>รูปแบบไฟล์ที่รองรับ:</strong> .xlsx, .csv (ขนาดไม่เกิน 5 MB)<br>
            <strong>คอลัมน์แนะนำ:</strong>
            <code class="bg-blue-100 px-1 rounded">รหัส | ชื่อคณะ/หน่วยงาน (TH) | ชื่อคณะ/หน่วยงาน (EN)</code> — คอลัมน์แรกหรือแถวแรกเป็น Header จะถูกข้ามอัตโนมัติ
        </div>

        <div id="fi-drop-zone" onclick="document.getElementById('fi-file-input').click()">
            <div id="fi-drop-icon" class="text-4xl text-gray-300 mb-3"><i class="fa-solid fa-cloud-arrow-up"></i></div>
            <p id="fi-drop-text" class="text-gray-500 font-semibold text-sm">คลิก หรือลากไฟล์มาวางที่นี่</p>
            <p class="text-gray-400 text-xs mt-1">.xlsx / .csv</p>
        </div>
        <input type="file" id="fi-file-input" accept=".xlsx,.csv" class="hidden">

        <div id="fi-file-info" class="hidden mt-3 flex items-center gap-3 p-3 bg-emerald-50 border border-emerald-100 rounded-xl">
            <i class="fa-solid fa-file-excel text-emerald-500 text-xl"></i>
            <div class="flex-1 min-w-0">
                <p id="fi-file-name" class="font-bold text-sm text-gray-900 truncate"></p>
                <p id="fi-file-size" class="text-xs text-gray-500"></p>
            </div>
            <button onclick="clearFile()" class="text-gray-400 hover:text-red-500 transition-colors">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="mt-4 flex gap-3">
            <button id="fi-upload-btn" onclick="uploadFile()" disabled
                class="flex-1 bg-purple-600 hover:bg-purple-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-bold py-3 px-5 rounded-xl transition-all text-sm flex items-center justify-center gap-2">
                <i class="fa-solid fa-file-import"></i> นำเข้าข้อมูล
            </button>
            <?php if ($tableExists && count($faculties) > 0): ?>
            <button onclick="clearAll()"
                class="bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 font-bold py-3 px-4 rounded-xl transition-all text-sm flex items-center gap-2">
                <i class="fa-solid fa-trash"></i> ล้างทั้งหมด
            </button>
            <?php endif; ?>
        </div>

        <!-- Progress / Result -->
        <div id="fi-progress" class="hidden mt-4">
            <div class="h-2 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full bg-purple-500 rounded-full animate-pulse" style="width:60%"></div>
            </div>
            <p class="text-xs text-gray-500 mt-2 text-center">กำลังประมวลผล...</p>
        </div>
        <div id="fi-result" class="hidden mt-4 p-4 rounded-xl text-sm font-semibold"></div>
    </div>

    <!-- Current Data Table -->
    <div class="fi-card">
        <div class="fi-label">
            <i class="fa-solid fa-list mr-1"></i> รายชื่อคณะ / หน่วยงานในระบบ
            <span class="ml-2 bg-purple-100 text-purple-700 text-[10px] font-black px-2 py-0.5 rounded-full" id="fi-count"><?= count($faculties) ?> รายการ</span>
        </div>

        <?php if (!$tableExists || count($faculties) === 0): ?>
            <div class="text-center py-12 text-gray-400">
                <i class="fa-solid fa-building-columns text-5xl mb-3 opacity-30"></i>
                <p class="font-semibold text-sm">ยังไม่มีข้อมูลในระบบ</p>
                <p class="text-xs mt-1">อัพโหลดไฟล์เพื่อนำเข้าข้อมูล</p>
            </div>
        <?php else: ?>
            <div class="overflow-x-auto -mx-1" id="fi-table-wrap">
                <table class="fi-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>รหัส</th>
                            <th>ชื่อ (ภาษาไทย)</th>
                            <th>ชื่อ (English)</th>
                        </tr>
                    </thead>
                    <tbody id="fi-tbody">
                        <?php foreach ($faculties as $i => $f): ?>
                            <tr>
                                <td class="text-gray-400 text-xs"><?= $i + 1 ?></td>
                                <td><?= $f['code'] ? '<span class="fi-badge">' . htmlspecialchars($f['code']) . '</span>' : '<span class="text-gray-300">—</span>' ?></td>
                                <td><?= htmlspecialchars($f['name_th']) ?></td>
                                <td class="text-gray-500"><?= htmlspecialchars($f['name_en'] ?? '') ?: '<span class="text-gray-300">—</span>' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
(function () {
    const dropZone  = document.getElementById('fi-drop-zone');
    const fileInput = document.getElementById('fi-file-input');
    const fileInfo  = document.getElementById('fi-file-info');
    const fileName  = document.getElementById('fi-file-name');
    const fileSize  = document.getElementById('fi-file-size');
    const uploadBtn = document.getElementById('fi-upload-btn');
    const progress  = document.getElementById('fi-progress');
    const result    = document.getElementById('fi-result');

    dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.classList.add('drag-over'); });
    dropZone.addEventListener('dragleave', () => dropZone.classList.remove('drag-over'));
    dropZone.addEventListener('drop', e => {
        e.preventDefault();
        dropZone.classList.remove('drag-over');
        const f = e.dataTransfer.files[0];
        if (f) applyFile(f);
    });

    fileInput.addEventListener('change', () => {
        if (fileInput.files[0]) applyFile(fileInput.files[0]);
    });

    function applyFile(f) {
        const ext = f.name.split('.').pop().toLowerCase();
        if (!['xlsx', 'csv'].includes(ext)) {
            showResult('error', 'รองรับเฉพาะไฟล์ .xlsx และ .csv เท่านั้น');
            return;
        }
        fileName.textContent = f.name;
        fileSize.textContent = (f.size / 1024).toFixed(1) + ' KB';
        fileInfo.classList.remove('hidden');
        dropZone.classList.add('file-chosen');
        uploadBtn.disabled = false;
        result.classList.add('hidden');
    }

    window.clearFile = function () {
        fileInput.value = '';
        fileInfo.classList.add('hidden');
        dropZone.classList.remove('file-chosen');
        uploadBtn.disabled = true;
        result.classList.add('hidden');
    };

    window.uploadFile = async function () {
        if (!fileInput.files[0]) return;
        uploadBtn.disabled = true;
        progress.classList.remove('hidden');
        result.classList.add('hidden');

        const fd = new FormData();
        fd.append('faculty_file', fileInput.files[0]);
        fd.append('csrf_token', '<?= htmlspecialchars(get_csrf_token()) ?>');

        try {
            const res = await fetch('ajax_faculty_import.php', { method: 'POST', body: fd });
            const data = await res.json();
            progress.classList.add('hidden');
            uploadBtn.disabled = false;
            if (data.status === 'ok') {
                showResult('ok', data.message);
                if (data.inserted > 0) setTimeout(() => location.reload(), 1200);
            } else {
                showResult('error', data.message || 'เกิดข้อผิดพลาด');
            }
        } catch (e) {
            progress.classList.add('hidden');
            uploadBtn.disabled = false;
            showResult('error', 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้');
        }
    };

    window.clearAll = async function () {
        if (!confirm('ยืนยันลบข้อมูลคณะ/หน่วยงานทั้งหมดออกจากระบบ?')) return;
        const fd = new FormData();
        fd.append('action', 'clear_all');
        fd.append('csrf_token', '<?= htmlspecialchars(get_csrf_token()) ?>');
        try {
            const res  = await fetch('ajax_faculty_import.php', { method: 'POST', body: fd });
            const data = await res.json();
            if (data.status === 'ok') { location.reload(); }
            else { showResult('error', data.message); }
        } catch (e) {
            showResult('error', 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้');
        }
    };

    function showResult(type, msg) {
        result.classList.remove('hidden', 'bg-emerald-50', 'border-emerald-100', 'text-emerald-700', 'bg-red-50', 'border-red-100', 'text-red-700');
        if (type === 'ok') {
            result.classList.add('bg-emerald-50', 'border', 'border-emerald-100', 'text-emerald-700');
            result.innerHTML = '<i class="fa-solid fa-circle-check mr-2"></i>' + msg;
        } else {
            result.classList.add('bg-red-50', 'border', 'border-red-100', 'text-red-700');
            result.innerHTML = '<i class="fa-solid fa-triangle-exclamation mr-2"></i>' + msg;
        }
    }
})();
</script>
