<?php
session_start();
include 'php/config.php';

// Pastikan pengguna masuk dan memiliki peran sebagai 'kaprodi'
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'kaprodi') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
// Ambil informasi pengguna
$sql = "SELECT * FROM users WHERE id='$user_id'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

// Ambil semua jadwal
$sql_schedules = "SELECT * FROM schedules";
$schedules = $conn->query($sql_schedules);

// Proses penambahan jadwal baru
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_schedule'])) {
    $room = $_POST['room'];
    $time = $_POST['time'];
    $examiners = $_POST['examiners'];

    // Ambil id mahasiswa dari sesi atau sumber lainnya
    $student_id = $_SESSION['user_id'];
    $schedule_id = generate_schedule_id(); // Fungsi untuk menghasilkan schedule_id unik
    $status = 'pending';

    // Query untuk insert
    $sql_insert = "INSERT INTO schedules (student_id, schedule_id, room, time, examiners, status) 
                   VALUES ('$student_id', '$schedule_id', '$room', '$time', '$examiners', '$status')";

    if ($conn->query($sql_insert) === TRUE) {
        // Redirect setelah insert berhasil
        header("Location: manage.php");
        exit();
    } else {
        echo "Error: " . $sql_insert . "<br>" . $conn->error;
    }
}

// Proses update jadwal
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_schedule'])) {
    $schedule_id = $_POST['schedule_id'];
    $room = $_POST['room'];
    $time = $_POST['time'];
    $examiners = $_POST['examiners'];

    // Query untuk update
    $sql_update = "UPDATE schedules SET room='$room', time='$time', examiners='$examiners' WHERE schedule_id='$schedule_id'";

    if ($conn->query($sql_update) === TRUE) {
        // Set session untuk notifikasi
        $_SESSION['update_success'] = "Schedule has been updated successfully.";
        // Redirect setelah update berhasil
        header("Location: manage.php");
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kaprodi Manage Schedule</title>
    <!-- CSS Bootstrap and FontAwesome -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="css/manage.css">
</head>
<body>
<!-- Header with toggle button for sidebar and dropdowns for notifications and profile -->
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
<!-- Sidebar for navigation -->
<div class="sidebar" id="sidebar">
    <p><img src="https://www.upnvj.ac.id/id/files/thumb/89f8a80e388ced3704b091e21f510755/520"><span> DOSEN</span></p>
    <a href="kaprodi_dashboard.php"><i class="fas fa-tachometer-alt icon"></i> <span>Dashboard</span></a>
    <a href="manage.php"><i class="fas fa-file-alt icon"></i> <span>Manage</span></a>
    <a href="logout.php"><i class="fas fa-sign-out-alt icon"></i> <span>Logout</span></a>
</div>
<!-- Main dashboard content -->
<div class="dashboard" id="dashboard">
    <div class="container-fluid">

        <h2 class="mt-4">Current Schedules</h2>

        <!-- Tampilkan pesan notifikasi -->
        <?php if(isset($_SESSION['update_success'])): ?>
            <div class="alert alert-success">
                <?php echo $_SESSION['update_success']; unset($_SESSION['update_success']); ?>
            </div>
        <?php endif; ?>

        <!-- Display current schedules -->
        <div class="row mt-3">
            <?php while ($schedule = $schedules->fetch_assoc()): ?>
                <div class="col-md-4">
                    <div class="card mb-3">
                        <div class="card-body">
                            <?php if ($schedule['status'] === 'approved'): ?>
                                <form method="POST">
                                    <input type="hidden" name="schedule_id" value="<?php echo $schedule['schedule_id']; ?>">
                                    <div class="form-group">
                                        <label for="room_<?php echo $schedule['schedule_id']; ?>">Room:</label>
                                        <input type="text" class="form-control" id="room_<?php echo $schedule['schedule_id']; ?>" name="room" value="<?php echo $schedule['room']; ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="time_<?php echo $schedule['schedule_id']; ?>">Time:</label>
                                        <input type="time" class="form-control" id="time_<?php echo $schedule['schedule_id']; ?>" name="time" value="<?php echo $schedule['time']; ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="examiners_<?php echo $schedule['schedule_id']; ?>">Examiners:</label>
                                        <input type="text" class="form-control" id="examiners_<?php echo $schedule['schedule_id']; ?>" name="examiners" value="<?php echo $schedule['examiners']; ?>" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary" name="update_schedule">Update Schedule</button>
                                </form>
                            <?php else: ?>
                                <p>Status: <?php echo ucfirst($schedule['status']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

</div>

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

