<?php
session_start();

require 'includes/dbConnection.php';
global $connection;

// استدعاء الهيدر
require 'includes/header.php';
?>

<div class="container mt-4">

    <?php if (isset($_SESSION['user_name'])): ?>
        <div class="alert alert-info shadow-sm">
            أهلاً بك يا <strong><?= $_SESSION['user_name']; ?></strong> في العيادة الذكية! نتمنى لك دوام الصحة.
        </div>
    <?php endif; ?>

    <h2 class="text-center mb-5 mt-4">👨‍⚕️ أطباء العيادة المتاحين</h2>

    <div class="row">
        <?php
        // 3. جملة الـ SQL لجلب كل الدكاترة
        $query = "SELECT * FROM `doctors`";
        $result = mysqli_query($connection, $query);

        // لو الداتا بيز فيها دكاترة، هنلف عليهم بـ while loop ونطبع كارت لكل واحد
        if (mysqli_num_rows($result) > 0) {
            while ($doctor = mysqli_fetch_assoc($result)) {
        ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm border-0">
                        <img src="https://cdn-icons-png.flaticon.com/512/3774/3774299.png" class="card-img-top p-4" alt="Doctor" style="height: 220px; object-fit: contain; background-color: #e9ecef;">

                        <div class="card-body text-center">
                            <h5 class="card-title text-primary fw-bold"><?= $doctor['doctor_name']; ?></h5>
                            <p class="card-text text-secondary fs-5"><?= $doctor['specialty']; ?></p>
                            <h6 class="text-success fw-bold">سعر الكشف: <?= $doctor['consultation_fee']; ?> ج.م</h6>
                        </div>

                        <div class="card-footer bg-white border-0 text-center pb-4">
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <a href="book.php?doctor_id=<?= $doctor['id']; ?>" class="btn btn-primary w-100 fs-5 shadow-sm">احجز الآن</a>
                            <?php else: ?>
                                <a href="login.php" class="btn btn-secondary w-100 shadow-sm">سجل دخول للحجز</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
        <?php
            }
        } else {
            // لو الأدمن لسه مضمنش دكاترة في الداتا بيز
            echo "<div class='col-12 text-center'><div class='alert alert-warning'>لا يوجد أطباء متاحين حالياً في العيادة.</div></div>";
        }
        ?>
    </div>
</div>

<?php
// استدعاء الفوتر
require 'includes/footer.php';
?>