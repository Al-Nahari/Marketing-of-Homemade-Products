<?php
require_once __DIR__ . '/../config/db.php';

/**
 * التحقق من تسجيل الدخول
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * التحقق من أن المستخدم مسؤول
 */
function isAdmin() {
    return isLoggedIn() && $_SESSION['user_type'] === 'admin';
}

/**
 * التحقق من أن المستخدم عائلة منتجة
 */
function isFamily() {
    return isLoggedIn() && $_SESSION['user_type'] === 'family';
}

/**
 * التحقق من أن المستخدم مسجل دخوله كمسؤول
 */
function isAdminLoggedIn() {
    if (!isLoggedIn() || !isAdmin()) {
        addFlashMessage('يجب أن تكون مسؤولاً للوصول إلى هذه الصفحة', 'danger');
        header('Location: /public/login.php');
        exit();
    }
    return true;
}

/**
 * التحقق من أن المستخدم مسجل دخوله كعائلة منتجة
 */
function isFamilyLoggedIn() {
    if (!isLoggedIn() || !isFamily()) {
        addFlashMessage('يجب أن تكون عائلة منتجة للوصول إلى هذه الصفحة', 'danger');
        header('Location: /public/login.php');
        exit();
    }
    return true;
}

/**
 * التحقق من أن المستخدم مسجل دخوله كمستخدم عادي
 */
function isUserLoggedIn() {
    if (!isLoggedIn() || $_SESSION['user_type'] !== 'user') {
        addFlashMessage('يجب تسجيل الدخول للوصول إلى هذه الصفحة', 'danger');
        header('Location: /public/login.php');
        exit();
    }
    return true;
}

/**
 * إضافة رسالة تنبيه
 */
function addFlashMessage($text, $type = 'info') {
    if (!isset($_SESSION['flash_messages'])) {
        $_SESSION['flash_messages'] = [];
    }
    $_SESSION['flash_messages'][] = [
        'text' => $text,
        'type' => $type
    ];
}

/**
 * تسجيل نشاط المستخدم
 */
function logActivity($user_id, $action) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("INSERT INTO activity_log (user_id, action) VALUES (?, ?)");
        $stmt->execute([$user_id, $action]);
    } catch (PDOException $e) {
        error_log("Activity log error: " . $e->getMessage());
    }
}

/**
 * الحصول على نص حالة المستخدم
 */
function getUserStatusText($status) {
    $statuses = [
        'active' => 'نشط',
        'pending' => 'قيد الانتظار',
        'blocked' => 'محظور'
    ];
    return $statuses[$status] ?? $status;
}

/**
 * الحصول على نص نوع المستخدم
 */
function getUserTypeText($type) {
    $types = [
        'admin' => 'مسؤول',
        'family' => 'عائلة منتجة',
        'user' => 'مستخدم'
    ];
    return $types[$type] ?? $type;
}

/**
 * الحصول على نص حالة المنتج
 */
function getProductStatusText($status) {
    $statuses = [
        'available' => 'متاح',
        'out_of_stock' => 'غير متوفر',
        'archived' => 'مؤرشف'
    ];
    return $statuses[$status] ?? $status;
}

/**
 * الحصول على نص حالة الطلب
 */
function getOrderStatusText($status) {
    $statuses = [
        'pending' => 'قيد الانتظار',
        'processing' => 'قيد المعالجة',
        'shipped' => 'تم الشحن',
        'delivered' => 'تم التسليم',
        'cancelled' => 'ملغى'
    ];
    return $statuses[$status] ?? $status;
}

/**
 * إعادة توجيه المستخدم حسب نوعه بعد التسجيل
 */
function redirectByUserType() {
    if (!isLoggedIn()) return;
    
    switch ($_SESSION['user_type']) {
        case 'admin':
            header('Location: /admin/dashboard.php');
            break;
        case 'family':
            header('Location: /family/dashboard.php');
            break;
        default:
            header('Location: /user/dashboard.php');
    }
    exit();
}
?>