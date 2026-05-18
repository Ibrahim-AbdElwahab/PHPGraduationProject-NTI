<?php
session_start();
require 'includes/dbConnection.php';
global $connection;

// 1. حماية الصفحة: لو اليوزر مش عامل لوجن، نطرده على صفحة اللوجن
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$message = '';
// 2. نجيب رقم الدكتور من الرابط (الـ URL)
$doctor_id = $_GET['doctor_id'] ?? 0;

// نتأكد إن الدكتور ده موجود فعلاً في الداتا بيز عشان نعرض اسمه
$doc_query = "SELECT * FROM `doctors` WHERE id = '$doctor_id'";
$doc_result = mysqli_query($connection, $doc_query);

if (mysqli_num_rows($doc_result) == 0) {
    die("<h3 class='text-center mt-5 text-danger'>عفواً، هذا الطبيب غير موجود!</h3>");
}
$doctor = mysqli_fetch_assoc($doc_result);

// 3. الكود اللي هيتنفذ لما المريض يختار الميعاد ويدوس "تأكيد الحجز"
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $date = $_POST['appointment_date'] ?? '';
    $time = $_POST['appointment_time'] ?? '';
    $patient_id = $_SESSION['user_id']; // بنجيب رقم المريض من الـ Session المخفية

    if (empty($date) || empty($time)) {
        $message = "<div class='alert alert-danger'>برجاء اختيار التاريخ والوقت!</div>";
    } else {
        // ندخل الحجز في الداتا بيز (الحالة الافتراضية pending)
        $query = "INSERT INTO `appointments` (patient_id, doctor_id, appointment_date, appointment_time, status) 
                  VALUES ('$patient_id', '$doctor_id', '$date', '$time', 'pending')";

        if (mysqli_query($connection, $query)) {
            $message = "<div class='alert alert-success'>🎉 تم تسجيل الحجز بنجاح! في انتظار تأكيد العيادة. 
                        <a href='index.php' class='alert-link'>العودة للرئيسية</a></div>";
        } else {
            $message = "<div class='alert alert-danger'>حدث خطأ أثناء الحجز، حاول مرة أخرى.</div>";
        }
    }
}

// استدعاء الهيدر
require 'includes/header.php';
?>

<div class="row justify-content-center mt-5">
    <div class="col-md-6">
        <div class="card shadow border-0">
            <div class="card-header bg-primary text-white text-center">
                <h4>📅 حجز موعد جديد</h4>
            </div>
            <div class="card-body p-4">

                <?= $message; ?>

                <div class="alert alert-secondary text-center">
                    <h5 class="mb-1">د. <?= $doctor['doctor_name']; ?></h5>
                    <p class="mb-0 text-muted"><?= $doctor['specialty']; ?> | كشف: <?= $doctor['consultation_fee']; ?> ج.م</p>
                </div>

                <form method="POST" action="book.php?doctor_id=<?= $doctor_id; ?>">
                    <div class="mb-3">
                        <label class="form-label">اختر تاريخ الكشف</label>
                        <input type="date" name="appointment_date" class="form-control" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">اختر وقت الكشف</label>
                        <input type="time" name="appointment_time" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-success w-100 fs-5 shadow-sm">تأكيد الحجز</button>
                </form>

            </div>
        </div>
    </div>
</div>

<?php require 'includes/footer.php'; ?>