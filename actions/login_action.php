<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /ene/public/login.php?error=invalid_request');
    exit();
}

$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$password = $_POST['password'];

// التحقق من البيانات المدخلة
if (empty($email) || empty($password)) {
    header('Location: /ene/public/login.php?error=empty_fields');
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
        header('Location: /ene/public/login.php?error=invalid_credentials');
        exit();
    }

    // التحقق من حالة الحساب
    if ($user['status'] !== 'active') {
        header('Location: /ene/public/login.php?error=account_not_active');
        exit();
    }

    // إنشاء جلسة المستخدم
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_type'] = $user['user_type'];
    $_SESSION['full_name'] = $user['full_name'];

    // تسجيل النشاط
    logActivity($user['id'], "تسجيل دخول ناجح");

    // التوجيه حسب نوع المستخدم
    switch ($user['user_type']) {
        case 'admin':
            header('Location: /ene/admin/dashboard.php');
            break;
        case 'family':
            header('Location: /ene/family/dashboard.php');
            break;
        default:
            header('Location: /ene/user/dashboard.php');
    }
    exit();

} catch (PDOException $e) {
    error_log("Login error: " . $e->getMessage());
    header('Location: /ene/public/login.php?error=server_error');
    exit();
}
?>