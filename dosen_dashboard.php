<?php
session_start();
include 'php/config.php';

// Mengecek apakah user sudah login dan memiliki peran sebagai dosen
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'teacher') {
    // Jika tidak, arahkan ke halaman utama
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Query untuk mendapatkan informasi user berdasarkan user_id
$sql = "SELECT * FROM users WHERE id='$user_id'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

// Query untuk mendapatkan upcoming events dari tabel schedules
$sql_events = "SELECT name, nim, dosen1, dosen2, booked_date, status, room, examiners, time
              FROM schedules
              WHERE status = 'approved'
              AND room IS NOT NULL
              AND examiners IS NOT NULL
              AND time IS NOT NULL
              AND (dosen1 = '{$user['name']}' OR dosen2 = '{$user['name']}')
              ORDER BY booked_date ASC"; // Misalkan booked_date adalah tanggal event

$result_events = $conn->query($sql_events);

// Query untuk menghitung total pengajuan yang perlu disetujui
$sql_pending_approvals = "SELECT id FROM schedules WHERE status = 'pending' AND (dosen1 = '{$user['name']}' OR dosen2 = '{$user['name']}')";
$result_pending_approvals = $conn->query($sql_pending_approvals);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dosen Dashboard</title>
    <!-- Memuat CSS dari Bootstrap dan FontAwesome untuk styling -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
<!-- Header dengan tombol toggle untuk menu sidebar dan dropdown untuk notifikasi serta profil -->
<div class="header">
    <i class="fas fa-bars" id="menu-toggle"></i>
    <div class="d-flex align-items-center">
        <div class="dropdown mr-3">
            <a href="#" class="text-white dropdown-toggle" id="notificationDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-bell"></i>
                <?php if ($result_pending_approvals->num_rows > 0): ?>
                    <span class="badge badge-danger"><?php echo $result_pending_approvals->num_rows; ?></span>
                <?php endif; ?>
            </a>
            <div class="dropdown-menu dropdown-menu-right notification-dropdown-menu" aria-labelledby="notificationDropdown">
                <div class="dropdown-item-text">
                    <strong>Notifications</strong>
                </div>
                <div class="dropdown-divider"></div>
                <!-- Isi notifikasi bisa diperbarui di sini -->
                <?php if ($result_pending_approvals->num_rows > 0): ?>
                    <?php while ($row = $result_pending_approvals->fetch_assoc()): ?>
                        <a class="dropdown-item" href="confirm_submissions.php"><i class="fas fa-check-circle icon"></i> <span>You have pending approval</span></a>
                    <?php endwhile; ?>
                <?php else: ?>
                    <a class="dropdown-item" href="#">No new notifications</a>
                <?php endif; ?>
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
                <a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
    </div>
</div>
<!-- Sidebar dengan menu navigasi -->
<div class="sidebar" id="sidebar">
    <p><img src="https://www.upnvj.ac.id/id/files/thumb/89f8a80e388ced3704b091e21f510755/520"><span> DOSEN</span></p>
    <a href="dosen_dashboard.php"><i class="fas fa-tachometer-alt icon"></i> <span>Dashboard</span></a>
    <a href="confirm_submissions.php"><i class="fas fa-check-circle icon"></i> <span>Confirm</span></a>
    <a href="logout.php"><i class="fas fa-sign-out-alt icon"></i> <span>Logout</span></a>
</div>
<!-- Konten utama dashboard -->
<div class="dashboard" id="dashboard">
    <div class="header mb-4">
        <h1> Lecturer Dashboard</h1>
    </div>
    <div class="container-fluid">
        <!-- Kolom untuk menampilkan daftar event yang akan datang -->
        <div class="row">
            <div class="col-md-6">
                <div class="dashboard-item">
                    <div class="card">
                        <div class="card-body">
                            <h3>My Upcoming Events</h3>
                            <ul class="list-group">
                                <?php if ($result_events->num_rows > 0): ?>
                                    <?php while ($event = $result_events->fetch_assoc()): ?>
                                        <li class="list-group-item">
                                            <strong><?php echo $event['name']; ?></strong><br>
                                            NIM: <?php echo $event['nim']; ?><br>
                                            Date: <?php echo $event['booked_date']; ?><br>
                                            Time: <?php echo $event['time']; ?><br>
                                            Room: <?php echo $event['room']; ?><br>
                                            Examiners: <?php echo $event['examiners']; ?><br>
                                            Status: <?php echo $event['status']; ?>
                                        </li>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <li class="list-group-item">No upcoming events found.</li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Memuat script JS untuk toggle menu dan dropdown -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    document.getElementById('navbarDropdown').addEventListener('click', function() {
        var dropdownMenu = document.querySelector('.profile-dropdown-menu');
        var notificationMenu = document.querySelector('.notification-dropdown-menu');
        dropdownMenu.classList.toggle('show');
        notificationMenu.classList.remove('show');
    });

    document.getElementById('notificationDropdown').addEventListener('click', function() {
        var notificationMenu = document.querySelector('.notification-dropdown-menu');
        var dropdownMenu = document.querySelector('.profile-dropdown-menu');
        notificationMenu.classList.toggle('show');
        dropdownMenu.classList.remove('show');
    });

    document.getElementById('menu-toggle').addEventListener('click', function() {
        var sidebar = document.getElementById('sidebar');
        var dashboard = document.getElementById('dashboard');
        sidebar.classList.toggle('collapsed');
        dashboard.classList.toggle('collapsed');
    });
</script>
</body>
</html>
