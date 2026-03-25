<?php
/**
 * portal/index.php (v2.0 Premium Re-design)
 * Central Hub Dashboard สำหรับการจัดการระบบทั้งหมด
 */
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/includes/auth.php'; // ตรวจสอบความปลอดภัย

$pdo = db();

/**
 * 📊 (1) LIVE DATA AGGREGATION
 * ดึงสถิติจริงจากทุกโมดูล (พร้อมระบบป้องกัน Error กรณีตารางยังไม่ถูกสร้าง)
 */
$stats = [
    'users'    => 0,
    'admins'   => 0,
    'camps'    => 0,
    'borrows'  => 0,
    'returned' => 0
];

try {
    $stats['users']   = (int)$pdo->query("SELECT COUNT(*) FROM sys_users")->fetchColumn();
    $stats['admins']  = (int)$pdo->query("SELECT COUNT(*) FROM sys_admins")->fetchColumn();
    $stats['camps']   = (int)$pdo->query("SELECT COUNT(*) FROM camp_list WHERE status = 'active'")->fetchColumn();
    
    // ตรวจสอบการยืม (เช็คก่อนว่าตารางมีอยู่จริงไหม)
    $stmt_borrow = $pdo->query("SHOW TABLES LIKE 'borrow_records'");
    if ($stmt_borrow->rowCount() > 0) {
        $stats['borrows']  = (int)$pdo->query("SELECT COUNT(*) FROM borrow_records WHERE approval_status = 'pending'")->fetchColumn();
        $stats['returned'] = (int)$pdo->query("SELECT COUNT(*) FROM borrow_records WHERE status = 'returned'")->fetchColumn();
    }
} catch (PDOException $e) {
    // หากเกิด Error เฉพาะจุด ให้ใช้ค่าเริ่มต้น (0) ต่อไป หน้าเว็บจะไม่พัง
    error_log("Portal Stats Error: " . $e->getMessage());
}

/**
 * 🕒 (2) RECENT ACTIVITY LOGS
 * ดึงกิจกรรมล่าสุด 5 รายการจากระบบ (พร้อมระบบป้องกัน Error)
 */
$recentLogs = [];
try {
    $stmt_logs = $pdo->query("SHOW TABLES LIKE 'activity_logs'");
    if ($stmt_logs->rowCount() > 0) {
        $recentLogs = $pdo->query("SELECT action_details, created_at, admin_username FROM activity_logs ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    error_log("Portal Logs Error: " . $e->getMessage());
}

/**
 * 🛡️ (3) SSO LOGIC FOR E-BORROW
 * ส่งสิทธิ์จาก Portal ไปยังโมดูลการยืม (Archive)
 */
if (!isset($_SESSION['user_id']) && isset($_SESSION['admin_username'])) {
    $staff = $pdo->prepare("SELECT id, full_name, role FROM sys_staff WHERE username = :uname LIMIT 1");
    $staff->execute([':uname' => $_SESSION['admin_username']]);
    $staffData = $staff->fetch();
    if ($staffData) {
        $_SESSION['user_id'] = $staffData['id'];
        $_SESSION['full_name'] = $staffData['full_name'];
        $_SESSION['role'] = $staffData['role'];
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart HUB Portal - Administrative Dashboard</title>
    
    <!-- Fonts & Essentials -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Outfit', 'Prompt', 'sans-serif'] },
                    colors: { primary: '#0052CC', accent: '#FFAB00', success: '#10B981', warning: '#F59E0B' }
                }
            }
        }
    </script>
    
    <style>
        body { background: #fdfdfd; min-height: 100vh; font-family: 'Outfit', 'Prompt', sans-serif; overflow-x: hidden; }
        .bg-mesh {
            background: 
                radial-gradient(at 0% 0%, rgba(0, 82, 204, 0.08) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(255, 171, 0, 0.08) 0px, transparent 50%),
                radial-gradient(at 50% 50%, rgba(255, 255, 255, 1) 0px, transparent 100%);
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.05);
        }
        .card-hover:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px -15px rgba(0, 82, 204, 0.15);
        }
        @keyframes slideIn { from { opacity: 0; transform: translateX(-20px); } to { opacity: 1; transform: translateX(0); } }
        .animate-slide { animation: slideIn 0.5s ease-out forwards; }
    </style>
