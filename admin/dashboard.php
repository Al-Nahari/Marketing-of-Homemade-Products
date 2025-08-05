<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';

// التحقق من أن المستخدم مسؤول
if (!isAdmin()) {
    redirect('/public/login.php', 'يجب أن تكون مسؤولاً للوصول إلى هذه الصفحة', 'danger');
}

// معالجة طلب تغيير حالة العائلة
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['family_id'], $_POST['new_status'])) {
    try {
        $family_id = filter_input(INPUT_POST, 'family_id', FILTER_SANITIZE_NUMBER_INT);
        $new_status = in_array($_POST['new_status'], ['active', 'pending', 'blocked']) ? $_POST['new_status'] : 'pending';
        
        $stmt = $pdo->prepare("UPDATE users SET status = ? WHERE id = ? AND user_type = 'family'");
        $stmt->execute([$new_status, $family_id]);
        
        if ($stmt->rowCount() > 0) {
            logActivity($_SESSION['user_id'], "تغيير حالة العائلة #$family_id إلى $new_status");
            addFlashMessage('تم تحديث حالة العائلة بنجاح', 'success');
        } else {
            addFlashMessage('لم يتم العثور على العائلة', 'warning');
        }
        
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
        
    } catch (PDOException $e) {
        error_log("Status update error: " . $e->getMessage());
        addFlashMessage('حدث خطأ أثناء تحديث الحالة', 'danger');
    }
}

// جلب إحصائيات النظام
try {
    // عدد العائلات المنتجة
    $families_count = $pdo->query("SELECT COUNT(*) FROM users WHERE user_type = 'family'")->fetchColumn();
    
    // عدد المنتجات
    $products_count = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
    
    // عدد الطلبات الجديدة
    $new_orders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn();
    
    // أحدث العائلات المنتجة
    $new_families = $pdo->query("
        SELECT u.*, COUNT(p.id) as products_count 
        FROM users u
        LEFT JOIN products p ON u.id = p.family_id
        WHERE u.user_type = 'family'
        GROUP BY u.id
        ORDER BY u.created_at DESC 
        LIMIT 5
    ")->fetchAll();
    
    // أحدث الطلبات
    $recent_orders = $pdo->query("
        SELECT o.*, u.full_name as buyer_name, p.name as product_name
        FROM orders o
        JOIN users u ON o.buyer_id = u.id
        JOIN products p ON o.product_id = p.id
        ORDER BY o.order_date DESC 
        LIMIT 5
    ")->fetchAll();

} catch (PDOException $e) {
    error_log("Dashboard error: " . $e->getMessage());
    $error = "حدث خطأ في جلب البيانات";
}

$page_title = "لوحة تحكم الإدارة";
include '../includes/header.php';
?>

<div class="container mt-5">
    <h1 class="mb-4">لوحة تحكم الإدارة</h1>
    
    <?php displayFlashMessages(); ?>
    
    <!-- بطاقات الإحصائيات -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">العائلات المنتجة</h5>
                    <p class="card-text display-4"><?= $families_count ?? 0 ?></p>
                    <a href="manage_families.php" class="text-white">عرض الكل <i class="fas fa-arrow-left"></i></a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">المنتجات</h5>
                    <p class="card-text display-4"><?= $products_count ?? 0 ?></p>
                    <a href="manage_products.php" class="text-white">عرض الكل <i class="fas fa-arrow-left"></i></a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h5 class="card-title">طلبات جديدة</h5>
                    <p class="card-text display-4"><?= $new_orders ?? 0 ?></p>
                    <a href="manage_orders.php" class="text-dark">عرض الكل <i class="fas fa-arrow-left"></i></a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">إجمالي المستخدمين</h5>
                    <p class="card-text display-4"><?= ($families_count + $products_count) ?? 0 ?></p>
                    <a href="manage_users.php" class="text-white">عرض الكل <i class="fas fa-arrow-left"></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- أحدث العائلات مع خاصية تغيير الحالة -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">أحدث العائلات المنتجة</h5>
                    <a href="approve_families.php" class="btn btn-sm btn-outline-primary">طلبات جديدة</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>الاسم</th>
                                    <th>المدينة</th>
                                    <th>المنتجات</th>
                                    <th>الحالة</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($new_families ?? [] as $family): ?>
                                <tr>
                                    <td><?= htmlspecialchars($family['full_name']) ?></td>
                                    <td><?= htmlspecialchars($family['city']) ?></td>
                                    <td><?= $family['products_count'] ?></td>
                                    <td>
                                        <form method="post" class="status-form">
                                            <input type="hidden" name="family_id" value="<?= $family['id'] ?>">
                                            <select name="new_status" class="form-select form-select-sm status-select" 
                                                    style="width: auto; display: inline-block;">
                                                <option value="active" <?= $family['status'] === 'active' ? 'selected' : '' ?>>نشط</option>
                                                <option value="pending" <?= $family['status'] === 'pending' ? 'selected' : '' ?>>قيد الانتظار</option>
                                                <option value="blocked" <?= $family['status'] === 'blocked' ? 'selected' : '' ?>>محظور</option>
                                            </select>
                                            <button type="submit" class="btn btn-sm btn-outline-primary d-none">تحديث</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- أحدث الطلبات -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">أحدث الطلبات</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>رقم الطلب</th>
                                    <th>المنتج</th>
                                    <th>المشتري</th>
                                    <th>الحالة</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($recent_orders ?? [] as $order): ?>
                                <tr>
                                    <td>#<?= $order['id'] ?></td>
                                    <td><?= textExcerpt(htmlspecialchars($order['product_name']), 15) ?></td>
                                    <td><?= textExcerpt(htmlspecialchars($order['buyer_name']), 15) ?></td>
                                    <td>
                                        <span class="badge <?= 
                                            $order['status'] === 'delivered' ? 'bg-success' : 
                                            ($order['status'] === 'cancelled' ? 'bg-danger' : 'bg-warning text-dark')
                                        ?>">
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
</div>

<script>
// جافاسكريبت لتغيير الحالة عند تغيير القائمة المنسدلة
document.querySelectorAll('.status-select').forEach(select => {
    select.addEventListener('change', function() {
        // إظهار زر التحديث عند التغيير
        const submitBtn = this.closest('form').querySelector('button[type="submit"]');
        submitBtn.classList.remove('d-none');
        
        // يمكنك إضافة تأكيد هنا إذا أردت
        if (confirm('هل أنت متأكد من تغيير حالة هذه العائلة؟')) {
            this.closest('form').submit();
        } else {
            this.value = this.dataset.originalValue;
            submitBtn.classList.add('d-none');
        }
    });
    
    // حفظ القيمة الأصلية
    select.dataset.originalValue = select.value;
});
</script>

<?php include '../includes/footer.php'; ?>