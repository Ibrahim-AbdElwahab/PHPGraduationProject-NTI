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

    // START: Validation & Registration Block
    if (empty($full_name) || empty($email) || empty($password)) {
        // التأكد من إدخال الحقول الإلزامية
        $message = "<div class='alert alert-danger'>⚠️ Please fill in all required fields!</div>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        //  الفالديشن الخاص بصحة الإيميل
        $message = "<div class='alert alert-danger'>⚠️ Invalid email address format!</div>";
    } elseif (strlen($password) < 6) {
        //  التأكد من قوة كلمة المرور (6 أحرف على الأقل)
        $message = "<div class='alert alert-danger'>⚠️ Password must be at least 6 characters long!</div>";
    } elseif (!empty($phone) && !preg_match('/^[0-9]+$/', $phone)) {
        //  التأكد من أن الهاتف يحتوي على أرقام فقط
        $message = "<div class='alert alert-danger'>⚠️ Phone number must contain digits only!</div>";
    } else {
        //  تنظيف البيانات لتأمين الاستعلام من أي اختراق
        $full_name = mysqli_real_escape_string($connection, trim($full_name));
        $email     = mysqli_real_escape_string($connection, trim($email));
        $password  = mysqli_real_escape_string($connection, trim($password));
        $phone     = mysqli_real_escape_string($connection, trim($phone));

        //  التحقق أولاً من أن الإيميل غير مكرر في النظام
        $check_email = "SELECT id FROM `users` WHERE email = '$email'";
        $check_result = mysqli_query($connection, $check_email);

        if (mysqli_num_rows($check_result) > 0) {
            $message = "<div class='alert alert-danger'> This email is already registered!</div>";
        } else {
            //  إدخال البيانات في جدول المستخدمين بصلاحية مريض
            $query = "INSERT INTO `users` (full_name, email, password, phone, role) 
                          VALUES ('$full_name', '$email', '$password', '$phone', 'patient')";

            if (mysqli_query($connection, $query)) {
                $message = "<div class='alert alert-success'>🎉 Account created successfully! You can now <a href='login.php' class='alert-link'>Login</a>.</div>";
            } else {
                $message = "<div class='alert alert-danger'> Something went wrong. Please try again later.</div>";
            }
        }
    }
}

//
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
require 'includes/footer.php';
?>