</head>
<body class="bg-mesh p-4 md:p-8">

    <div class="max-w-[1400px] mx-auto space-y-8">
        
        <!-- HEADER COMMAND BAR -->
        <header class="flex flex-col md:flex-row justify-between items-center gap-6 animate-slide">
            <div class="flex items-center gap-5">
                <div class="w-16 h-16 bg-gradient-to-br from-primary to-blue-700 rounded-[22px] flex items-center justify-center text-white text-3xl shadow-xl shadow-blue-200">
                    <i class="fa-solid fa-layer-group"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-[800] text-gray-900 tracking-tight leading-none uppercase">Hub Portal</h1>
                    <p class="text-xs text-primary/60 font-black tracking-widest uppercase mt-1">Central Command Center</p>
                </div>
            </div>

            <div class="flex items-center gap-3 glass-card px-5 py-2.5 rounded-full">
                <div class="w-9 h-9 bg-blue-100 text-primary rounded-full flex items-center justify-center text-sm font-bold shadow-inner">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>
                <div class="hidden sm:block">
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-wider leading-none">Administrator</p>
                    <p class="text-xs font-bold text-gray-800 leading-tight"><?= htmlspecialchars($_SESSION['admin_username'] ?? 'User Name') ?></p>
                </div>
                <div class="w-[1px] h-6 bg-gray-200 mx-1"></div>
                <a href="../admin/logout.php" class="text-gray-300 hover:text-red-500 transition-all ml-1"><i class="fa-solid fa-power-off"></i></a>
            </div>
        </header>

        <!-- KPI SUMMARY SECTION (Real Actionable Data) -->
        <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 animate-slide" style="animation-delay: 0.1s;">
            <div class="glass-card p-6 rounded-[32px] border-l-4 border-primary">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Total Database</span>
                    <i class="fa-solid fa-users text-primary/30"></i>
                </div>
                <h3 class="text-3xl font-black text-gray-900"><?= number_format($stats['users']) ?><span class="text-xs text-gray-300 ml-1">Lives</span></h3>
            </div>
            <div class="glass-card p-6 rounded-[32px] border-l-4 border-success">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Active Campaigns</span>
                    <i class="fa-solid fa-bullhorn text-success/30"></i>
                </div>
                <h3 class="text-3xl font-black text-gray-900"><?= $stats['camps'] ?><span class="text-xs text-gray-300 ml-1">Active</span></h3>
            </div>
            <div class="glass-card p-6 rounded-[32px] border-l-4 border-accent">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">Pending Actions</span>
                    <i class="fa-solid fa-clock-rotate-left text-accent/30 scale-110"></i>
                </div>
                <div class="flex items-center gap-2">
                    <h3 class="text-3xl font-black text-gray-900"><?= $stats['borrows'] ?></h3>
                    <?php if($stats['borrows'] > 0): ?>
                        <span class="px-2 py-0.5 bg-red-100 text-red-600 text-[10px] font-black rounded-lg animate-pulse">Urgent</span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="glass-card p-6 rounded-[32px] border-l-4 border-gray-900">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">System Logs</span>
                    <i class="fa-solid fa-database text-gray-900/10"></i>
                </div>
                <h3 class="text-3xl font-black text-gray-900">Ok<span class="text-[10px] text-success ml-2 font-bold uppercase">Healthy</span></h3>
            </div>
        </section>

        <!-- MAIN LAYOUT (App Launcher + Activity Log) -->
        <main class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            
            <!-- LEFT AREA: PROJECT TILES -->
            <div class="lg:col-span-3 space-y-8 animate-slide" style="animation-delay: 0.2s;">
                <h4 class="text-lg font-black text-gray-900 uppercase tracking-tighter flex items-center gap-2">
                    <div class="w-1.5 h-6 bg-accent rounded-full"></div> Available Applications
                </h4>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- App 1: User Directory -->
                    <div class="glass-card p-8 rounded-[40px] flex flex-col justify-between card-hover transition-all duration-300">
                        <div>
                            <div class="w-14 h-14 bg-accent/10 text-accent rounded-3xl flex items-center justify-center text-2xl mb-6">
                                <i class="fa-solid fa-id-card-clip"></i>
                            </div>
                            <h3 class="text-2xl font-black text-gray-900 mb-3 tracking-tight">User Directory</h3>
                            <p class="text-gray-500 text-xs leading-relaxed mb-6">Master database สำหรับจัดการนักศึกษาและสิทธิ์ผู้ดูแลระบบทั้งหมดในเครือ RSU Services</p>
                        </div>
                        <div class="flex gap-2">
                            <a href="../sys_admin/users.php?layout=none" class="flex-1 text-center bg-gray-50 hover:bg-gray-100 py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest text-gray-700 transition-all active:scale-95">Search Users</a>
                            <a href="../sys_admin/manage_admins.php?layout=none" class="flex-1 text-center bg-accent text-white py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-accent/20 hover:brightness-105 transition-all active:scale-95">Manage Admins</a>
                        </div>
                    </div>

                    <!-- App 2: E-Campaign -->
                    <div class="bg-primary p-2 rounded-[40px] shadow-2xl shadow-primary/10 card-hover transition-all duration-300">
                        <div class="bg-white/5 backdrop-blur-sm p-8 rounded-[38px] h-full flex flex-col justify-between text-white border border-white/10">
                            <div>
                                <div class="w-14 h-14 bg-white/20 text-white rounded-3xl flex items-center justify-center text-2xl mb-6">
                                    <i class="fa-solid fa-bullhorn rotate-12"></i>
                                </div>
                                <h3 class="text-2xl font-black mb-3 tracking-tight">e-Campaign V2</h3>
                                <p class="text-blue-100/70 text-xs leading-relaxed mb-6">ระบบจัดการแคมเปญ งานอบรม และการจองสิทธิ์ต่างๆ ครอบคลุมระบบ QR Check-in</p>
                            </div>
                            <a href="../admin/index.php" class="w-full bg-white text-primary py-4 rounded-2xl text-center font-black text-xs uppercase tracking-[0.2em] shadow-xl hover:bg-accent hover:text-white transition-all active:scale-95">
                                Launch Project <i class="fa-solid fa-arrow-right-long ml-2"></i>
                            </a>
                        </div>
                    </div>

                    <!-- App 3: E-Borrow -->
                    <div class="glass-card p-6 rounded-[32px] flex items-center justify-between group transition-all duration-300 hover:bg-gray-50 md:col-span-2">
                        <div class="flex items-center gap-6">
                            <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-3xl flex items-center justify-center text-xl group-hover:bg-blue-600 group-hover:text-white transition-all duration-500">
                                <i class="fa-solid fa-toolbox"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-gray-900 group-hover:text-blue-600 transition-colors">e-Borrow & Inventory Archive</h3>
                                <p class="text-gray-400 text-[10px] font-medium uppercase tracking-wide">จัดการอุปกรณ์และเวชภัณฑ์ทางการแพทย์</p>
                            </div>
                        </div>
                        <a href="../archive/e_Borrow/admin/index.php" class="px-6 py-3.5 bg-gray-900 text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-blue-600 hover:shadow-lg transition-all active:scale-95">Open System</a>
                    </div>

                    <!-- Future App Placeholder -->
                    <div class="border-2 border-dashed border-gray-200 p-8 rounded-[40px] flex flex-col items-center justify-center text-center opacity-40 hover:opacity-100 transition-all group">
                        <div class="w-12 h-12 bg-gray-100 text-gray-400 rounded-full flex items-center justify-center text-xl mb-4 group-hover:bg-blue-50 group-hover:text-blue-600 transition-all">
                            <i class="fa-solid fa-plus-circle"></i>
                        </div>
                        <p class="text-xs font-black uppercase tracking-widest text-gray-400 mb-1">Coming Soon</p>
                        <p class="text-[10px] text-gray-300">Ready for next module...</p>
                    </div>

                </div>
            </div>

            <!-- RIGHT AREA: ACTIVITY FEED -->
            <aside class="lg:col-span-1 space-y-6 animate-slide" style="animation-delay: 0.3s;">
                <h4 class="text-lg font-black text-gray-900 uppercase tracking-tighter flex items-center gap-2">
                    <i class="fa-solid fa-bolt-lightning text-accent"></i> Recent Activity
                </h4>
                
                <div class="glass-card rounded-[32px] overflow-hidden">
                    <div class="p-6 space-y-6">
                        <?php if($recentLogs): ?>
                            <?php foreach($recentLogs as $log): ?>
                                <div class="relative pl-6 border-l-2 border-primary/20 last:border-0 pb-2">
                                    <div class="absolute -left-[5px] top-0 w-2 h-2 bg-primary rounded-full"></div>
                                    <p class="text-[11px] text-gray-400 font-bold mb-1 uppercase tracking-tighter">
                                        <?= date('H:i', strtotime($log['created_at'])) ?> - <?= htmlspecialchars($log['admin_username']) ?>
                                    </p>
                                    <p class="text-xs font-bold text-gray-700 leading-snug line-clamp-2">
                                        <?= htmlspecialchars($log['action_details']) ?>
                                    </p>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-10">
                                <i class="fa-solid fa-ghost text-gray-200 text-4xl mb-4"></i>
                                <p class="text-xs font-bold text-gray-300 uppercase tracking-widest">No activity found</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="bg-gray-50/80 p-4 text-center border-t border-gray-100">
                        <a href="../admin/activity_logs.php" class="text-[10px] font-black uppercase tracking-widest text-primary hover:underline">View All Logs</a>
                    </div>
                </div>
            </aside>

        </main>

        <!-- FOOTER INFO -->
        <footer class="text-center py-10 opacity-30">
            <p class="text-[10px] font-bold uppercase tracking-[0.3em]">Management Portal &copy; 2026 RSU Healthcare Services</p>
        </footer>

    </div>

</body>
</html>
