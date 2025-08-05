<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';

if (!isFamilyLoggedIn()) {
    header('Location: /public/login.php');
    exit();
}

// جلب التصنيفات وأنواع المنتجات
try {
    $categories_stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
    $categories = $categories_stmt->fetchAll();

    $types_stmt = $pdo->query("SELECT * FROM product_types ORDER BY name");
    $types = $types_stmt->fetchAll();

} catch (PDOException $e) {
    error_log("Product form error: " . $e->getMessage());
    $error = "حدث خطأ في جلب البيانات";
}

$page_title = "إضافة منتج جديد";
include '../includes/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5>إضافة منتج جديد</h5>
                </div>
                <div class="card-body">
                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger">
                            <?php
                            $messages = [
                                'empty_fields' => 'جميع الحقول مطلوبة',
                                'invalid_price' => 'السعر غير صالح',
                                'invalid_quantity' => 'الكمية غير صالحة',
                                'invalid_category' => 'التصنيف غير صالح',
                                'invalid_image_type' => 'نوع الصورة غير مدعوم',
                                'upload_failed' => 'فشل رفع الصورة',
                                'server_error' => 'خطأ في الخادم'
                            ];
                            echo $messages[$_GET['error']] ?? 'حدث خطأ ما';
                            ?>
                        </div>
                    <?php endif; ?>

                    <form action="/actions/add_product_action.php" method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="name" class="form-label">اسم المنتج</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">وصف المنتج</label>
                            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="price" class="form-label">السعر (ر.س)</label>
                                <input type="number" class="form-control" id="price" name="price" min="0.01" step="0.01" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="quantity" class="form-label">الكمية المتاحة</label>
                                <input type="number" class="form-control" id="quantity" name="quantity" min="1" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="category_id" class="form-label">التصنيف</label>
                                <select class="form-select" id="category_id" name="category_id" required>
                                    <option value="">اختر تصنيفاً</option>
                                    <?php foreach($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="type_id" class="form-label">نوع المنتج</label>
                                <select class="form-select" id="type_id" name="type_id">
                                    <option value="">اختر نوعاً</option>
                                    <?php foreach($types as $type): ?>
                                    <option value="<?= $type['id'] ?>"><?= htmlspecialchars($type['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">صورة المنتج</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            <small class="text-muted">الصيغ المدعومة: JPG, PNG, WEBP</small>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">حفظ المنتج</button>
                            <a href="family/dashboard.php" class="btn btn-outline-secondary">إلغاء</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>