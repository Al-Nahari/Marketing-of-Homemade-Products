<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';

if (!isFamilyLoggedIn()) {
    header('Location: /ene/public/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /family/add_product.php?error=invalid_request');
    exit();
}

// جمع البيانات
$name = htmlspecialchars(trim($_POST['name']));
$description = htmlspecialchars(trim($_POST['description']));
$price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
$quantity = filter_input(INPUT_POST, 'quantity', FILTER_SANITIZE_NUMBER_INT);
$category_id = filter_input(INPUT_POST, 'category_id', FILTER_SANITIZE_NUMBER_INT);
$type_id = filter_input(INPUT_POST, 'type_id', FILTER_SANITIZE_NUMBER_INT);

// التحقق من البيانات
$errors = [];
if (empty($name)) $errors[] = 'empty_name';
if (empty($description)) $errors[] = 'empty_description';
if ($price <= 0) $errors[] = 'invalid_price';
if ($quantity < 1) $errors[] = 'invalid_quantity';
if ($category_id < 1) $errors[] = 'invalid_category';

// معالجة صورة المنتج
$image_path = null;
if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
    $file_type = $_FILES['image']['type'];
    
    if (!in_array($file_type, $allowed_types)) {
        $errors[] = 'invalid_image_type';
    } else {
        $upload_dir = __DIR__ . '/../assets/images/products/';
        $file_ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $file_name = 'product_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $file_ext;
        $target_path = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
            $image_path = '/assets/images/products/' . $file_name;
        } else {
            $errors[] = 'upload_failed';
        }
    }
}

if (!empty($errors)) {
    header('Location: /family/add_product.php?error=' . implode(',', $errors));
    exit();
}

try {
    // إضافة المنتج إلى قاعدة البيانات
    $stmt = $pdo->prepare("INSERT INTO products 
        (name, description, image, price, quantity, category_id, type_id, family_id) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->execute([
        $name,
        $description,
        $image_path,
        $price,
        $quantity,
        $category_id,
        $type_id,
        $_SESSION['user_id']
    ]);

    // تسجيل النشاط
    logActivity($_SESSION['user_id'], "تم إضافة منتج جديد: $name");

    header('Location: /family/dashboard.php?success=product_added');
    exit();

} catch (PDOException $e) {
    error_log("Add product error: " . $e->getMessage());
    header('Location: /family/add_product.php?error=server_error');
    exit();
}
?>