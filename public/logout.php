<?php
require_once __DIR__ . '/../includes/auth.php';

// بدء الجلسة إذا لم تكن بدأت
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// تسجيل نشاط الخروج
if (isset($_SESSION['user_id'])) {
    logActivity($_SESSION['user_id'], "تسجيل خروج");
}

// تدمير جميع بيانات الجلسة
$_SESSION = array();
session_destroy();

// توجيه المستخدم إلى الصفحة الرئيسية
header('Location: /public/index.php');
exit();
?>