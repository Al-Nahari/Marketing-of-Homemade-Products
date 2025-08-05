<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';

if (!isFamilyLoggedIn()) {
    header('Location: /ene/public/login.php');
    exit();
}

// جلب بيانات العائلة
$family_id = $_SESSION['user_id'];
try {
    // إحصائيات العائلة
    $stats_stmt = $pdo->prepare("
        SELECT 
            COUNT(p.id) as total_products,
            SUM(CASE WHEN p.status = 'available' THEN 1 ELSE 0 END) as available_products,
            COUNT(o.id) as total_orders,
            SUM(CASE WHEN o.status = 'delivered' THEN o.total_price ELSE 0 END) as total_sales
        FROM products p
        LEFT JOIN orders o ON p.id = o.product_id
        WHERE p.family_id = ?
    ");
    $stats_stmt->execute([$family_id]);
    $stats = $stats_stmt->fetch();

    // جلب الطلبات الأخيرة
    $orders_stmt = $pdo->prepare("
        SELECT o.*, u.full_name as buyer_name, p.name as product_name
        FROM orders o
        JOIN users u ON o.buyer_id = u.id
        JOIN products p ON o.product_id = p.id
        WHERE o.seller_id = ?
        ORDER BY o.order_date DESC LIMIT 5
    ");
    $orders_stmt->execute([$family_id]);
    $recent_orders = $orders_stmt->fetchAll();

    // جلب المنتجات الأكثر مبيعاً
    $top_products_stmt = $pdo->prepare("
        SELECT p.name, COUNT(o.id) as orders_count
        FROM products p
        LEFT JOIN orders o ON p.id = o.product_id
        WHERE p.family_id = ?
        GROUP BY p.id
        ORDER BY orders_count DESC LIMIT 3
    ");
    $top_products_stmt->execute([$family_id]);
    $top_products = $top_products_stmt->fetchAll();

} catch (PDOException $e) {
    error_log("Family dashboard error: " . $e->getMessage());
    $error = "حدث خطأ في جلب البيانات";
}

$page_title = "لوحة التحكم - العائلة المنتجة";
include '../includes/header.php';
?>

<div class="container mt-5">
    <h1 class="mb-4">لوحة تحكم العائلة المنتجة</h1>
    
    <!-- بطاقات الإحصائيات -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">المنتجات</h5>
                    <p class="card-text display-4"><?= $stats['total_products'] ?? 0 ?></p>
                    <small><?= $stats['available_products'] ?? 0 ?> متاح</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">الطلبات</h5>
                    <p class="card-text display-4"><?= $stats['total_orders'] ?? 0 ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">المبيعات</h5>
                    <p class="card-text display-4"><?= number_format($stats['total_sales'] ?? 0, 2) ?> ر.س</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h5 class="card-title">إجراءات سريعة</h5>
                    <a href="family/add_product.php" class="btn btn-dark btn-sm mb-2">إضافة منتج</a>
                    <a href="family/products.php" class="btn btn-dark btn-sm mb-2">إدارة المنتجات</a>
                    <a href="family/orders.php" class="btn btn-dark btn-sm">عرض الطلبات</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- الطلبات الأخيرة -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5>الطلبات الأخيرة</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($recent_orders)): ?>
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>رقم الطلب</th>
                                    <th>المنتج</th>
                                    <th>المشتري</th>
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
                                    <td><?= htmlspecialchars($order['buyer_name']) ?></td>
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
                        <a href="family/orders.php" class="btn btn-outline-primary">عرض جميع الطلبات</a>
                    <?php else: ?>
                        <div class="alert alert-info">لا توجد طلبات حتى الآن</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- المنتجات الأكثر مبيعاً -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5>المنتجات الأكثر مبيعاً</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($top_products)): ?>
                        <ul class="list-group">
                            <?php foreach($top_products as $product): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?= htmlspecialchars($product['name']) ?>
                                <span class="badge bg-primary rounded-pill"><?= $product['orders_count'] ?></span>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <div class="alert alert-info">لا توجد مبيعات حتى الآن</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>