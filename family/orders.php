<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';

if (!isFamilyLoggedIn()) {
    header('Location: /public/login.php');
    exit();
}

$family_id = $_SESSION['user_id'];

// جلب طلبات العائلة
try {
    $stmt = $pdo->prepare("
        SELECT o.*, u.full_name as buyer_name, p.name as product_name
        FROM orders o
        JOIN users u ON o.buyer_id = u.id
        JOIN products p ON o.product_id = p.id
        WHERE o.seller_id = ?
        ORDER BY o.order_date DESC
    ");
    $stmt->execute([$family_id]);
    $orders = $stmt->fetchAll();

} catch (PDOException $e) {
    error_log("Family orders error: " . $e->getMessage());
    $error = "حدث خطأ في جلب البيانات";
}

$page_title = "إدارة الطلبات - العائلة المنتجة";
include '../includes/header.php';
?>

<div class="container mt-5">
    <h1 class="mb-4">إدارة الطلبات</h1>
    
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">
            <?php
            $messages = [
                'status_updated' => 'تم تحديث حالة الطلب بنجاح',
            ];
            echo $messages[$_GET['success']] ?? 'تمت العملية بنجاح';
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger">
            <?php
            $messages = [
                'invalid_order' => 'طلب غير صالح',
                'invalid_status' => 'حالة غير صالحة',
                'server_error' => 'خطأ في الخادم'
            ];
            echo $messages[$_GET['error']] ?? 'حدث خطأ ما';
            ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <?php if (!empty($orders)): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>رقم الطلب</th>
                                <th>المنتج</th>
                                <th>المشتري</th>
                                <th>الكمية</th>
                                <th>المبلغ</th>
                                <th>حالة الطلب</th>
                                <th>التاريخ</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($orders as $order): ?>
                            <tr>
                                <td>#<?= $order['id'] ?></td>
                                <td><?= htmlspecialchars($order['product_name']) ?></td>
                                <td><?= htmlspecialchars($order['buyer_name']) ?></td>
                                <td><?= $order['quantity'] ?></td>
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
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-primary dropdown-toggle" 
                                                type="button" data-bs-toggle="dropdown">
                                            تغيير الحالة
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <form action="/actions/update_order_status.php" method="post">
                                                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                                    <button type="submit" name="status" value="processing" 
                                                            class="dropdown-item">قيد المعالجة</button>
                                                    <button type="submit" name="status" value="shipped" 
                                                            class="dropdown-item">تم الشحن</button>
                                                    <button type="submit" name="status" value="delivered" 
                                                            class="dropdown-item">تم التسليم</button>
                                                    <button type="submit" name="status" value="cancelled" 
                                                            class="dropdown-item text-danger">إلغاء الطلب</button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">لا توجد طلبات حتى الآن</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>