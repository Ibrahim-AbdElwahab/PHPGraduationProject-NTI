<?php
// 1. لازم نبدأ الـ Session في أول سطر خالص عشان نقدر نحفظ بيانات اليوزر لما يدخل
session_start();

require 'includes/dbConnection.php';
global $connection;

$message = '';

// 2. الكود اللي هيتنفذ لما اليوزر يدوس "دخول"
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email    = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $message = "<div class='alert alert-danger'>برجاء إدخال الإيميل وكلمة المرور!</div>";
    } else {
        // بنسأل الداتا بيز: هل في يوزر بالإيميل والباسورد دول؟
        $query = "SELECT * FROM `users` WHERE email='$email' AND password='$password'";
        $result = mysqli_query($connection, $query);

        // لو لقينا اليوزر
        if (mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);

            // 3. بنخزن بياناته في الـ Sessions عشان الموقع كله يفضل فاكره
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];

            // 4. توجيه اليوزر (Redirect) حسب هو مريض ولا مدير
            if ($user['role'] == 'admin') {
                header("Location: admin/index.php"); // لو مدير، يروح لوحة التحكم
            } else {
                header("Location: index.php"); // لو مريض، يروح الصفحة الرئيسية يحجز
            }
            exit(); // بنوقف السكريبت هنا عشان التوجيه يشتغل صح
        } else {
            // لو الإيميل أو الباسورد غلط
            $message = "<div class='alert alert-danger'>البيانات غير صحيحة، تأكد من الإيميل وكلمة المرور!</div>";
        }
    }
}

// استدعاء الهيدر
require 'includes/header.php';
?>

<div class="row justify-content-center mt-5">
    <div class="col-md-5">
        <div class="card shadow">
            <div class="card-header bg-success text-white text-center">
                <h4>تسجيل الدخول</h4>
            </div>
            <div class="card-body p-4">

                <?= $message; ?>

                <form method="POST" action="login.php">
                    <div class="mb-3">
                        <label class="form-label">البريد الإلكتروني</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">كلمة المرور</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100">دخول</button>
                </form>

                <div class="text-center mt-3">
                    <p>معندكش حساب؟ <a href="register.php">سجل حساب جديد من هنا</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require 'includes/footer.php'; ?>