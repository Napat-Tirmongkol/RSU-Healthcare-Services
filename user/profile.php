<?php
// user/profile.php — Premium Profile Management (Production)
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../config.php';
check_maintenance('e_campaign');

$lineUserId = $_SESSION['line_user_id'] ?? '';
if ($lineUserId === '') {
    header('Location: index.php');
    exit;
}

$userData = [
    'prefix' => '', 'first_name' => '', 'last_name' => '', 'full_name' => '',
    'id_number' => '', 'citizen_id' => '', 'phone' => '', 'status' => '',
    'email' => '', 'gender' => '', 'department' => '',
];

try {
    $pdo = db();
    $stmt = $pdo->prepare("SELECT * FROM sys_users WHERE line_user_id = :line_id LIMIT 1");
    $stmt->execute([':line_id' => $lineUserId]);
    $user = $stmt->fetch();

    if ($user) {
        $userData = [
            'prefix' => $user['prefix'] ?? '',
            'first_name' => $user['first_name'] ?? '',
            'last_name' => $user['last_name'] ?? '',
            'full_name' => $user['full_name'] ?? '',
            'id_number' => $user['student_personnel_id'] ?? '',
            'citizen_id' => $user['citizen_id'] ?? '',
            'phone' => $user['phone_number'] ?? '',
            'status' => $user['status'] ?? '',
            'email' => $user['email'] ?? '',
            'gender' => $user['gender'] ?? '',
            'department' => $user['department'] ?? '',
        ];
    }
} catch (PDOException $e) {
    error_log("Profile fetch error: " . $e->getMessage());
}

$isEditing = !empty($userData['full_name']);
$redirectBack = $_GET['redirect_back'] ?? 'hub.php';

