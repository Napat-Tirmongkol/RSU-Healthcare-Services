<?php
// portal/includes/auth.php
require_once __DIR__ . '/../../includes/session_guard.php';
start_secure_session();

$_inAjax = basename(dirname($_SERVER['SCRIPT_NAME'] ?? '')) === 'ajax';
$_pfx    = $_inAjax ? '../../admin/auth/' : '../admin/auth/';
check_admin_session($_pfx . 'login.php', $_pfx . 'staff_login.php');
