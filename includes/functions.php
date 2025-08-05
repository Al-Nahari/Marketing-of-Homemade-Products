<?php

if (!function_exists('getProductStatusText')) {
    function getProductStatusText($status) {
        $statuses = [
            'available' => 'متاح',
            'out_of_stock' => 'غير متوفر',
            'archived' => 'مؤرشف'
        ];
        return $statuses[$status] ?? $status;
    }
}

/**
 * تحويل حالة الطلب إلى نص مقروء
 */
if (!function_exists('getOrderStatusText')) {
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
}

/**
 * تحويل نوع المستخدم إلى نص مقروء
 */
if (!function_exists('getUserTypeText')) {
    function getUserTypeText($type) {
        $types = [
            'admin' => 'مسؤول',
            'family' => 'عائلة منتجة',
            'user' => 'مستخدم'
        ];
        return $types[$type] ?? $type;
    }
}

/**
 * تحويل حالة المستخدم إلى نص مقروء
 */
if (!function_exists('getUserStatusText')) {
    function getUserStatusText($status) {
        $statuses = [
            'active' => 'نشط',
            'pending' => 'قيد الانتظار',
            'blocked' => 'محظور'
        ];
        return $statuses[$status] ?? $status;
    }
}

/**
 * إضافة رسالة تنبيه للجلسة
 */
if (!function_exists('addFlashMessage')) {
    function addFlashMessage($message, $type = 'info') {
        if (!isset($_SESSION['flash_messages'])) {
            $_SESSION['flash_messages'] = [];
        }
        $_SESSION['flash_messages'][] = [
            'text' => $message,
            'type' => $type
        ];
    }
}

/**
 * عرض رسائل التنبيه
 */
function displayFlashMessages() {
    if (!empty($_SESSION['flash_messages'])) {
        foreach ($_SESSION['flash_messages'] as $message) {
            echo '<div class="alert alert-'.$message['type'].' alert-dismissible fade show">';
            echo $message['text'];
            echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
            echo '</div>';
        }
        unset($_SESSION['flash_messages']);
    }
}

/**
 * تسجيل نشاط المستخدم
 */
if (!function_exists('logActivity')) {
    function logActivity($user_id, $action) {
        global $pdo;
        try {
            $stmt = $pdo->prepare("INSERT INTO activity_log (user_id, action) VALUES (?, ?)");
            $stmt->execute([$user_id, $action]);
        } catch (PDOException $e) {
            error_log("Activity log error: " . $e->getMessage());
        }
    }
}

/**
 * تحميل صورة المنتج
 */
function uploadProductImage($file) {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
    $maxSize = 2 * 1024 * 1024; // 2MB
    
    if (!in_array($file['type'], $allowedTypes)) {
        return ['success' => false, 'error' => 'نوع الملف غير مدعوم'];
    }
    
    if ($file['size'] > $maxSize) {
        return ['success' => false, 'error' => 'حجم الملف كبير جداً'];
    }
    
    $uploadDir = __DIR__ . '/../assets/images/products/';
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'product_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
    
    if (move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
        return ['success' => true, 'path' => '/assets/images/products/' . $filename];
    }
    
    return ['success' => false, 'error' => 'فشل رفع الملف'];
}

/**
 * إنشاء اختصار للنص
 */
function textExcerpt($text, $length = 100, $suffix = '...') {
    if (mb_strlen($text) <= $length) {
        return $text;
    }
    return mb_substr($text, 0, $length) . $suffix;
}

/**
 * تنسيق السعر
 */
function formatPrice($price) {
    return number_format($price, 2) . ' ر.س';
}

/**
 * التحقق من صلاحيات المستخدم
 */
function hasPermission($requiredType) {
    if (!isset($_SESSION['user_type'])) {
        return false;
    }
    
    $userType = $_SESSION['user_type'];
    
    // إذا كان المستخدم مسؤولاً، لديه جميع الصلاحيات
    if ($userType === 'admin') {
        return true;
    }
    
    return $userType === $requiredType;
}

/**
 * إعادة توجيه المستخدم مع رسالة
 */
function redirect($url, $message = null, $type = 'info') {
    if ($message) {
        addFlashMessage($message, $type);
    }
    header("Location: $url");
    exit();
}

/**
 * تنقية بيانات الإدخال
 */
function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

/**
 * إنشاء عنصر واجهة select
 */
function renderSelect($name, $options, $selected = null, $attributes = []) {
    $attrString = '';
    foreach ($attributes as $key => $value) {
        $attrString .= " $key=\"$value\"";
    }
    
    $html = "<select name=\"$name\"$attrString>";
    foreach ($options as $value => $label) {
        $isSelected = $selected == $value ? ' selected' : '';
        $html .= "<option value=\"$value\"$isSelected>$label</option>";
    }
    $html .= '</select>';
    
    return $html;
}

/**
 * إنشاء عنصر واجهة pagination
 */
function renderPagination($totalItems, $perPage, $currentPage, $url) {
    $totalPages = ceil($totalItems / $perPage);
    if ($totalPages <= 1) return '';
    
    $html = '<nav aria-label="Page navigation"><ul class="pagination">';
    
    // زر السابق
    if ($currentPage > 1) {
        $html .= '<li class="page-item"><a class="page-link" href="'.$url.'?page='.($currentPage-1).'">السابق</a></li>';
    }
    
    // أرقام الصفحات
    for ($i = 1; $i <= $totalPages; $i++) {
        $active = $i == $currentPage ? ' active' : '';
        $html .= '<li class="page-item'.$active.'"><a class="page-link" href="'.$url.'?page='.$i.'">'.$i.'</a></li>';
    }
    
    // زر التالي
    if ($currentPage < $totalPages) {
        $html .= '<li class="page-item"><a class="page-link" href="'.$url.'?page='.($currentPage+1).'">التالي</a></li>';
    }
    
    $html .= '</ul></nav>';
    
    return $html;
}

/**
 * إنشاء عنصر واجهة rating stars
 */
function renderRatingStars($rating) {
    $html = '';
    $fullStars = floor($rating);
    $hasHalfStar = ($rating - $fullStars) >= 0.5;
    $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);
    
    for ($i = 0; $i < $fullStars; $i++) {
        $html .= '<i class="fas fa-star text-warning"></i>';
    }
    
    if ($hasHalfStar) {
        $html .= '<i class="fas fa-star-half-alt text-warning"></i>';
    }
    
    for ($i = 0; $i < $emptyStars; $i++) {
        $html .= '<i class="far fa-star text-warning"></i>';
    }
    
    return $html;
}

/**
 * إنشاء slug للروابط
 */
function createSlug($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    
    return $text ?: 'untitled';
}