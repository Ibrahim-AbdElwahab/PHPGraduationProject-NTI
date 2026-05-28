<?php
session_start();

require 'includes/dbConnection.php';
global $connection;

$message = '';

// 2. الكود اللي هيتنفذ لما اليوزر يدوس "دخول"
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email    = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    // START Validation & Security Block
    if (empty($email) || empty($password)) {
        //Check if the input fields are empty
        $message = "<div class='alert alert-danger'>⚠️ Please enter both email and password!</div>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        //Validate email format
        $message = "<div class='alert alert-danger'>⚠️ Invalid email format!</div>";
    } else {
        //  Clean inputs to protect the database from SQL Injection
        $email    = mysqli_real_escape_string($connection, trim($email));
        $password = mysqli_real_escape_string($connection, trim($password));

        //  Query the database for the user with their role
        $query = "SELECT * FROM `users` WHERE email='$email' AND password='$password'";
        $result = mysqli_query($connection, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);

            //  Store user data in the Session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];

            //  Redirect based on user role (Admin or Patient)
            if ($user['role'] == 'admin') {
                header("Location: admin/index.php");
            } else {
                header("Location: index.php");
            }
            exit();
        } else {
            //  English error message for incorrect credentials
            $message = "<div class='alert alert-danger'> Incorrect email or password. Please try again!</div>";
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