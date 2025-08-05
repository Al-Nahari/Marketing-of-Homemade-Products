<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/functions.php';

/**
 * التحقق من تسجيل الدخول
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * التحقق من نوع المستخدم
 */
function isAdmin() {
    return isLoggedIn() && $_SESSION['user_type'] === 'admin';
}

function isFamily() {
    return isLoggedIn() && $_SESSION['user_type'] === 'family';
}

function isUser() {
    return isLoggedIn() && $_SESSION['user_type'] === 'user';
}

/**
 * التحقق من أن المستخدم لديه الصلاحية المناسبة وإلا يتم تحويله
 */
function requireLogin($type = null) {
    if (!isLoggedIn()) {
        addFlashMessage('يجب تسجيل الدخول للوصول إلى هذه الصفحة', 'danger');
        header('Location: /ene/public/login.php');
        exit();
    }

    if ($type && $_SESSION['user_type'] !== $type) {
        addFlashMessage('ليست لديك صلاحية الوصول إلى هذه الصفحة', 'danger');
        header('Location: /ene/public/login.php');
        exit();
    }
}

/**
 * إعادة توجيه المستخدم حسب نوعه
 */
function redirectByUserType() {
    if (!isLoggedIn()) return;

    switch ($_SESSION['user_type']) {
        case 'admin':
            header('Location: /ene/admin/dashboard.php');
            break;
        case 'family':
            header('Location: /ene/family/dashboard.php');
            break;
        case 'user':
        default:
            header('Location: /ene/user/dashboard.php');
            break;
    }
    exit();
}
