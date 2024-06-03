<?php
session_start();
include 'php/config.php';

// Jika pengguna sudah login, arahkan mereka ke halaman profil
if (isset($_SESSION['user_id'])) {
    header("Location: profile.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Home</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <h1>Welcome to the Project Web</h1>
    <p>Please <a href="login.php">Login</a> or <a href="register.php">Register</a> to continue.</p>
</body>
</html>
