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
if ($result) {
    $user = $result->fetch_assoc();
} else {
    // Handle error if the query fails
    echo "Error: " . $conn->error;
    exit();
}

// Ambil name dari user
$name = $user['name'];

// Query untuk mendapatkan upcoming events dengan filter berdasarkan name user
$sql_events = "SELECT name, nim, dosen1, dosen2, booked_date, status, room, examiners, time
              FROM schedules
              WHERE status = 'approved'
              AND room IS NOT NULL
              AND examiners IS NOT NULL
              AND time IS NOT NULL
              AND name = '$name'  -- Membandingkan dengan name dari user
              ORDER BY booked_date ASC";


$result_events = $conn->query($sql_events);

// Hitung jumlah upcoming events
$upcoming_events_count = $result_events->num_rows;

// Ambil tanggal saat ini
$current_date = date('Y-m-d');

// Query untuk mengambil semua jadwal yang sudah dipesan dan belum terlewat
$sql_booked = "SELECT booked_date FROM schedules WHERE booked_date >= '$current_date'";
$result_booked = $conn->query($sql_booked);

$bookedSchedules = [];
if ($result_booked->num_rows > 0) {
    while ($row = $result_booked->fetch_assoc()) {
        $bookedSchedules[] = $row['booked_date'];
    }
}

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
                <?php if ($upcoming_events_count > 0): ?>
                    <span class="badge badge-danger"><?php echo $upcoming_events_count; ?></span>
                <?php endif; ?>
            </a>
            <div class="dropdown-menu dropdown-menu-right notification-dropdown-menu" aria-labelledby="notificationDropdown">
                <div class="dropdown-item-text">
                    <strong>Notifications</strong>
                </div>
                <div class="dropdown-divider"></div>
                <?php
                if ($upcoming_events_count > 0) {
                    // Jalankan ulang query untuk mendapatkan upcoming events
                    $result_events = $conn->query($sql_events);
                    while ($event = $result_events->fetch_assoc()) {
                        echo "<a class='dropdown-item' href='#'>Event: {$event['name']}<br>Date: {$event['booked_date']}</a>";
                        echo "<div class='dropdown-divider'></div>";
                    }
                } else {
                    echo "<a class='dropdown-item' href='#'>No new notifications</a>";
                }
                ?>
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
    <p><img src="https://www.upnvj.ac.id/id/files/thumb/89f8a80e388ced3704b091e21f510755/520"><span> MAHASISWA</span></p>
    <a href="profile.php"><i class="fas fa-home icon"></i> <span> Dashboard</span></a>
    <a href="php/schedule.php"><i class="fas fa-calendar-alt icon"></i><span> Schedule</span></a>
    <a href="logout.php"><i class="fas fa-sign-out-alt icon"></i><span> Logout</span></a>
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
                <?php
                if ($upcoming_events_count > 0) {
                    // Jalankan ulang query untuk mendapatkan upcoming events
                    $result_events = $conn->query($sql_events);
                    while ($event = $result_events->fetch_assoc()) {
                        echo "<p>Name: {$event['name']}<br>";
                        echo "NIM: {$event['nim']}<br>";
                        echo "Dosen 1: {$event['dosen1']}<br>";
                        echo "Dosen 2: {$event['dosen2']}<br>";
                        echo "Booked Date: {$event['booked_date']}<br>";
                        echo "Status: {$event['status']}<br>";
                        echo "Room: {$event['room']}<br>";
                        echo "Examiners: {$event['examiners']}<br>";
                        echo "Time: {$event['time']}</p>";
                    }
                } else {
                    echo "<p>No upcoming events.</p>";
                }
                ?>
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
        var dropdownMenu = document.querySelector('.notification-dropdown-menu');
        var profileMenu = document.querySelector('.profile-dropdown-menu');
        dropdownMenu.classList.toggle('show');
        profileMenu.classList.remove('show');
    });

    // Menambahkan event listener untuk menyembunyikan menu dropdown ketika mengklik di luar menu
    window.addEventListener('click', function(e) {
        var profileMenu = document.querySelector('.profile-dropdown-menu');
        var notificationMenu = document.querySelector('.notification-dropdown-menu');
        if (!e.target.matches('#navbarDropdown')) {
            profileMenu.classList.remove('show');
        }
        if (!e.target.matches('#notificationDropdown')) {
            notificationMenu.classList.remove('show');
        }
    });

    // Menginisialisasi FullCalendar
    document.addEventListener('DOMContentLoaded', function() {
        $('#calendar').fullCalendar({
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            editable: true,
            events: [
                <?php foreach ($bookedSchedules as $date): ?>
                {
                    start: '<?php echo $date; ?>',
                    rendering: 'background',
                    color: '#ff9f89'
                },
                <?php endforeach; ?>
            ]
        });
    });

    // Menambahkan event listener untuk toggle sidebar
    document.getElementById('menu-toggle').addEventListener('click', function() {
        var sidebar = document.getElementById('sidebar');
        var dashboard = document.getElementById('dashboard');
        sidebar.classList.toggle('active');
        dashboard.classList.toggle('active');
    });
</script>
</body>
</html>