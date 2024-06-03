<?php
session_start();
include 'php/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
    <h1>Admin Dashboard</h1>
    <a href="php/admin/manage_users.php">Manage Users</a><br>
    <a href="php/admin/manage_schedule.php">Manage Schedule</a><br>
    <a href="php/admin/manage_documents.php">Manage Documents</a>
</body>
</html>
