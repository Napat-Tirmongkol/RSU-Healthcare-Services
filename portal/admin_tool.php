<?php
declare(strict_types=1);
session_start();
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/includes/auth.php';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Tool — RSU Healthcare Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/tailwind.min.css">
    <link rel="stylesheet" href="../assets/css/portal.css">
    <style>
        * { font-family: 'Prompt', sans-serif; }
    </style>
</head>
<body>

    <!-- ── Header ── -->
    <header class="portal-header">
        <div class="max-w-[1280px] mx-auto px-4 sm:px-6 py-3 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <a href="index.php"
                   class="w-9 h-9 flex items-center justify-center rounded-xl border text-sm transition-all hover:bg-gray-50"
                   style="border-color:#c7e8d5; color:#2e9e63;"
                   title="กลับ Portal">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <div class="w-9 h-9 rounded-xl flex items-center justify-center text-violet-600" style="background:#ede9fe;">
                    <i class="fa-solid fa-screwdriver-wrench text-sm"></i>
                </div>
                <div>
                    <div class="font-black text-gray-900 text-[15px] leading-none">Admin Tool</div>
                    <div class="text-[10px] font-bold tracking-[.15em] uppercase opacity-60 mt-0.5" style="color:#2e9e63">RSU Healthcare Portal</div>
                </div>
            </div>
            <div class="user-pill">
                <div class="user-avatar"><i class="fa-solid fa-user-shield text-[10px]"></i></div>
                <div class="hidden sm:block">
                    <div class="text-[9px] font-bold text-gray-400 uppercase tracking-wider leading-none mb-0.5">
                        <?= htmlspecialchars(ucfirst($_SESSION['admin_role'] ?? 'admin')) ?>
                    </div>
                    <div class="text-xs font-black text-gray-900 leading-none">
                        <?= htmlspecialchars($_SESSION['admin_username'] ?? 'Admin') ?>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- ── Page Body ── -->
    <div class="max-w-[1280px] mx-auto px-5 md:px-8 py-10">

        <!-- Page Title -->
        <div class="mb-8 au">
            <h1 class="text-2xl sm:text-3xl font-black text-gray-900 flex items-center gap-3">
                <span class="w-2 h-8 rounded-full flex-shrink-0" style="background:linear-gradient(180deg,#7c3aed,#a78bfa)"></span>
                Admin Tool
            </h1>
            <p class="text-sm text-gray-400 mt-2 ml-5">เครื่องมือสำหรับผู้ดูแลระบบ</p>
        </div>

        <!-- Content Area (เพิ่ม content ที่นี่) -->
        <div class="au d1">

            <!-- Placeholder -->
            <div class="bg-white border border-dashed border-gray-200 rounded-2xl flex flex-col items-center justify-center py-24 text-center">
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center mb-4 text-violet-400" style="background:#ede9fe;">
                    <i class="fa-solid fa-screwdriver-wrench text-2xl"></i>
                </div>
                <p class="font-black text-gray-400 text-lg mb-1">Admin Tool</p>
                <p class="text-sm text-gray-300">ยังไม่มี content — เพิ่มได้เลยครับ</p>
            </div>

        </div>

    </div>

</body>
</html>
