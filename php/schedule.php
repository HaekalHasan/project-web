<?php
session_start();
include 'config.php';

// Pastikan pengguna sudah login dan memiliki role sebagai student
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'student') {
    header("Location: login.php");
    exit();
}

// Ambil data pengguna dari sesi
$user_id = $_SESSION['user_id'];

// Query untuk mengambil data pengguna
$userSql = "SELECT * FROM users WHERE id = '$user_id'";
$userResult = $conn->query($userSql);

// Periksa apakah pengguna ditemukan
if ($userResult->num_rows > 0) {
    $user = $userResult->fetch_assoc(); // Mengambil data pengguna dari hasil query
} else {
    // Jika pengguna tidak ditemukan, kembalikan ke halaman login
    header("Location: login.php");
    exit();
}

// Query untuk mengambil semua jadwal yang sudah dipesan
$sql = "SELECT schedule_id FROM schedules";
$result = $conn->query($sql);

$bookedSchedules = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $bookedSchedules[] = $row['schedule_id'];
    }
}

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $schedule_id = $_POST['schedule_id'];

    // Validasi apakah tanggal sudah dibooking
    $sql_check_booking = "SELECT * FROM schedules WHERE schedule_id = '$schedule_id'";
    $result_check_booking = $conn->query($sql_check_booking);

    if ($result_check_booking->num_rows > 0) {
        $message = "Tanggal ini sudah dibooking, pilih tanggal lain.";
    } else {
        // Lanjutkan dengan proses booking seperti sebelumnya
        $student_id = $_SESSION['user_id'];
        $name = $_POST['name'];
        $nim = $_POST['nim'];
        $dosen1 = $_POST['dosen1'];
        $dosen2 = $_POST['dosen2'];
        $judul_ta = $_POST['judul_ta'];
        $no_hp = $_POST['no_hp'];
        $booked_date = $_POST['schedule_id']; // Tanggal yang dibooking

        // Handle file upload
        $uploadDir = 'uploads/';
        $uploadFile = $uploadDir . basename($_FILES['file']['name']);

        if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile)) {
            // Insert into database
            $sql = "INSERT INTO schedules (student_id, schedule_id, name, nim, dosen1, dosen2, judul_ta, no_hp, file_path, booked_date) 
                    VALUES ('$student_id', '$schedule_id', '$name', '$nim', '$dosen1', '$dosen2', '$judul_ta', '$no_hp', '$uploadFile', '$booked_date')";

            if ($conn->query($sql) === TRUE) {
                $message = "Schedule booked successfully";
                // Tambahkan tanggal baru ke array bookedSchedules
                $bookedSchedules[] = $booked_date;
                // Mengosongkan form setelah booking berhasil
                $_POST = array(); // Mengosongkan $_POST
            } else {
                $message = "Error: " . $sql . "<br>" . $conn->error;
            }
        } else {
            $message = "Error uploading file.";
        }
    }
}

// Query to fetch available schedules
$sql = "SELECT * FROM schedules";
$schedules = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/schedule.css">
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
                    <a class="dropdown-item" href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>
        </div>
    </div>
    <!-- Sidebar untuk navigasi -->
    <div class="sidebar" id="sidebar">
        <p><img src="https://www.upnvj.ac.id/id/files/thumb/89f8a80e388ced3704b091e21f510755/520"><span> MAHASISWA</span></p>
        <a href="../profile.php"><i class="fas fa-home icon"></i> <span>Dashboard</span></a>
        <a href="schedule.php"><i class="fas fa-calendar-alt icon"></i> <span>Schedule</span></a>
        <a href="../logout.php"><i class="fas fa-sign-out-alt icon"></i> <span>Logout</span></a>
    </div>

    <!-- Bagian utama dashboard -->
    <div class="dashboard" id="dashboard">
        <div class="container schedule">
            <h1>Schedule</h1>
            <?php if (!empty($message)): ?>
                <div class="alert alert-info"><?php echo $message; ?></div>
            <?php endif; ?>
            <form id="bookingForm" method="POST" action="" enctype="multipart/form-data">
                <input type="text" name="name" placeholder="Nama Mahasiswa" value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>" required>
                <input type="text" name="nim" placeholder="NIM" value="<?php echo isset($_POST['nim']) ? htmlspecialchars($_POST['nim']) : ''; ?>" required>
                <input type="text" name="dosen1" placeholder="Dosen Pembimbing 1" value="<?php echo isset($_POST['dosen1']) ? htmlspecialchars($_POST['dosen1']) : ''; ?>" required>
                <input type="text" name="dosen2" placeholder="Dosen Pembimbing 2" value="<?php echo isset($_POST['dosen2']) ? htmlspecialchars($_POST['dosen2']) : ''; ?>" required>
                <input type="text" name="judul_ta" placeholder="Judul Tugas Akhir" value="<?php echo isset($_POST['judul_ta']) ? htmlspecialchars($_POST['judul_ta']) : ''; ?>" required>
                <input type="tel" name="no_hp" placeholder="No. HP" value="<?php echo isset($_POST['no_hp']) ? htmlspecialchars($_POST['no_hp']) : ''; ?>" required>
                <label for="file">Upload File:</label>
                <input type="file" name="file" required>
                <label for="schedule_date">Pilih Jadwal Sidang:</label>
                <div id="calendar"></div>
                <input type="hidden" id="schedule_id" name="schedule_id" value="<?php echo isset($_POST['schedule_id']) ? $_POST['schedule_id'] : ''; ?>">
                <input type="submit" value="Book Schedule">
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
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

        $(document).ready(function() {
            var bookedSchedules = <?php echo json_encode($bookedSchedules); ?>;

            $('#calendar').fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay'
                },
                editable: false,
                events: bookedSchedules.map(function(schedule) {
                    return {
                        start: schedule,
                        end: schedule,
                        rendering: 'background',
                        color: '#ff9f89'
                    };
                }),
                dayClick: function(date, jsEvent, view) {
                    var selectedDate = date.format();

                    if (bookedSchedules.includes(selectedDate)) {
                        alert("Tanggal ini sudah dibooking, pilih tanggal lain.");
                        return;
                    }

                    $('#schedule_id').val(selectedDate);
                    $('.fc-day').removeClass('selected-day');
                    $(this).addClass('selected-day');
                }
            });

            // Mengosongkan form setelah refresh halaman jika booking berhasil
            <?php if (!empty($message) && strpos($message, 'Schedule booked successfully') !== false): ?>
                $('#bookingForm')[0].reset(); // Reset form
                $('#schedule_id').val(''); // Reset hidden field
                // Tambahkan tanggal baru ke kalender
                var newEvent = {
                    start: '<?php echo $booked_date; ?>',
                    end: '<?php echo $booked_date; ?>',
                    rendering: 'background',
                    color: '#ff9f89'
                };
                $('#calendar').fullCalendar('renderEvent', newEvent);
            <?php endif; ?>
        });
    </script>
</body>
</html>