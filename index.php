<?php
session_start();

if (isset($_SESSION['user_role'])) {
    if ($_SESSION['user_role'] === 'admin') {
        header("Location: admin_dashboard.html");
    } else {
        header("Location: profile.html");
    }
} else {
    header("Location: login.html");
}
?>
