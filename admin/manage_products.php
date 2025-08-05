<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';

if (!isAdminLoggedIn()) {
    header('Location: /public/login.php');
    exit();
}

// جلب جميع المنتجات مع معلومات العائلات
try {
    $stmt = $pdo->query("SELECT p.*, u.full_name as family_name 
                        FROM products p
                        JOIN users u ON p.family_id = u.id
                        ORDER BY p.created_at DESC");
    $products = $stmt->fetchAll();

} catch (PDOException $e) {
    error_log("Manage products error: " . $e->getMessage());
    $error = "حدث خطأ في جلب البيانات";
}

include '../includes/header.php';
?>

<div class="container mt-5">
    <h1 class="mb-4">إدارة المنتجات</h1>
    
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الصورة</th>
                            <th>اسم المنتج</th>
                            <th>السعر</th>
                            <th>الكمية</th>
                            <th>العائلة المنتجة</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($products ?? [] as $product): ?>
                        <tr>
                            <td><?= $product['id'] ?></td>
                            <td>
                                <?php if ($product['image']): ?>
                                <img src="<?= $product['image'] ?>" alt="صورة المنتج" width="50">
                                <?php else: ?>
                                <span class="text-muted">بدون صورة</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($product['name']) ?></td>
                            <td><?= number_format($product['price'], 2) ?> ر.س</td>
                            <td><?= $product['quantity'] ?></td>
                            <td><?= htmlspecialchars($product['family_name']) ?></td>
                            <td>
                                <span class="badge <?= 
                                    $product['status'] === 'available' ? 'bg-success' : 
                                    ($product['status'] === 'out_of_stock' ? 'bg-warning text-dark' : 'bg-secondary') 
                                ?>">
                                    <?= getProductStatusText($product['status']) ?>
                                </span>
                            </td>
                            <td>
                                <a href="family/edit_product.php?id=<?= $product['id'] ?>" class="btn btn-primary btn-sm">تعديل</a>
                                <button class="btn btn-danger btn-sm" onclick="confirmDelete(<?= $product['id'] ?>)">حذف</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(productId) {
    if (confirm('هل أنت متأكد من حذف هذا المنتج؟')) {
        window.location.href = 'actions/delete_product_action.php?id=' + productId;
    }
}
</script>

<?php include '../includes/footer.php'; ?>