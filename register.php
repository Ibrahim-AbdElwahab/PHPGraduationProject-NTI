<?php
// 1. الاتصال بالداتا بيز
require 'includes/dbConnection.php';
global $connection;

$message = ''; // متغير هنشيل فيه رسالة النجاح أو الخطأ عشان نعرضها لليوزر

// 2. الكود اللي هيتنفذ لما اليوزر يدوس على زرار "تسجيل"
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $_POST['full_name'] ?? '';
    $email     = $_POST['email'] ?? '';
    $password  = $_POST['password'] ?? '';
    $phone     = $_POST['phone'] ?? '';

    // Validation بسيط
    if (empty($full_name) || empty($email) || empty($password)) {
        $message = "<div class='alert alert-danger'>برجاء إدخال جميع البيانات المطلوبة!</div>";
    } else {
        // إدخال البيانات في الداتا بيز
        $query = "INSERT INTO `users` (full_name, email, password, phone, role) 
                  VALUES ('$full_name', '$email', '$password', '$phone', 'patient')";

        if (mysqli_query($connection, $query)) {
            $message = "<div class='alert alert-success'>تم إنشاء الحساب بنجاح! تقدر تسجل دخول دلوقتي.</div>";
        } else {
            $message = "<div class='alert alert-danger'>حدث خطأ: الإيميل ده متسجل بيه قبل كده.</div>";
        }
    }
}

// 3. استدعاء الهيدر (بداية تصميم الصفحة)
require 'includes/header.php';
?>

<div class="row justify-content-center mt-5">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-header bg-primary text-white text-center">
                <h4>إنشاء حساب مريض جديد</h4>
            </div>
            <div class="card-body p-4">

                <?= $message; ?>

                <form method="POST" action="register.php">
                    <div class="mb-3">
                        <label class="form-label">الاسم بالكامل</label>
                        <input type="text" name="full_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">البريد الإلكتروني</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">رقم التليفون</label>
                        <input type="text" name="phone" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">كلمة المرور</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">تسجيل الحساب</button>
                </form>

                <div class="text-center mt-3">
                    <p>عندك حساب بالفعل؟ <a href="login.php">سجل دخول من هنا</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// 5. استدعاء الفوتر (نهاية تصميم الصفحة)
require 'includes/footer.php';
?>