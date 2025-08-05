<?php
// في بداية الملف - تأكد من استدعاء ملف الاتصال أولاً
require_once __DIR__ . '/../config/db.php';

// الحصول على اتصال PDO
$pdo = Database::getInstance();

// الآن يمكنك تنفيذ الاستعلامات بأمان
try {
    // معالجة معاملات البحث والتصفية
    $search = $_GET['search'] ?? '';
    $category_id = $_GET['category'] ?? '';
    $family_id = $_GET['family'] ?? '';
    $min_price = $_GET['min_price'] ?? '';
    $max_price = $_GET['max_price'] ?? '';

    // بناء استعلام SQL مع عوامل التصفية
    $query = "
        SELECT p.*, u.full_name as family_name, c.name as category_name
        FROM products p
        JOIN users u ON p.family_id = u.id
        JOIN categories c ON p.category_id = c.id
        WHERE p.status = 'available'
    ";

    $params = [];
    $conditions = [];

    if (!empty($search)) {
        $conditions[] = "(p.name LIKE ? OR p.description LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

    if (!empty($category_id)) {
        $conditions[] = "p.category_id = ?";
        $params[] = $category_id;
    }

    if (!empty($family_id)) {
        $conditions[] = "p.family_id = ?";
        $params[] = $family_id;
    }

    if (!empty($min_price)) {
        $conditions[] = "p.price >= ?";
        $params[] = $min_price;
    }

    if (!empty($max_price)) {
        $conditions[] = "p.price <= ?";
        $params[] = $max_price;
    }

    if (!empty($conditions)) {
        $query .= " AND " . implode(" AND ", $conditions);
    }

    // إضافة الترتيب
    $query .= " ORDER BY p.created_at DESC";

    // جلب التصنيفات لعرضها في الفلتر
    $categories_stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
    $categories = $categories_stmt->fetchAll();

    // جلب المنتجات مع التطبيق الفلاتر
    $products_stmt = $pdo->prepare($query);
    $products_stmt->execute($params);
    $products = $products_stmt->fetchAll();

} catch (PDOException $e) {
    error_log("Products page error: " . $e->getMessage());
    $error = "حدث خطأ في جلب البيانات";
}

$page_title = "تصفح المنتجات";
include __DIR__ . '/../includes/header.php';
?>
<div class="container py-5">
    <div class="row">
        <!-- جانب الفلترة -->
        <div class="col-md-3">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">تصفية المنتجات</h5>
                </div>
                <div class="card-body">
                    <form method="get" action="/public/products.php">
                        <div class="mb-3">
                            <label for="search" class="form-label">بحث</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="<?= htmlspecialchars($search) ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label for="category" class="form-label">التصنيف</label>
                            <select class="form-select" id="category" name="category">
                                <option value="">جميع التصنيفات</option>
                                <?php foreach($categories as $category): ?>
                                <option value="<?= $category['id'] ?>" 
                                    <?= $category_id == $category['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($category['name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">نطاق السعر</label>
                            <div class="row g-2">
                                <div class="col">
                                    <input type="number" class="form-control" placeholder="الحد الأدنى" 
                                           name="min_price" value="<?= htmlspecialchars($min_price) ?>">
                                </div>
                                <div class="col">
                                    <input type="number" class="form-control" placeholder="الحد الأقصى" 
                                           name="max_price" value="<?= htmlspecialchars($max_price) ?>">
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100">تطبيق الفلتر</button>
                        <a href="public/products.php" class="btn btn-outline-secondary w-100 mt-2">إعادة تعيين</a>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- قائمة المنتجات -->
        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold">تصفح المنتجات</h2>
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" 
                            data-bs-toggle="dropdown">
                        ترتيب حسب
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="?sort=newest">الأحدث</a></li>
                        <li><a class="dropdown-item" href="?sort=price_asc">السعر: من الأقل للأعلى</a></li>
                        <li><a class="dropdown-item" href="?sort=price_desc">السعر: من الأعلى للأقل</a></li>
                    </ul>
                </div>
            </div>
            
            <?php if (!empty($products)): ?>
                <div class="row g-4">
                    <?php foreach($products as $product): ?>
                    <div class="col-md-4">
                        <div class="card h-100 product-card">
                            <img src="<?= $product['image'] ?: '/assets/images/product-placeholder.png' ?>" 
                                 class="card-img-top" 
                                 alt="<?= htmlspecialchars($product['name']) ?>"
                                 style="height: 200px; object-fit: cover;">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                                <p class="text-muted small">
                                    <span class="d-block"><?= htmlspecialchars($product['category_name']) ?></span>
                                    <span class="d-block">من <?= htmlspecialchars($product['family_name']) ?></span>
                                </p>
                                <p class="card-text text-success fw-bold">
                                    <?= number_format($product['price'], 2) ?> ر.س
                                </p>
                            </div>
                            <div class="card-footer bg-white">
                                <a href="public/product_details.php?id=<?= $product['id'] ?>" 
                                   class="btn btn-sm btn-primary">التفاصيل</a>
                                <button class="btn btn-sm btn-outline-secondary">
                                    <i class="far fa-heart"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info text-center py-5">
                    <i class="fas fa-box-open fa-3x mb-3 text-muted"></i>
                    <h4>لا توجد منتجات متاحة</h4>
                    <p class="mb-0">لم يتم العثور على منتجات تطابق معايير البحث</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>