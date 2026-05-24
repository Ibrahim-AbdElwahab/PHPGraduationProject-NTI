<?php


// 1. لازم نبدأ السيشين عشان نقدر نوصل لها ونمسحها
//  Start the session to gain access to session variables
session_start();

// 2. تفريغ جميع متغيرات الـ Session المخزنة
//  Unset all of the session variables
$_SESSION = array();

// 3. تدمير السيشين بالكامل من السيرفر
//  Destroy the session entirely
if (session_id() != "" || isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 2592000, '/');
}
session_destroy();

// 4. توجيه المستخدم تلقائياً لصفحة تسجيل الدخول الرئيسية
//  Redirect the user back to the main login page
header("Location: login.php");
exit();

// ==========================================
// END: Logout & Session Destruction Block
// ==========================================
?>