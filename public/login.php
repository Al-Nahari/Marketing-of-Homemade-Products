<?php
// إذا كان المستخدم مسجل دخوله بالفعل، يتم توجيهه
if (isset($_SESSION['user_id'])) {
    header('Location: /');
    exit();
}

$page_title = "تسجيل الدخول";
include '../includes/header.php';
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">تسجيل الدخول</h4>
                </div>
                <div class="card-body">
                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger">
                            <?php
                            $messages = [
                                'invalid_request' => 'طلب غير صالح',
                                'empty_fields' => 'جميع الحقول مطلوبة',
                                'invalid_credentials' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة',
                                'account_not_active' => 'الحساب غير مفعل، يرجى التواصل مع الإدارة'
                            ];
                            echo $messages[$_GET['error']] ?? 'حدث خطأ أثناء تسجيل الدخول';
                            ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_GET['success'])): ?>
                        <div class="alert alert-success">
                            <?php
                            $messages = [
                                'registration_complete' => 'تم التسجيل بنجاح، يرجى تسجيل الدخول',
                                'password_updated' => 'تم تحديث كلمة المرور بنجاح، يرجى تسجيل الدخول'
                            ];
                            echo $messages[$_GET['success']] ?? 'تمت العملية بنجاح';
                            ?>
                        </div>
                    <?php endif; ?>

                    <form action="/ene/actions/login_action.php" method="post">
                        <div class="mb-3">
                            <label for="email" class="form-label">البريد الإلكتروني</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">كلمة المرور</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                            <label class="form-check-label" for="remember">تذكرني</label>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">تسجيل الدخول</button>
                        </div>
                    </form>

                    <hr class="my-4">

                    <div class="text-center">
                        <a href="/ene/public/forgot_password.php" class="text-decoration-none">نسيت كلمة المرور؟</a>
                        <p class="mt-3">ليس لديك حساب؟ <a href="/ene/public/register.php" class="text-primary">سجل الآن</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>