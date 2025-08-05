<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/db.php';
function isAdminLoggedIn() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin';
}

if (!isAdminLoggedIn()) {
    header('Location: /ene/public/login.php');
    exit();
}

// جلب جميع المستخدمين
try {
    $stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
    $users = $stmt->fetchAll();

} catch (PDOException $e) {
    error_log("Manage users error: " . $e->getMessage());
    $error = "حدث خطأ في جلب البيانات";
}

include '../includes/header.php';
?>

<div class="container mt-5">
    <h1 class="mb-4">إدارة المستخدمين</h1>
    
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>الاسم</th>
                            <th>البريد الإلكتروني</th>
                            <th>نوع المستخدم</th>
                            <th>المدينة</th>
                            <th>الحالة</th>
                            <th>تاريخ التسجيل</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($users ?? [] as $user): ?>
                        <tr>
                            <td><?= $user['id'] ?></td>
                            <td><?= htmlspecialchars($user['full_name']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td>
                                <span class="badge <?= 
                                    $user['user_type'] === 'admin' ? 'bg-danger' : 
                                    ($user['user_type'] === 'family' ? 'bg-primary' : 'bg-secondary') 
                                ?>">
                                    <?= getUserTypeText($user['user_type']) ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($user['city'] ?? 'غير محدد') ?></td>
                            <td>
                                <span class="badge <?= 
                                    $user['status'] === 'active' ? 'bg-success' : 
                                    ($user['status'] === 'blocked' ? 'bg-danger' : 'bg-warning text-dark') 
                                ?>">
                                    <?= getUserStatusText($user['status']) ?>
                                </span>
                            </td>
                            <td><?= date('Y/m/d', strtotime($user['created_at'])) ?></td>
                            <td>
                                <div class="btn-group">
                                    <?php if ($user['status'] === 'active'): ?>
                                    <a href="/ene/actions/block_user_action.php?id=<?= $user['id'] ?>" class="btn btn-warning btn-sm">حظر</a>
                                    <?php else: ?>
                                    <a href="/ene/actions/activate_user_action.php?id=<?= $user['id'] ?>" class="btn btn-success btn-sm">تفعيل</a>
                                    <?php endif; ?>
                                    <button class="btn btn-danger btn-sm" onclick="confirmDelete(<?= $user['id'] ?>)">حذف</button>
                                </div>
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
function confirmDelete(userId) {
    if (confirm('هل أنت متأكد من حذف هذا المستخدم؟')) {
        window.location.href = '/ene/actions/delete_user_action.php?id=' + userId;
    }
}
</script>

<?php include '../includes/footer.php'; ?>