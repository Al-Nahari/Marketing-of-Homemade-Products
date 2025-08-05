<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/db.php';

/**
 * Flash Messages
 */
function addFlashMessage($message, $type = 'info') {
    $_SESSION['flash_messages'][] = ['text' => $message, 'type' => $type];
}

function displayFlashMessages() {
    if (!empty($_SESSION['flash_messages'])) {
        foreach ($_SESSION['flash_messages'] as $msg) {
            echo '<div class="alert alert-' . htmlspecialchars($msg['type']) . ' alert-dismissible fade show">';
            echo htmlspecialchars($msg['text']);
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
            echo '</div>';
        }
        unset($_SESSION['flash_messages']);
    }
}

/**
 * ترجمة الحالات
 */
function getUserTypeText($type) {
    return [
        'admin' => 'مسؤول',
        'family' => 'عائلة منتجة',
        'user' => 'مستخدم'
    ][$type] ?? $type;
}

function getUserStatusText($status) {
    return [
        'active' => 'نشط',
        'pending' => 'قيد الانتظار',
        'blocked' => 'محظور'
    ][$status] ?? $status;
}

function getProductStatusText($status) {
    return [
        'available' => 'متاح',
        'out_of_stock' => 'غير متوفر',
        'archived' => 'مؤرشف'
    ][$status] ?? $status;
}

function getOrderStatusText($status) {
    return [
        'pending' => 'قيد الانتظار',
        'processing' => 'قيد المعالجة',
        'shipped' => 'تم الشحن',
        'delivered' => 'تم التسليم',
        'cancelled' => 'ملغى'
    ][$status] ?? $status;
}

/**
 * أدوات مساعدة
 */
function formatPrice($price) {
    return number_format($price, 2) . ' ر.س';
}

function textExcerpt($text, $length = 100) {
    return mb_strlen($text) > $length ? mb_substr($text, 0, $length) . '...' : $text;
}

function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * رفع صورة
 */
function uploadProductImage($file) {
    $allowed = ['image/jpeg', 'image/png', 'image/webp'];
    if (!in_array($file['type'], $allowed)) return ['success' => false, 'error' => 'نوع غير مدعوم'];
    if ($file['size'] > 2 * 1024 * 1024) return ['success' => false, 'error' => 'الملف كبير جداً'];

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'product_' . time() . '_' . bin2hex(random_bytes(4)) . ".$ext";
    $path = __DIR__ . '/../assets/images/products/' . $filename;

    if (move_uploaded_file($file['tmp_name'], $path)) {
        return ['success' => true, 'path' => '/assets/images/products/' . $filename];
    }

    return ['success' => false, 'error' => 'فشل رفع الصورة'];
}

/**
 * تسجيل النشاط
 */
function logActivity($user_id, $action) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("INSERT INTO activity_log (user_id, action) VALUES (?, ?)");
        $stmt->execute([$user_id, $action]);
    } catch (PDOException $e) {
        error_log('Log error: ' . $e->getMessage());
    }
}
