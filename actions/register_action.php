<?php
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /public/register.php?error=invalid_request');
    exit();
}

// جمع البيانات مع التنظيف
$full_name = htmlspecialchars(trim($_POST['full_name']));
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];
$user_type = in_array($_POST['user_type'], ['user', 'family']) ? $_POST['user_type'] : 'user';
$city = htmlspecialchars(trim($_POST['city']));
$phone = htmlspecialchars(trim($_POST['phone']));

// التحقق من البيانات
$errors = [];

if (empty($full_name)) $errors[] = 'empty_full_name';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'invalid_email';
if (strlen($password) < 8) $errors[] = 'short_password';
if ($password !== $confirm_password) $errors[] = 'password_mismatch';

if (!empty($errors)) {
    header('Location: /public/register.php?error=' . implode(',', $errors));
    exit();
}

try {
    // التحقق من عدم وجود البريد الإلكتروني مسبقاً
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->fetch()) {
        header('Location: /public/register.php?error=email_exists');
        exit();
    }

    // تسجيل المستخدم الجديد
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $status = ($user_type === 'family') ? 'pending' : 'active';
    
    $stmt = $pdo->prepare("INSERT INTO users 
        (full_name, email, password, user_type, city, phone, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->execute([
        $full_name,
        $email,
        $hashed_password,
        $user_type,
        $city,
        $phone,
        $status
    ]);

    // إرسال إشعار للمسؤول إذا كان تسجيل عائلة
    if ($user_type === 'family') {
        $admin_stmt = $pdo->prepare("SELECT id FROM users WHERE user_type = 'admin' LIMIT 1");
        $admin_stmt->execute();
        $admin = $admin_stmt->fetch();
        
        if ($admin) {
            $notif_stmt = $pdo->prepare("INSERT INTO notifications 
                (user_id, message) VALUES (?, ?)");
            $notif_stmt->execute([
                $admin['id'],
                "طلب تسجيل جديد لعائلة منتجة: $full_name"
            ]);
        }
    }

    header('Location: /public/login.php?success=registration_complete');
    exit();

} catch (PDOException $e) {
    error_log("Registration error: " . $e->getMessage());
    header('Location: /public/register.php?error=server_error');
    exit();
}
?>