// Completeness
$completenessPercent = 0;
if ($isEditing) {
    $items = [
        !empty($userData['prefix']), !empty($userData['first_name']), !empty($userData['last_name']),
        !empty($userData['phone']), !empty($userData['gender']), !empty($userData['citizen_id'])
    ];
    $completenessPercent = (int)round((count(array_filter($items)) / count($items)) * 100);
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>ข้อมูลส่วนตัว - RSU Medical</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        @font-face {
            font-family: 'RSU';
            src: url('../assets/fonts/RSU_Regular.ttf') format('truetype');
            font-weight: normal;
            font-style: normal;
        }
        @font-face {
            font-family: 'RSU';
            src: url('../assets/fonts/RSU_BOLD.ttf') format('truetype');
            font-weight: bold;
            font-style: normal;
        }
        body { font-family: 'RSU', sans-serif; background-color: #F8FAFF; -webkit-tap-highlight-color: transparent; }
        .glass-header { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); }
        .input-premium { @apply w-full px-5 py-4 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-blue-50 focus:border-blue-400 outline-none transition-all font-bold text-slate-900 placeholder:text-slate-300 shadow-sm text-base; }
        .label-premium { @apply block text-[14px] font-black text-slate-700 uppercase tracking-wide mb-2 ml-1; }
    </style>
</head>
<body class="text-slate-900 pb-20">

    <div class="max-w-md mx-auto relative min-h-screen">
        
        <!-- ── Navigation Header ── -->
        <header class="glass-header sticky top-0 z-[60] px-6 py-5 flex items-center justify-between border-b border-slate-100 shadow-sm shadow-slate-50">
            <button onclick="window.location.href='<?= $redirectBack ?>'" class="w-11 h-11 flex items-center justify-center bg-slate-50 rounded-2xl text-slate-400 active:scale-90 transition-all">
                <i class="fa-solid fa-chevron-left"></i>
            </button>
            <h1 class="text-lg font-black text-slate-900 tracking-tight">แก้ไขข้อมูลส่วนตัว</h1>
            <div class="w-11 h-11"></div>
        </header>

        <main class="px-6 pt-10 space-y-10">
            
            <!-- ── Profile Progress ── -->
            <div class="bg-white rounded-[2.5rem] p-8 border border-slate-50 shadow-[0_20px_40px_rgba(0,0,0,0.02)] relative overflow-hidden">
                <div class="absolute -right-6 -top-6 w-32 h-32 bg-blue-50 rounded-full blur-3xl opacity-50"></div>
                <div class="relative z-10 flex items-center justify-between mb-4">
                    <div>
                        <h2 class="text-slate-900 font-black text-xl tracking-tight">ความสมบูรณ์</h2>
                        <p class="text-slate-400 text-[10px] font-black uppercase tracking-widest">Profile Completeness</p>
                    </div>
                    <span class="text-2xl font-black text-blue-600"><?= $completenessPercent ?>%</span>
                </div>
                <div class="h-3 w-full bg-slate-50 rounded-full overflow-hidden border border-slate-100 p-0.5">
                    <div class="h-full bg-blue-600 rounded-full transition-all duration-1000 shadow-[0_0_15px_rgba(0,82,204,0.3)]" style="width: <?= $completenessPercent ?>%"></div>
                </div>
            </div>

            <!-- ── Information Form ── -->
            <form action="save_profile.php" method="POST" class="space-y-8 pb-10">
                <?php csrf_field(); ?>
                
                <div class="space-y-6">
                    <div class="flex items-center gap-3 px-2">
                        <div class="w-1.5 h-6 bg-blue-600 rounded-full"></div>
                        <h3 class="text-slate-900 font-black text-lg tracking-tight">ข้อมูลพื้นฐาน</h3>
                    </div>

                    <div class="space-y-5">
                        <!-- Prefix -->
                        <div>
                            <label class="label-premium">คำนำหน้าชื่อ</label>
                            <div class="relative">
                                <select name="name_title" class="input-premium appearance-none pr-10">
                                    <option value="นาย" <?= $userData['prefix'] === 'นาย' ? 'selected' : '' ?>>นาย</option>
                                    <option value="นาง" <?= $userData['prefix'] === 'นาง' ? 'selected' : '' ?>>นาง</option>
                                    <option value="นางสาว" <?= $userData['prefix'] === 'นางสาว' ? 'selected' : '' ?>>นางสาว</option>
                                </select>
                                <div class="absolute right-5 top-1/2 -translate-y-1/2 pointer-events-none text-slate-300">
                                    <i class="fa-solid fa-chevron-down text-xs"></i>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="label-premium">ชื่อจริง</label>
                                <input type="text" name="first_name" value="<?= htmlspecialchars($userData['first_name']) ?>" class="input-premium" placeholder="First Name">
                            </div>
                            <div>
                                <label class="label-premium">นามสกุล</label>
                                <input type="text" name="last_name" value="<?= htmlspecialchars($userData['last_name']) ?>" class="input-premium" placeholder="Last Name">
                            </div>
                        </div>

                        <div>
                            <label class="label-premium">เพศ</label>
                            <div class="grid grid-cols-3 gap-3">
                                <?php foreach(['male' => 'ชาย', 'female' => 'หญิง', 'other' => 'อื่นๆ'] as $val => $lbl): ?>
                                <label class="cursor-pointer group">
                                    <input type="radio" name="gender" value="<?= $val ?>" class="hidden peer" <?= $userData['gender'] === $val ? 'checked' : '' ?>>
                                    <div class="py-4 text-center rounded-2xl border border-slate-200 bg-white font-bold text-sm text-slate-500 peer-checked:bg-blue-600 peer-checked:text-white peer-checked:border-blue-600 peer-checked:shadow-lg peer-checked:shadow-blue-100 transition-all active:scale-95">
                                        <?= $lbl ?>
                                    </div>
                                </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-6 pt-4">
                    <div class="flex items-center gap-3 px-2">
                        <div class="w-1.5 h-6 bg-emerald-500 rounded-full"></div>
                        <h3 class="text-slate-900 font-black text-lg tracking-tight">ข้อมูลสถานะ</h3>
                    </div>

                    <div class="space-y-5">
                        <div>
                            <label class="label-premium">ประเภทผู้ใช้งาน</label>
                            <div class="grid grid-cols-3 gap-3">
                                <?php foreach(['student' => 'นักศึกษา', 'staff' => 'บุคลากร', 'other' => 'บุคคลทั่วไป'] as $val => $lbl): ?>
                                <label class="cursor-pointer group">
                                    <input type="radio" name="status" value="<?= $val ?>" class="hidden peer" <?= $userData['status'] === $val ? 'checked' : '' ?>>
                                    <div class="py-4 text-center rounded-2xl border border-slate-200 bg-white font-bold text-[11px] text-slate-500 peer-checked:bg-emerald-500 peer-checked:text-white peer-checked:border-emerald-500 peer-checked:shadow-lg peer-checked:shadow-emerald-100 transition-all active:scale-95">
                                        <?= $lbl ?>
                                    </div>
                                </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div>
                            <label class="label-premium">รหัสประจำตัว (7 หลัก)</label>
                            <input type="text" name="id_number" value="<?= htmlspecialchars($userData['id_number']) ?>" class="input-premium" placeholder="Ex. 6401234">
                        </div>

                        <div>
                            <label class="label-premium">คณะ / หน่วยงาน</label>
                            <input type="text" name="department" value="<?= htmlspecialchars($userData['department']) ?>" class="input-premium" placeholder="เช่น วิทยาลัยนวัตกรรมดิจิทัลฯ">
                        </div>
                    </div>
                </div>

                <div class="space-y-6 pt-4">
                    <div class="flex items-center gap-3 px-2">
                        <div class="w-1.5 h-6 bg-orange-500 rounded-full"></div>
                        <h3 class="text-slate-900 font-black text-lg tracking-tight">การติดต่อ</h3>
                    </div>

                    <div class="space-y-5">
                        <div>
                            <label class="label-premium">เบอร์โทรศัพท์</label>
                            <input type="tel" name="phone_number" value="<?= htmlspecialchars($userData['phone']) ?>" class="input-premium" placeholder="08X-XXX-XXXX">
                        </div>

                        <div>
                            <label class="label-premium">อีเมล</label>
                            <input type="email" name="email" value="<?= htmlspecialchars($userData['email']) ?>" class="input-premium" placeholder="example@rsu.ac.th">
                        </div>
                    </div>
                </div>

                <!-- ── Action Buttons ── -->
                <div class="pt-10 flex gap-4">
                    <button type="button" onclick="window.history.back()" class="flex-1 h-18 bg-slate-50 text-slate-400 font-black rounded-2xl active:scale-95 transition-all text-sm tracking-widest border border-slate-100">ย้อนกลับ</button>
                    <button type="submit" class="flex-[2] h-18 bg-slate-900 text-white font-black rounded-2xl active:scale-95 transition-all text-sm tracking-widest shadow-xl shadow-slate-200">บันทึกข้อมูล</button>
                </div>

            </form>

        </main>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Form validation or AI suggestions can go here
    </script>
</body>
</html>