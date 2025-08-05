<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

$page_title = "الصفحة الرئيسية - نظام العائلات المنتجة";
include __DIR__ . '/../includes/header.php';

try {
    // جلب أحدث المنتجات
    $products_stmt = $pdo->query("
        SELECT p.*, u.full_name as family_name 
        FROM products p
        JOIN users u ON p.family_id = u.id
        WHERE p.status = 'available'
        ORDER BY p.created_at DESC 
        LIMIT 8
    ");
    $latest_products = $products_stmt->fetchAll();

    // جلب أفضل العائلات المنتجة
    $families_stmt = $pdo->query("
        SELECT u.*, COUNT(p.id) as products_count
        FROM users u
        LEFT JOIN products p ON u.id = p.family_id
        WHERE u.user_type = 'family' AND u.status = 'active'
        GROUP BY u.id
        ORDER BY products_count DESC
        LIMIT 4
    ");
    $top_families = $families_stmt->fetchAll();

} catch (PDOException $e) {
    error_log("Home page error: " . $e->getMessage());
    $error = "حدث خطأ في جلب البيانات";
}
?>

<!-- قسم الهيرو -->
<section class="hero-section bg-light py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="display-4 fw-bold mb-4">اكتشف منتجات العائلات المنتجة</h1>
                <p class="lead mb-4">منتجات يدوية بجودة عالية من صنع أيدي سعودية</p>
                <a href="products.php" class="btn btn-primary btn-lg px-4">تصفح المنتجات</a>
            </div>
            <div class="col-md-6">
                <img src="/assets/images/hero-image.jpg" alt="منتجات العائلات المنتجة" class="img-fluid rounded shadow">
            </div>
        </div>
    </div>
</section>

<!-- أحدث المنتجات -->
<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold">أحدث المنتجات</h2>
            <a href="products.php" class="btn btn-outline-primary">عرض الكل</a>
        </div>
        
        <div class="row g-4">
            <?php foreach($latest_products as $product): ?>
            <div class="col-md-3">
                <div class="card h-100 product-card">
                    <div class="badge bg-success position-absolute" style="top: 10px; right: 10px">جديد</div>
                    <img src="<?= $product['image'] ?: '/assets/images/product-placeholder.png' ?>" 
                         class="card-img-top" 
                         alt="<?= htmlspecialchars($product['name']) ?>"
                         style="height: 200px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                        <p class="text-muted small">من <?= htmlspecialchars($product['family_name']) ?></p>
                        <p class="card-text text-success fw-bold">
                            <?= number_format($product['price'], 2) ?> ر.س
                        </p>
                    </div>
                    <div class="card-footer bg-white">
                        <a href="product_details.php?id=<?= $product['id'] ?>" 
                           class="btn btn-sm btn-primary">التفاصيل</a>
                        <button class="btn btn-sm btn-outline-secondary">
                            <i class="far fa-heart"></i>
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- أفضل العائلات المنتجة -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="fw-bold mb-4 text-center">أفضل العائلات المنتجة</h2>
        
        <div class="row g-4">
            <?php foreach($top_families as $family): ?>
            <div class="col-md-3">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <img src="/assets/images/family-avatar.png" 
                             alt="<?= htmlspecialchars($family['full_name']) ?>" 
                             class="rounded-circle mb-3" width="100">
                        <h5 class="card-title"><?= htmlspecialchars($family['full_name']) ?></h5>
                        <p class="text-muted small">
                            <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($family['city'] ?? 'غير محدد') ?>
                        </p>
                        <span class="badge bg-primary">
                            <?= $family['products_count'] ?> منتج
                        </span>
                    </div>
                    <div class="card-footer bg-white">
                        <a href="products.php?family=<?= $family['id'] ?>" 
                           class="btn btn-sm btn-outline-primary">عرض المنتجات</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- لماذا نختارنا -->
<section class="py-5">
    <div class="container">
        <h2 class="fw-bold mb-5 text-center">لماذا تختار منتجاتنا؟</h2>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="text-center p-4">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3 d-inline-block mb-3">
                        <i class="fas fa-award fa-2x"></i>
                    </div>
                    <h4>جودة عالية</h4>
                    <p class="text-muted">منتجات يدوية الصنع بجودة عالية ومواد طبيعية</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center p-4">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3 d-inline-block mb-3">
                        <i class="fas fa-hand-holding-heart fa-2x"></i>
                    </div>
                    <h4>دعم مباشر</h4>
                    <p class="text-muted">نحن ندعم العائلات المنتجة بشكل مباشر</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="text-center p-4">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3 d-inline-block mb-3">
                        <i class="fas fa-truck fa-2x"></i>
                    </div>
                    <h4>توصيل سريع</h4>
                    <p class="text-muted">خدمة توصيل سريعة لجميع أنحاء المملكة</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>