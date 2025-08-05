<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';

if (!isAdminLoggedIn()) {
    header('Location: /public/login.php');
    exit();
}

// إحصائيات النظام
try {
    // عدد العائلات المنتجة
    $families_stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE user_type = 'family'");
    $families_count = $families_stmt->fetchColumn();

    // عدد المنتجات
    $products_stmt = $pdo->query("SELECT COUNT(*) FROM products");
    $products_count = $products_stmt->fetchColumn();

    // عدد الطلبات الجديدة
    $orders_stmt = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'");
    $new_orders = $orders_stmt->fetchColumn();

    // أحدث العائلات المنتجة
    $new_families_stmt = $pdo->query("SELECT * FROM users 
                                     WHERE user_type = 'family' 
                                     ORDER BY created_at DESC LIMIT 5");
    $new_families = $new_families_stmt->fetchAll();

    // أحدث الطلبات
    $recent_orders_stmt = $pdo->query("SELECT o.*, u.full_name as buyer_name 
                                      FROM orders o 
                                      JOIN users u ON o.buyer_id = u.id 
                                      ORDER BY order_date DESC LIMIT 5");
    $recent_orders = $recent_orders_stmt->fetchAll();

} catch (PDOException $e) {
    error_log("Dashboard error: " . $e->getMessage());
    $error = "حدث خطأ في جلب البيانات";
}

include '../includes/header.php';
?>

<div class="container mt-5">
    <h1 class="mb-4">لوحة تحكم الإدارة</h1>
    
    <!-- بطاقات الإحصائيات -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">العائلات المنتجة</h5>
                    <p class="card-text display-4"><?= $families_count ?? 0 ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">المنتجات</h5>
                    <p class="card-text display-4"><?= $products_count ?? 0 ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h5 class="card-title">طلبات جديدة</h5>
                    <p class="card-text display-4"><?= $new_orders ?? 0 ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">إجمالي المستخدمين</h5>
                    <p class="card-text display-4"><?= $families_count + $products_count ?? 0 ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- أحدث العائلات -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>أحدث العائلات المنتجة</h5>
                </div>
                <div class="card-body">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>الاسم</th>
                                <th>المدينة</th>
                                <th>الحالة</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($new_families ?? [] as $family): ?>
                            <tr>
                                <td><?= htmlspecialchars($family['full_name']) ?></td>
                                <td><?= htmlspecialchars($family['city']) ?></td>
                                <td>
                                    <span class="badge <?= $family['status'] === 'active' ? 'bg-success' : 'bg-secondary' ?>">
                                        <?= $family['status'] === 'active' ? 'نشط' : 'قيد الانتظار' ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- أحدث الطلبات -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>أحدث الطلبات</h5>
                </div>
                <div class="card-body">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>رقم الطلب</th>
                                <th>المشتري</th>
                                <th>الحالة</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($recent_orders ?? [] as $order): ?>
                            <tr>
                                <td>#<?= $order['id'] ?></td>
                                <td><?= htmlspecialchars($order['buyer_name']) ?></td>
                                <td>
                                    <span class="badge bg-warning text-dark">
                                        <?= getOrderStatusText($order['status']) ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>