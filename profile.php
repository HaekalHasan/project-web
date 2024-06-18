<?php
session_start();
include 'php/config.php';

// Mengecek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    // Jika belum login, arahkan ke halaman login
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
// Query untuk mendapatkan informasi user berdasarkan user_id
$sql = "SELECT * FROM users WHERE id='$user_id'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

// Memproses form jika ada request POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];

    // Query untuk mengupdate informasi user
    $sql = "UPDATE users SET name='$name', email='$email' WHERE id='$user_id'";

    // Mengecek apakah query berhasil dijalankan
    if ($conn->query($sql) === TRUE) {
        $message = "Profile updated successfully";
    } else {
        $message = "Error updating profile: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- Memuat CSS dari Bootstrap, FontAwesome, dan FullCalendar -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css">
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
<div class="header">
    <!-- Tombol untuk toggle menu sidebar -->
    <i class="fas fa-bars" id="menu-toggle"></i>
    <div class="d-flex align-items-center">
        <!-- Dropdown untuk notifikasi -->
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
        <!-- Dropdown untuk profil user -->
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
    <p><img src="https://www.upnvj.ac.id/id/files/thumb/89f8a80e388ced3704b091e21f510755/520">MAHASISWA</p>
    <a href="profile.php"><i class="fas fa-home icon"></i> Dashboard</a>
    <a href="registration.php"><i class="fas fa-user-plus icon"></i> Registration</a>
    <a href="schedule.php"><i class="fas fa-calendar-alt icon"></i> Schedule</a>
    <a href="documents.php"><i class="fas fa-file-alt icon"></i> Documents</a>
    <a href="logout.php"><i class="fas fa-sign-out-alt icon"></i> Logout</a>
</div>

<!-- Bagian utama dashboard -->
<div class="dashboard" id="dashboard">
    <div class="header mb-4">
        <h1>Student Dashboard</h1>
    </div>
    <!-- Menampilkan pesan jika ada -->
    <?php if (isset($message)) { echo "<div class='alert alert-info'>$message</div>"; } ?>
    <div class="dashboard-container">
        <div class="dashboard-item profile">
            <div class="card">
                <div class="card-body">
                    <!-- Form untuk mengupdate profil user -->
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="name">Name:</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo $user['name']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email:</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo $user['email']; ?>" required>
                        </div>
                        <button type="submit" class="btn btn-custom">Update</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="dashboard-item">
            <div class="upcoming-events">
                <h5>Upcoming Events</h5>
                <p>No upcoming events.</p>
            </div>
            <div id="calendar"></div>
        </div>
    </div>
</div>
<!-- Memuat JavaScript dari jQuery, Popper.js, Bootstrap, dan FullCalendar -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>
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

    // Menginisialisasi FullCalendar
    $(document).ready(function() {
        $('#calendar').fullCalendar({
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            editable: true,
            events: []
        });
    });
</script>
</body>
</html>
