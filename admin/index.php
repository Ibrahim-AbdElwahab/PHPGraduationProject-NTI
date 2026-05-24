<?php
// ==========================================
// START: Session & Access Control Block
// ==========================================
session_start();

// بالعربي: التحقق من الصلاحية لحماية المجلد، إذا لم يكن أدمن يتم توجيهه لصفحة اللوجن الرئيسية
// English: Check role for security, if not admin redirect to the main login page
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
// ==========================================
// END: Session & Access Control Block
// ==========================================

// بالعربي: استدعاء ملف الاتصال بقاعدة البيانات (نخرج خطوة للخلف للوصول للمجلد الرئيسي)
// English: Include database connection file (step back to reach the main directory)
require '../includes/dbConnection.php';
global $connection;

// بالعربي: جلب الإحصائيات الحالية لعرضها في كروت الداش بورد
// English: Fetch database stats to display them inside dashboard cards
$count_docs = mysqli_fetch_assoc(mysqli_query($connection, "SELECT COUNT(*) as total FROM `doctors`"))['total'];
$count_patients = mysqli_fetch_assoc(mysqli_query($connection, "SELECT COUNT(*) as total FROM `users` WHERE role='patient'"))['total'];
$count_appoin = mysqli_fetch_assoc(mysqli_query($connection, "SELECT COUNT(*) as total FROM `appointments`"))['total'];

// بالعربي: جلب بيانات الحجوزات مع عمل JOIN لجلب اسم المريض واسم الدكتور
// English: Fetch all appointments with JOIN to get patient name and doctor name
$query = "SELECT appointments.*, users.full_name as patient_name, doctors.doctor_name 
          FROM `appointments`
          JOIN `users` ON appointments.patient_id = users.id
          JOIN `doctors` ON appointments.doctor_id = doctors.id
          ORDER BY appointments.id DESC";
$appointments_result = mysqli_query($connection, $query);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>لوحة تحكم المسؤول - نظام العيادة</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">👨‍💻 لوحة تحكم العيادة</a>
        <div class="d-flex align-items-center">
            <span class="text-white me-3">مرحباً بك، <?= htmlspecialchars($_SESSION['user_name']); ?></span>
            <a href="../logout.php" class="btn btn-danger btn-sm">تسجيل الخروج</a>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <div class="row text-center mb-5">
        <div class="col-md-4 mb-3">
            <div class="card bg-primary text-white border-0 shadow-sm">
                <div class="card-body p-4">
                    <h3>👨‍⚕️ عدد الأطباء</h3>
                    <h2 class="display-5 fw-bold"><?= $count_docs; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card bg-success text-white border-0 shadow-sm">
                <div class="card-body p-4">
                    <h3>👥 المرضى المسجلين</h3>
                    <h2 class="display-5 fw-bold"><?= $count_patients; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card bg-warning text-dark border-0 shadow-sm">
                <div class="card-body p-4">
                    <h3>📅 إجمالي المواعيد</h3>
                    <h2 class="display-5 fw-bold"><?= $count_appoin; ?></h2>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-secondary">🗂️ إدارة سجلات المواعيد والحجوزات</h3>
        <a href="addDoctor.php" class="btn btn-primary btn-lg shadow-sm">➕ إضافة طبيب جديد</a>
    </div>

    <div class="card shadow border-0 mb-5">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>رقم الحجز</th>
                            <th>اسم المريض</th>
                            <th>اسم الطبيب</th>
                            <th>تاريخ الموعد</th>
                            <th>وقت الموعد</th>
                            <th>حالة الحجز</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($appointments_result) > 0): ?>
                            <?php while ($row = mysqli_fetch_assoc($appointments_result)): ?>
                                <tr>
                                    <td><strong>#<?= $row['id']; ?></strong></td>
                                    <td><?= htmlspecialchars($row['patient_name']); ?></td>
                                    <td>د. <?= htmlspecialchars($row['doctor_name']); ?></td>
                                    <td><?= $row['appointment_date']; ?></td>
                                    <td><?= $row['appointment_time']; ?></td>
                                    <td>
                                        <?php if ($row['status'] == 'pending'): ?>
                                            <span class="badge bg-warning text-dark px-3 py-2 fs-6">قيد الانتظار</span>
                                        <?php else: ?>
                                            <span class="badge bg-success px-3 py-2 fs-6">مؤكد</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="p-4 text-muted">لا توجد أي سجلات حجوزات حالية داخل قاعدة بيانات نظام العيادة.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>