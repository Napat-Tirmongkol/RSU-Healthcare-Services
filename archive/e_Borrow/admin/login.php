<?php
// ����� Session (��ͧ���¡�� session_start() 㹷ء˹�ҷ���ͧ����� Session)
session_start();

// ��Ǩ�ͺ��� ��� Log in ���� (�� Session 'user_id' ����)
if (isset($_SESSION['user_id'])) {
    // ������˹�� index.php �ѹ�� (�����繵�ͧ Log in ���)
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log in - к׹ػóᾷ</title>
    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
        body {
            background-color: var(--color-page-bg, #f1f5f9);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            transition: background 0.3s ease;
        }

        .login-container {
            background: var(--color-content-bg, #fff);
            padding: 40px;
            border-radius: var(--border-radius-main, 16px);
            box-shadow: var(--box-shadow-main, 0 8px 24px rgba(0, 0, 0, 0.1));
            width: 100%;
            max-width: 380px;
            text-align: center;
            border: 1px solid var(--border-color);
        }

        .login-container h1 {
            color: var(--color-primary, #0B6623);
            margin-bottom: 8px;
            font-size: 1.8rem;
            font-weight: 800;
        }
        .login-container p {
            color: var(--color-text-muted);
            margin-bottom: 24px;
            font-size: 0.9rem;
        }

        .login-container input[type="text"],
        .login-container input[type="password"] {
            width: 100%;
            padding: 12px 16px;
            margin-bottom: 16px;
            border: 1px solid var(--border-color, #ddd);
            border-radius: 8px;
            box-sizing: border-box;
            background: var(--color-page-bg);
            color: var(--color-text-dark);
            font-size: 1rem;
        }

        .login-container button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, var(--color-primary), var(--color-primary-dark));
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(11, 102, 35, 0.2);
        }

        .login-container button:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }

        /* Error Messages */
        .error-message {
            background-color: #fee2e2;
            color: #dc2626;
            border: 1px solid #fecaca;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.85rem;
            display: <?php echo isset($_GET['error']) ? 'block' : 'none'; ?>;
        }
        
        /* Dark Mode Overrides */
        body.dark-mode .error-message {
            background-color: rgba(220, 38, 38, 0.2);
            color: #f87171;
            border-color: rgba(220, 38, 38, 0.3);
        }
    </style>
</head>

<body>
    <script>
        // Apply theme immediately before page renders
        if (localStorage.getItem('theme') === 'dark') {
            document.body.classList.add('dark-mode');
        }
    </script>

    <div class="login-container">
        <h1>MedLoan Log in</h1>
        <p>ระบบจัดการยืมอุปกรณ์แพทย์</p>

        <div class="error-message">
            ชื่อผู้ใช้งาน หรือ รหัสผ่าน ไม่ถูกต้อง!
        </div>

        <div class="error-message" style="background-color: #fff3cd; color: #664d03; border-color: #ffecb5; display: <?php echo (isset($_GET['error']) && $_GET['error'] == 'disabled') ? 'block' : 'none'; ?>;">
            บัญชีนี้ถูกระงับการใช้งานชั่วคราว!
        </div>

        <form action="../process/login_process.php" method="POST">
            <div>
                <input type="text" name="username" placeholder="Username" required autofocus>
            </div>
            <div>
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit">เข้าสู่ระบบ</button>
        </form>
    </div>

</body>

</html>