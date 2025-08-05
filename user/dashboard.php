<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';

if (!isUserLoggedIn()) {
    header('Location: /ene/public/login.php');
    exit();
}

// جلب بيانات المستخدم
$user_id = $_SESSION['user_id'];
try {
    $user_stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $user_stmt->execute([$user_id]);
    $user = $user_stmt->fetch();

    // جلب طلبات المستخدم الأخيرة
    $orders_stmt = $pdo->prepare("SELECT o.*, p.name as product_name 
                                FROM orders o
                                JOIN products p ON o.product_id = p.id
                                WHERE o.buyer_id = ?
                                ORDER BY o.order_date DESC LIMIT 5");
    $orders_stmt->execute([$user_id]);
    $recent_orders = $orders_stmt->fetchAll();

    // جلب المنتجات المقترحة
    $products_stmt = $pdo->query("SELECT * FROM products 
                                WHERE status = 'available'
                                ORDER BY created_at DESC LIMIT 5");
    $suggested_products = $products_stmt->fetchAll();

} catch (PDOException $e) {
    error_log("User dashboard error: " . $e->getMessage());
    $error = "حدث خطأ في جلب البيانات";
}

$page_title = "لوحة التحكم - المستخدم";
include '../includes/header.php';
?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-4">
            <!-- بطاقة معلومات المستخدم -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5>معلوماتي</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <img src="/assets/images/user-avatar.png" alt="صورة المستخدم" class="rounded-circle" width="100">
                    </div>
                    <h4 class="text-center"><?= htmlspecialchars($user['full_name']) ?></h4>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">
                            <i class="fas fa-envelope me-2"></i> <?= htmlspecialchars($user['email']) ?>
                        </li>
                        <li class="list-group-item">
                            <i class="fas fa-phone me-2"></i> <?= htmlspecialchars($user['phone'] ?? 'غير محدد') ?>
                        </li>
                        <li class="list-group-item">
                            <i class="fas fa-city me-2"></i> <?= htmlspecialchars($user['city'] ?? 'غير محدد') ?>
                        </li>
                    </ul>
                    <a href="user/profile.php" class="btn btn-outline-primary mt-3 w-100">تعديل الملف الشخصي</a>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <!-- طلباتي الأخيرة -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5>طلباتي الأخيرة</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($recent_orders)): ?>
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>رقم الطلب</th>
                                    <th>المنتج</th>
                                    <th>المبلغ</th>
                                    <th>الحالة</th>
                                    <th>التاريخ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($recent_orders as $order): ?>
                                <tr>
                                    <td>#<?= $order['id'] ?></td>
                                    <td><?= htmlspecialchars($order['product_name']) ?></td>
                                    <td><?= number_format($order['total_price'], 2) ?> ر.س</td>
                                    <td>
                                        <span class="badge <?= 
                                            $order['status'] === 'delivered' ? 'bg-success' : 
                                            ($order['status'] === 'cancelled' ? 'bg-danger' : 'bg-warning text-dark')
                                        ?>">
                                            <?= getOrderStatusText($order['status']) ?>
                                        </span>
                                    </td>
                                    <td><?= date('Y/m/d', strtotime($order['order_date'])) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <a href="user/orders.php" class="btn btn-outline-success">عرض جميع الطلبات</a>
                    <?php else: ?>
                        <div class="alert alert-info">لا توجد طلبات سابقة</div>
                        <a href="/ene/public/products.php" class="btn btn-success">تصفح المنتجات</a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- منتجات مقترحة -->
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5>منتجات قد تعجبك</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php foreach($suggested_products as $product): ?>
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <img src="<?= $product['image'] ?: '/assets/images/product-placeholder.png' ?>" 
                                     class="card-img-top" 
                                     alt="<?= htmlspecialchars($product['name']) ?>" 
                                     style="height: 150px; object-fit: cover;">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                                    <p class="card-text text-success fw-bold">
                                        <?= number_format($product['price'], 2) ?> ر.س
                                    </p>
                                </div>
                                <div class="card-footer bg-white">
                                    <a href="/ene/public/product_details.php?id=<?= $product['id'] ?>" 
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
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>