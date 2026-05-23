<?php

session_start();

// بالعربي: حماية الصفحة من أي دخول غير مصرح به
// English: Secure the page from unauthorized access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
// ==========================================
// END: Session & Access Control Block
// ==========================================

require '../includes/dbConnection.php';
global $connection;

$message = '';

// بالعربي: معالجة البيانات المرسلة عند الضغط على زر الحفظ
// English: Process form submission when clicking save button
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // بالعربي: تنظيف تخرجات الفورم لحماية الداتا بيز من الـ SQL Injection
    // English: Sanitize form data inputs to prevent SQL Injection vulnerabilities
    $doctor_name      = mysqli_real_escape_string($connection, trim($_POST['doctor_name']));
    $specialty        = mysqli_real_escape_string($connection, trim($_POST['specialty']));
    $consultation_fee = mysqli_real_escape_string($connection, trim($_POST['consultation_fee']));
    
    // صورة افتراضية للطبيب الجديد لعدم كسر تصميم الكروت في الإندكس الرئيسي
    $image_url        = 'https://cdn-icons-png.flaticon.com/512/3774/3774299.png'; 

    // بالعربي: التحقق من الحقول المطلوبة (Validation)
    // English: Data fields validation check
    if (empty($doctor_name) || empty($specialty) || empty($consultation_fee)) {
        $message = "<div class='alert alert-danger'>⚠️ All fields are mandatory! Please fill them up.</div>";
    } else {
        // بالعربي: إدخال البيانات في جدول doctors
        // English: Perform database insert query into doctors table
        $query = "INSERT INTO `doctors` (doctor_name, specialty, consultation_fee, image_url) 
                  VALUES ('$doctor_name', '$specialty', '$consultation_fee', '$image_url')";

        if (mysqli_query($connection, $query)) {
            $message = "<div class='alert alert-success'>🎉 Doctor has been successfully added to the system!</div>";
        } else {
            $message = "<div class='alert alert-danger'>❌ System Error: Failed to save record into the database.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <title>Add New Doctor - System Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="fw-bold text-dark">➕ Add New Doctor Profile</h3>
                <a href="index.php" class="btn btn-secondary">Back to Admin Panel</a>
            </div>
            
            <div class="card shadow border-0">
                <div class="card-body p-4">
                    <?= $message; ?>
                  <form method="POST" action="addDoctor.php">
                        <div class="mb-3">
                            <label class="form-label">Doctor Full Name</label>
                            <input type="text" name="doctor_name" class="form-control" placeholder="e.g., Dr. John Doe" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Medical Specialty</label>
                            <input type="text" name="specialty" class="form-control" placeholder="e.g., Dentistry, Cardiology" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Consultation Fee (EGP)</label>
                            <input type="number" name="consultation_fee" class="form-control" min="0" placeholder="e.g., 300" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 fs-5 shadow-sm">Save Doctor Profile</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>