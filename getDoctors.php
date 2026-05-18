<?php
header('Content-Type: application/json; charset=utf-8');
require 'includes/dbconnection.php';
global $connection;

// هنجيب كل الدكاترة المتاحين
$query = "SELECT id, doctor_name, specialty, consultation_fee, image_url FROM `doctors`";
$result = mysqli_query($connection, $query);

if ($result) {
    $doctors = mysqli_fetch_all($result, MYSQLI_ASSOC);
    echo json_encode(['status' => 'success', 'data' => $doctors]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'فشل في جلب البيانات']);
}
