<?php
session_start();
include '../config.php';

// Ensure the user is logged in and has the role of admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'kaprodi') {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
// Query untuk mendapatkan informasi user berdasarkan user_id
$sql = "SELECT * FROM users WHERE id='$user_id'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

// Query to fetch documents from schedules table
$sql = "SELECT id, student_id, name, nim, judul_ta, dosen1, dosen2, file_path AS file_name, created_at AS uploaded_at, status AS approval FROM schedules";
$schedules = $conn->query($sql);
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Schedules</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css"><link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <link rel="stylesheet" href="../../css/manage.css">
</head>
<body>
<div class="header">
    <i class="fas fa-bars" id="menu-toggle"></i>
    <div class="d-flex align-items-center">
        <div class="dropdown mr-3">
            <a href="#" class="text-white dropdown-toggle" id="notificationDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-bell"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-right notification-dropdown-menu" aria-labelledby="notificationDropdown">
                <div class="dropdown-item-text">
                    <strong>Notifications</strong>
                </div>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="#">No new notifications</a>
            </div>
        </div>
        <div class="dropdown">
            <a href="#" class="text-white dropdown-toggle" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-user-circle mr-1"></i>
                <?php echo $user['name']; ?>
            </a>
            <div class="dropdown-menu dropdown-menu-right profile-dropdown-menu" aria-labelledby="navbarDropdown">
                <div class="dropdown-item-text">
                    <strong><?php echo $user['name']; ?></strong><br>
                    <small><?php echo $user['email']; ?></small>
                </div>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="../../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
    </div>
</div>
<!-- Sidebar untuk navigasi -->
<div class="sidebar" id="sidebar">
    <p><img src="https://www.upnvj.ac.id/id/files/thumb/89f8a80e388ced3704b091e21f510755/520"><span> ADMIN</span></p>
    <a href="../../admin_dashboard.php"><i class="fas fa-tachometer-alt icon"></i> <span>Dashboard</span></a>
    <a href="manage_users.php"><i class="fas fa-users icon"></i> <span>Manage Users</span></a>
    <a href="manage_schedule.php"><i class="fas fa-calendar-alt icon"></i> <span>Manage Schedule</span></a>
    <a href="../../logout.php"><i class="fas fa-sign-out-alt icon"></i> <span>Logout</span></a>
</div>
<div class="dashboard" id="dashboard">
    <div class="container">
        <h1 class="text-center">
            <span class="fw-normal text-dark">Manage</span> <span class="text-primary">Schedules</span>
        </h1>
        <div class="d-flex justify-content-between mb-4">
            <a href="../../admin_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>
        <div class="table-container">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Student ID</th>
                        <th>Name</th>
                        <th>NIM</th>
                        <th>Judul TA</th>
                        <th>Dosen 1</th>
                        <th>Dosen 2</th>
                        <th>File Name</th>
                        <th>Uploaded At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($schedule = $schedules->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $schedule['id']; ?></td>
                            <td><?php echo $schedule['student_id']; ?></td>
                            <td><?php echo $schedule['name']; ?></td>
                            <td><?php echo $schedule['nim']; ?></td>
                            <td><?php echo $schedule['judul_ta']; ?></td>
                            <td><?php echo $schedule['dosen1']; ?></td>
                            <td><?php echo $schedule['dosen2']; ?></td>
                            <td><?php echo $schedule['file_name']; ?></td>
                            <td><?php echo $schedule['uploaded_at']; ?></td>
                            <td>
                                <a href="<?php echo '../uploads/' . basename($schedule['file_name']); ?>" class="btn btn-success btn-sm" download>Download</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    // Menambahkan event listener untuk menampilkan atau menyembunyikan menu dropdown profil
    document.getElementById('navbarDropdown').addEventListener('click', function() {
        var dropdownMenu = document.querySelector('.profile-dropdown-menu');
        var notificationMenu = document.querySelector('.notification-dropdown-menu');
        dropdownMenu.classList.toggle('show');
        notificationMenu.classList.remove('show');
    });

    // Menambahkan event listener untuk menampilkan atau menyembunyikan menu dropdown notifikasi
    document.getElementById('notificationDropdown').addEventListener('click', function() {
        var notificationMenu = document.querySelector('.notification-dropdown-menu');
        var dropdownMenu = document.querySelector('.profile-dropdown-menu');
        notificationMenu.classList.toggle('show');
        dropdownMenu.classList.remove('show');
    });


    // Menambahkan event listener untuk toggle menu sidebar
    document.getElementById('menu-toggle').addEventListener('click', function() {
        var sidebar = document.getElementById('sidebar');
        var dashboard = document.getElementById('dashboard');
        sidebar.classList.toggle('collapsed');
        dashboard.classList.toggle('collapsed');
    });
</script>
</body>
</html>