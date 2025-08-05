<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';

if (!isAdminLoggedIn()) {
    header('Location: /ene/public/login.php');
    exit();
}

// جلب الإحصائيات
try {
    // إحصائيات المنتجات
    $products_stats = $pdo->query("
        SELECT 
            COUNT(*) as total_products,
            SUM(CASE WHEN status = 'available' THEN 1 ELSE 0 END) as available,
            SUM(CASE WHEN status = 'out_of_stock' THEN 1 ELSE 0 END) as out_of_stock
        FROM products
    ")->fetch();

    // إحصائيات الطلبات
    $orders_stats = $pdo->query("
        SELECT 
            COUNT(*) as total_orders,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as delivered,
            SUM(total_price) as total_sales
        FROM orders
    ")->fetch();

    // المنتجات الأكثر مبيعاً
    $top_products = $pdo->query("
        SELECT p.name, COUNT(o.id) as orders_count, SUM(o.quantity) as total_quantity
        FROM orders o
        JOIN products p ON o.product_id = p.id
        GROUP BY p.id
        ORDER BY total_quantity DESC
        LIMIT 5
    ")->fetchAll();

    // العائلات الأكثر مبيعاً
    $top_families = $pdo->query("
        SELECT u.full_name, COUNT(o.id) as orders_count, SUM(o.total_price) as total_sales
        FROM orders o
        JOIN users u ON o.seller_id = u.id
        GROUP BY u.id
        ORDER BY total_sales DESC
        LIMIT 5
    ")->fetchAll();

} catch (PDOException $e) {
    error_log("Reports error: " . $e->getMessage());
    $error = "حدث خطأ في جلب البيانات";
}

include '../includes/header.php';
?>

<div class="container mt-5">
    <h1 class="mb-4">التقارير والإحصائيات</h1>
    
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>إحصائيات المنتجات</h5>
                </div>
                <div class="card-body">
                    <canvas id="productsChart" height="200"></canvas>
                    <ul class="list-group mt-3">
                        <li class="list-group-item">
                            <strong>إجمالي المنتجات:</strong> <?= $products_stats['total_products'] ?? 0 ?>
                        </li>
                        <li class="list-group-item">
                            <strong>متاحة:</strong> <?= $products_stats['available'] ?? 0 ?>
                        </li>
                        <li class="list-group-item">
                            <strong>غير متاحة:</strong> <?= $products_stats['out_of_stock'] ?? 0 ?>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>إحصائيات الطلبات</h5>
                </div>
                <div class="card-body">
                    <canvas id="ordersChart" height="200"></canvas>
                    <ul class="list-group mt-3">
                        <li class="list-group-item">
                            <strong>إجمالي الطلبات:</strong> <?= $orders_stats['total_orders'] ?? 0 ?>
                        </li>
                        <li class="list-group-item">
                            <strong>المبيعات الإجمالية:</strong> <?= number_format($orders_stats['total_sales'] ?? 0, 2) ?> ر.س
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>المنتجات الأكثر مبيعاً</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>المنتج</th>
                                <th>عدد الطلبات</th>
                                <th>الكمية المباعة</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($top_products ?? [] as $product): ?>
                            <tr>
                                <td><?= htmlspecialchars($product['name']) ?></td>
                                <td><?= $product['orders_count'] ?></td>
                                <td><?= $product['total_quantity'] ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>العائلات الأكثر مبيعاً</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>العائلة</th>
                                <th>عدد الطلبات</th>
                                <th>إجمالي المبيعات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($top_families ?? [] as $family): ?>
                            <tr>
                                <td><?= htmlspecialchars($family['full_name']) ?></td>
                                <td><?= $family['orders_count'] ?></td>
                                <td><?= number_format($family['total_sales'], 2) ?> ر.س</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- مكتبة Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// رسم مخطط المنتجات
const productsCtx = document.getElementById('productsChart').getContext('2d');
const productsChart = new Chart(productsCtx, {
    type: 'doughnut',
    data: {
        labels: ['متاحة', 'غير متاحة'],
        datasets: [{
            data: [<?= $products_stats['available'] ?? 0 ?>, <?= $products_stats['out_of_stock'] ?? 0 ?>],
            backgroundColor: ['#28a745', '#ffc107'],
        }]
    }
});

// رسم مخطط الطلبات
const ordersCtx = document.getElementById('ordersChart').getContext('2d');
const ordersChart = new Chart(ordersCtx, {
    type: 'bar',
    data: {
        labels: ['طلبات جديدة', 'طلبات مكتملة'],
        datasets: [{
            label: 'الطلبات',
            data: [<?= $orders_stats['pending'] ?? 0 ?>, <?= $orders_stats['delivered'] ?? 0 ?>],
            backgroundColor: ['#ffc107', '#28a745'],
        }]
    }
});
</script>

<?php include '../includes/footer.php'; ?>