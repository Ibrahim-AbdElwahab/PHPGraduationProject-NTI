<?php
// START Session & Access Control Block
session_start();

//  Secure the page from unauthorized access
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
// Session & Access Control Block

require '../includes/dbConnection.php';
global $connection;

$message = '';

//Process form submission when clicking save button
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    //Sanitize form data inputs to prevent SQL Injection vulnerabilities
    $doctor_name      = mysqli_real_escape_string($connection, trim($_POST['doctor_name']));
    $specialty        = mysqli_real_escape_string($connection, trim($_POST['specialty']));
    $consultation_fee = mysqli_real_escape_string($connection, trim($_POST['consultation_fee']));
    
    $image_url        = 'https://cdn-icons-png.flaticon.com/512/3774/3774299.png'; 

    //Data fields validation check with strict English messages
    if (empty($doctor_name) || empty($specialty) || empty($consultation_fee)) {
        $message = "<div class='alert alert-danger'>⚠️ All fields are mandatory! Please fill them up.</div>";
    } else {
        
        // Perform database insert query into doctors table
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
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إضافة طبيب جديد - لوحة التحكم</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="fw-bold text-dark">➕ إضافة ملف طبيب جديد</h3>
                <a href="index.php" class="btn btn-secondary">العودة للوحة التحكم</a>
            </div>
            
            <div class="card shadow border-0">
                <div class="card-body p-4">
                    <?= $message; ?>
                    
                    <form method="POST" action="addDoctor.php">
                        <div class="mb-3">
                            <label class="form-label">الاسم بالكامل للطبيب</label>
                            <input type="text" name="doctor_name" class="form-control" placeholder="مثال: د. أحمد محمد" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">التخصص الطبي</label>
                            <input type="text" name="specialty" class="form-control" placeholder="مثال: طب الأسنان، أمراض القلب" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">سعر الكشف (بالجنيه المصري)</label>
                            <input type="number" name="consultation_fee" class="form-control" min="0" placeholder="مثال: 300" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 fs-5 shadow-sm">حفظ بيانات الطبيب</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>