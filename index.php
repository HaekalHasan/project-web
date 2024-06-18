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
<body class="index">
    <div class="container">
        <h4 id="p">Welcome to the Project Web</h4>
        <h4 >Please <a href="login.php">Login</a> to continue.</h4>
    </div>
</body>
</html>
