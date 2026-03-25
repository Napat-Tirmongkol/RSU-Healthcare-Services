<?php
// portal/includes/auth.php
session_start();

// ตรวจสอบสิทธิ์การเข้าถึง Portal (ต้องเป็นแอดมินเท่านั้น)
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: ../admin/login.php');
    exit;
}
