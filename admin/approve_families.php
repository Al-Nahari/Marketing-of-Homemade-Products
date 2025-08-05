<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';

if (!isAdminLoggedIn()) {
    header('Location: /ene/public/login.php');
    exit();
}

// جلب العائلات المنتجة التي تنتظر الموافقة
try {
    $stmt = $pdo->query("SELECT * FROM users 
                        WHERE user_type = 'family' 
                        AND approval_status = 'pending'
                        ORDER BY created_at DESC");
    $pending_families = $stmt->fetchAll();

} catch (PDOException $e) {
    error_log("Approve families error: " . $e->getMessage());
    $error = "حدث خطأ في جلب البيانات";
}

include '../includes/header.php';
?>

<div class="container mt-5">
    <h1 class="mb-4">طلبات العائلات المنتجة</h1>
    
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success">
            <?php
            $messages = [
                'action_completed' => 'تم تنفيذ العملية بنجاح',
            ];
            echo $messages[$_GET['success']] ?? 'تمت العملية بنجاح';
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger">
            <?php
            $messages = [
                'invalid_data' => 'بيانات غير صالحة',
                'family_not_found' => 'العائلة غير موجودة',
                'server_error' => 'خطأ في الخادم'
            ];
            echo $messages[$_GET['error']] ?? 'حدث خطأ ما';
            ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الاسم</th>
                        <th>البريد الإلكتروني</th>
                        <th>المدينة</th>
                        <th>الهاتف</th>
                        <th>تاريخ التسجيل</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($pending_families ?? [] as $family): ?>
                    <tr>
                        <td><?= $family['id'] ?></td>
                        <td><?= htmlspecialchars($family['full_name']) ?></td>
                        <td><?= htmlspecialchars($family['email']) ?></td>
                        <td><?= htmlspecialchars($family['city']) ?></td>
                        <td><?= htmlspecialchars($family['phone']) ?></td>
                        <td><?= date('Y/m/d', strtotime($family['created_at'])) ?></td>
                        <td>
                            <form method="post" action="/ene/actions/approve_family_action.php" class="d-inline">
                                <input type="hidden" name="family_id" value="<?= $family['id'] ?>">
                                <button type="submit" name="action" value="approve" class="btn btn-success btn-sm">موافقة</button>
                                <button type="submit" name="action" value="reject" class="btn btn-danger btn-sm">رفض</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>

                    <?php if (empty($pending_families)): ?>
                    <tr>
                        <td colspan="7" class="text-center">لا توجد طلبات جديدة</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>