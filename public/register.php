<?php
// إذا كان المستخدم مسجل دخوله بالفعل، يتم توجيهه
if (isset($_SESSION['user_id'])) {
    header('Location: /');
    exit();
}

$page_title = "تسجيل جديد";
include '../includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">إنشاء حساب جديد</h4>
                </div>
                <div class="card-body">
                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger">
                            <?php
                            $messages = [
                                'invalid_request' => 'طلب غير صالح',
                                'empty_full_name' => 'الاسم الكامل مطلوب',
                                'invalid_email' => 'بريد إلكتروني غير صالح',
                                'short_password' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
                                'password_mismatch' => 'كلمتا المرور غير متطابقتين',
                                'email_exists' => 'البريد الإلكتروني مسجل مسبقاً',
                                'server_error' => 'خطأ في الخادم'
                            ];
                            echo $messages[$_GET['error']] ?? 'حدث خطأ أثناء التسجيل';
                            ?>
                        </div>
                    <?php endif; ?>

                    <form action="/ene/actions/register_action.php" method="post">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="full_name" class="form-label">الاسم الكامل</label>
                                <input type="text" class="form-control" id="full_name" name="full_name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">البريد الإلكتروني</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">كلمة المرور</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="confirm_password" class="form-label">تأكيد كلمة المرور</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="city" class="form-label">المدينة</label>
                                <input type="text" class="form-control" id="city" name="city">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">رقم الجوال</label>
                                <input type="tel" class="form-control" id="phone" name="phone">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="user_type" class="form-label">نوع الحساب</label>
                            <select class="form-select" id="user_type" name="user_type" required>
                                <option value="">اختر نوع الحساب</option>
                                <option value="user">مشتري (أرغب بشراء المنتجات)</option>
                                <option value="family">عائلة منتجة (أرغب ببيع منتجاتي)</option>
                            </select>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="agree_terms" name="agree_terms" required>
                            <label class="form-check-label" for="agree_terms">أوافق على <a href="/ene/public/terms.php" class="text-decoration-none">الشروط والأحكام</a></label>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">تسجيل الحساب</button>
                        </div>
                    </form>

                    <hr class="my-4">

                    <div class="text-center">
                        <p>لديك حساب بالفعل؟ <a href="/ene/public/login.php" class="text-primary">سجل الدخول الآن</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>