<?php
session_start();
include 'php/config.php';

// Mengecek apakah user sudah login dan memiliki peran sebagai kaprodi
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'kaprodi') {
    // Jika tidak, arahkan ke halaman utama
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
// Query untuk mendapatkan informasi user berdasarkan user_id
$sql = "SELECT * FROM users WHERE id='$user_id'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kaprodi Dashboard</title>
    <!-- Memuat CSS dari Bootstrap dan FontAwesome untuk styling -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <style>
        /* CSS untuk styling halaman */
        body {
            background: #f4f4f4;
            font-family: Arial, sans-serif;
        }
        .header {
            background-color: #007965; 
            color: #fff;
            padding: 15px; 
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 1.2rem;
        }
        .header i {
            font-size: 1.5rem;
        }
        .sidebar {
            height: 100vh;
            background-color: #00463a;
            padding: 10px;
            position: fixed;
            width: 220px;
            top: 0;
            left: 0;
            transition: width 0.3s;
        }
        .sidebar a {
            color: #fff;
            display: block;
            padding: 15px;
            text-decoration: none;
        }
        .sidebar a:hover {
            background-color: #007965;
            color: #fff;
        }
        .sidebar .icon {
            margin-right: 10px;
        }
        .dashboard {
            margin-left: 240px;
            padding: 20px;
            transition: margin-left 0.3s;
        }
        .card {
            margin-top: 20px;
        }
        .btn-custom {
            background: #007bff;
            color: #fff;
        }
        .btn-custom:hover {
            background: #0056b3;
            color: #fff;
        }
        .dropdown-menu-right {
            right: 0;
            left: auto;
        }
        .profile-dropdown-menu,
        .notification-dropdown-menu {
            right: 0;
            left: auto;
            top: 50px;
            display: none;
            position: absolute;
            z-index: 1000;
        }
        .dropdown.show .profile-dropdown-menu,
        .dropdown.show .notification-dropdown-menu {
            display: block;
        }
        .dashboard-container {
            display: flex;
            justify-content: space-between;
        }
        .dashboard-item {
            width: 100%;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .dashboard-item h3 {
            margin-bottom: 15px;
        }
        .dashboard-item p {
            margin-bottom: 0;
        }
    </style>
</head>
    <body>
    <!-- Header dengan tombol toggle untuk menu sidebar dan dropdown untuk notifikasi serta profil -->
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
                    <a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Sidebar untuk navigasi -->
    <div class="sidebar" id="sidebar">
        <a href="kaprodi_dashboard.php"><i class="fas fa-tachometer-alt icon"></i> Dashboard</a>
        <a href="../php/admin/manage_schedule.php"><i class="fas fa-calendar-alt icon"></i> View Documents</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt icon"></i> Logout</a>
    </div>
    <!-- Bagian utama dashboard -->
    <div class="dashboard" id="dashboard">
        <div class="header mb-4">
            <h1>Program Head Dashboard</h1>
        </div>
        <!-- Bagian container-fluid masih kosong -->
        <div class="container-fluid">
            <!-- Content khusus kaprodi bisa ditambahkan di sini -->
            <div class="row">
                <!-- Dashboard item untuk manage schedule -->
                <div class="col-md-6">
                    <div class="dashboard-item">
                        <h3>Documents</h3>
                        <p>View documents</p>
                        <a href="../php/admin/manage_schedule.php" class="btn btn-custom">View Documents</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Memuat JavaScript dari jQuery, Popper.js, dan Bootstrap -->
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


