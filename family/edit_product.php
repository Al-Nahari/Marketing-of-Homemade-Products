<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';

if (!isFamilyLoggedIn()) {
    header('Location: /public/login.php');
    exit();
}

// التحقق من وجود معرف المنتج
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: /family/dashboard.php?error=invalid_product');
    exit();
}

$product_id = $_GET['id'];
$family_id = $_SESSION['user_id'];

// جلب بيانات المنتج للتأكد من ملكيته للعائلة
try {
    // جلب بيانات المنتج
    $product_stmt = $pdo->prepare("SELECT * FROM products WHERE id = ? AND family_id = ?");
    $product_stmt->execute([$product_id, $family_id]);
    $product = $product_stmt->fetch();

    if (!$product) {
        header('Location: /family/dashboard.php?error=product_not_found');
        exit();
    }

    // جلب التصنيفات وأنواع المنتجات
    $categories_stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
    $categories = $categories_stmt->fetchAll();

    $types_stmt = $pdo->query("SELECT * FROM product_types ORDER BY name");
    $types = $types_stmt->fetchAll();

} catch (PDOException $e) {
    error_log("Edit product error: " . $e->getMessage());
    header('Location: /family/dashboard.php?error=server_error');
    exit();
}

$page_title = "تعديل المنتج";
include '../includes/header.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5>تعديل المنتج</h5>
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

                    <form action="/actions/update_product_action.php" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">

                        <div class="mb-3">
                            <label for="name" class="form-label">اسم المنتج</label>
                            <input type="text" class="form-control" id="name" name="name" 
                                   value="<?= htmlspecialchars($product['name']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">وصف المنتج</label>
                            <textarea class="form-control" id="description" name="description" 
                                      rows="3" required><?= htmlspecialchars($product['description']) ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="price" class="form-label">السعر (ر.س)</label>
                                <input type="number" class="form-control" id="price" name="price" 
                                       value="<?= $product['price'] ?>" min="0.01" step="0.01" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="quantity" class="form-label">الكمية المتاحة</label>
                                <input type="number" class="form-control" id="quantity" name="quantity" 
                                       value="<?= $product['quantity'] ?>" min="1" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="category_id" class="form-label">التصنيف</label>
                                <select class="form-select" id="category_id" name="category_id" required>
                                    <option value="">اختر تصنيفاً</option>
                                    <?php foreach($categories as $category): ?>
                                    <option value="<?= $category['id'] ?>" 
                                        <?= $category['id'] == $product['category_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($category['name']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="type_id" class="form-label">نوع المنتج</label>
                                <select class="form-select" id="type_id" name="type_id">
                                    <option value="">اختر نوعاً</option>
                                    <?php foreach($types as $type): ?>
                                    <option value="<?= $type['id'] ?>" 
                                        <?= $type['id'] == $product['type_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($type['name']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label">حالة المنتج</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="available" <?= $product['status'] === 'available' ? 'selected' : '' ?>>متاح</option>
                                <option value="out_of_stock" <?= $product['status'] === 'out_of_stock' ? 'selected' : '' ?>>غير متوفر</option>
                                <option value="archived" <?= $product['status'] === 'archived' ? 'selected' : '' ?>>مؤرشف</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="image" class="form-label">صورة المنتج</label>
                            <?php if ($product['image']): ?>
                            <div class="mb-2">
                                <img src="<?= $product['image'] ?>" alt="صورة المنتج الحالية" width="100" class="img-thumbnail">
                                <div class="form-check mt-2">
                                    <input class="form-check-input" type="checkbox" id="remove_image" name="remove_image">
                                    <label class="form-check-label" for="remove_image">حذف الصورة الحالية</label>
                                </div>
                            </div>
                            <?php endif; ?>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            <small class="text-muted">اتركه فارغاً للحفاظ على الصورة الحالية</small>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
                            <a href="family/dashboard.php" class="btn btn-outline-secondary">إلغاء</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>