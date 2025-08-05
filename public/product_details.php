<?php
require_once __DIR__ . '/../config/db.php';

// التحقق من وجود معرف المنتج
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: /public/products.php');
    exit();
}

$product_id = $_GET['id'];

// جلب بيانات المنتج
try {
    $product_stmt = $pdo->prepare("
        SELECT p.*, u.full_name as family_name, u.city as family_city, 
               c.name as category_name, t.name as type_name
        FROM products p
        JOIN users u ON p.family_id = u.id
        JOIN categories c ON p.category_id = c.id
        LEFT JOIN product_types t ON p.type_id = t.id
        WHERE p.id = ? AND p.status = 'available'
    ");
    $product_stmt->execute([$product_id]);
    $product = $product_stmt->fetch();

    if (!$product) {
        header('Location: /public/products.php?error=product_not_found');
        exit();
    }

    // جلب منتجات مشابهة من نفس التصنيف
    $related_stmt = $pdo->prepare("
        SELECT p.*, u.full_name as family_name
        FROM products p
        JOIN users u ON p.family_id = u.id
        WHERE p.category_id = ? AND p.id != ? AND p.status = 'available'
        ORDER BY RAND() LIMIT 4
    ");
    $related_stmt->execute([$product['category_id'], $product_id]);
    $related_products = $related_stmt->fetchAll();

} catch (PDOException $e) {
    error_log("Product details error: " . $e->getMessage());
    header('Location: /public/products.php?error=server_error');
    exit();
}

$page_title = htmlspecialchars($product['name']) . " - تفاصيل المنتج";
include '../includes/header.php';
?>

<div class="container py-5">
    <div class="row">
        <div class="col-md-6">
            <!-- معرض صور المنتج -->
            <div class="product-gallery mb-4">
                <div class="main-image mb-3">
                    <img src="<?= $product['image'] ?: '/assets/images/product-placeholder.png' ?>" 
                         class="img-fluid rounded border" 
                         alt="<?= htmlspecialchars($product['name']) ?>" 
                         id="mainImage">
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="product-details">
                <h1 class="fw-bold mb-3"><?= htmlspecialchars($product['name']) ?></h1>
                
                <div class="d-flex align-items-center mb-3">
                    <span class="text-success fw-bold fs-4 me-3">
                        <?= number_format($product['price'], 2) ?> ر.س
                    </span>
                    <?php if ($product['quantity'] > 0): ?>
                        <span class="badge bg-success">متوفر</span>
                    <?php else: ?>
                        <span class="badge bg-danger">غير متوفر</span>
                    <?php endif; ?>
                </div>
                
                <div class="mb-4">
                    <p class="mb-2"><strong>التصنيف:</strong> <?= htmlspecialchars($product['category_name']) ?></p>
                    <?php if (!empty($product['type_name'])): ?>
                        <p class="mb-2"><strong>النوع:</strong> <?= htmlspecialchars($product['type_name']) ?></p>
                    <?php endif; ?>
                    <p class="mb-2"><strong>الكمية المتاحة:</strong> <?= $product['quantity'] ?></p>
                </div>
                
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">من العائلة المنتجة</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <img src="/assets/images/family-avatar.png" 
                                 alt="<?= htmlspecialchars($product['family_name']) ?>" 
                                 class="rounded-circle me-3" width="60">
                            <div>
                                <h6 class="mb-1"><?= htmlspecialchars($product['family_name']) ?></h6>
                                <p class="text-muted small mb-1">
                                    <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($product['family_city']) ?>
                                </p>
                                <a href="public/products.php?family=<?= $product['family_id'] ?>" 
                                   class="btn btn-sm btn-outline-primary">عرض جميع منتجات العائلة</a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="product-actions mb-4">
                    <form action="/actions/add_to_cart.php" method="post">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        <div class="row g-2 align-items-center mb-3">
                            <div class="col-auto">
                                <label for="quantity" class="col-form-label">الكمية:</label>
                            </div>
                            <div class="col-4">
                                <input type="number" class="form-control" id="quantity" name="quantity" 
                                       value="1" min="1" max="<?= $product['quantity'] ?>">
                            </div>
                            <div class="col">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-shopping-cart me-2"></i> أضف إلى السلة
                                </button>
                            </div>
                        </div>
                    </form>
                    
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-secondary flex-grow-1">
                            <i class="far fa-heart me-2"></i> المفضلة
                        </button>
                        <button class="btn btn-outline-secondary flex-grow-1">
                            <i class="fas fa-share-alt me-2"></i> مشاركة
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- تفاصيل إضافية -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <ul class="nav nav-tabs card-header-tabs">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#description">وصف المنتج</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#reviews">التقييمات</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="description">
                            <p><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                        </div>
                        <div class="tab-pane fade" id="reviews">
                            <div class="text-center py-4">
                                <i class="fas fa-comment-alt fa-3x text-muted mb-3"></i>
                                <h5>لا توجد تقييمات بعد</h5>
                                <p>كن أول من يقيم هذا المنتج</p>
                                <button class="btn btn-primary">إضافة تقييم</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- منتجات مشابهة -->
    <?php if (!empty($related_products)): ?>
    <div class="row mt-5">
        <div class="col-12">
            <h3 class="fw-bold mb-4">منتجات مشابهة</h3>
            <div class="row g-4">
                <?php foreach($related_products as $related): ?>
                <div class="col-md-3">
                    <div class="card h-100 product-card">
                        <img src="<?= $related['image'] ?: '/assets/images/product-placeholder.png' ?>" 
                             class="card-img-top" 
                             alt="<?= htmlspecialchars($related['name']) ?>"
                             style="height: 150px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($related['name']) ?></h5>
                            <p class="text-muted small">من <?= htmlspecialchars($related['family_name']) ?></p>
                            <p class="card-text text-success fw-bold">
                                <?= number_format($related['price'], 2) ?> ر.س
                            </p>
                        </div>
                        <div class="card-footer bg-white">
                            <a href="public/product_details.php?id=<?= $related['id'] ?>" 
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
    </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>