<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';

if (!isAdminLoggedIn()) {
    header('Location: /public/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /admin/approve_families.php?error=invalid_request');
    exit();
}

$family_id = filter_input(INPUT_POST, 'family_id', FILTER_SANITIZE_NUMBER_INT);
$action = $_POST['action']; // 'approve' أو 'reject'

if (empty($family_id) || !in_array($action, ['approve', 'reject'])) {
    header('Location: /admin/approve_families.php?error=invalid_data');
    exit();
}

try {
    // التحقق من وجود العائلة
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? AND user_type = 'family'");
    $stmt->execute([$family_id]);
    $family = $stmt->fetch();

    if (!$family) {
        header('Location: /admin/approve_families.php?error=family_not_found');
        exit();
    }

    // تحديث حالة العائلة
    $new_status = ($action === 'approve') ? 'active' : 'blocked';
    $approval_status = ($action === 'approve') ? 'approved' : 'rejected';
    
    $update_stmt = $pdo->prepare("UPDATE users 
        SET status = ?, approval_status = ? 
        WHERE id = ?");
    $update_stmt->execute([$new_status, $approval_status, $family_id]);

    // إرسال إشعار للعائلة
    $message = ($action === 'approve') 
        ? "تمت الموافقة على طلب تسجيلك كعائلة منتجة. يمكنك الآن تسجيل الدخول وإضافة منتجاتك." 
        : "تم رفض طلب تسجيلك كعائلة منتجة. يرجى التواصل مع الإدارة لمزيد من المعلومات.";
    
    $notif_stmt = $pdo->prepare("INSERT INTO notifications 
        (user_id, message) VALUES (?, ?)");
    $notif_stmt->execute([$family_id, $message]);

    // تسجيل النشاط
    $action_text = ($action === 'approve') ? 'موافقة على' : 'رفض';
    logActivity($_SESSION['user_id'], "$action_text عائلة منتجة: {$family['full_name']}");

    header('Location: /admin/approve_families.php?success=action_completed');
    exit();

} catch (PDOException $e) {
    error_log("Approve family error: " . $e->getMessage());
    header('Location: /admin/approve_families.php?error=server_error');
    exit();
}
?>