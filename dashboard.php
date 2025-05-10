<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Redirect based on role
if ($_SESSION['role'] == 'student') {
    header("Location: student_dashboard.php");
} else {
    header("Location: admin_dashboard.php");
}
exit;
?>