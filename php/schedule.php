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
    // Jika pengguna tidak ditemukan, lakukan sesuatu, misalnya kembalikan ke halaman login
    header("Location: login.php");
    exit();
}

// Query untuk mengambil semua jadwal yang sudah dipesan
$sql = "SELECT schedule_id FROM schedules";
$result = $conn->query($sql);

$bookedSchedules = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
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
    <style>
        body {
            background-color: #f4f4f4;
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
        .container {
            max-width: 800px;
            margin: 50px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        form {
            margin-bottom: 20px;
        }
        select, input[type="text"], input[type="tel"], input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            background-color: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
            padding: 10px 20px;
            border-radius: 4px;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        a {
            display: block;
            text-align: center;
            margin-top: 10px;
            text-decoration: none;
            color: #007bff;
        }
        a:hover {
            text-decoration: underline;
        }
        .selected-day {
            background-color: #007bff; /* Warna latar belakang yang menandakan tanggal yang dipilih */
            color: white; /* Warna teks yang kontras dengan latar belakang */
            border-radius: 50%; /* Membuat tampilan lingkaran pada tanggal yang dipilih */
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.3); /* Menambahkan bayangan untuk menyoroti tanggal yang dipilih */
        }

    </style>
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
        <a href="../profile.php"><i class="fas fa-home icon"></i> Dashboard</a>
        <a href="schedule.php"><i class="fas fa-calendar-alt icon"></i> Schedule</a>
        <a href="../documents.php"><i class="fas fa-file-alt icon"></i> Documents</a>
        <a href="../logout.php"><i class="fas fa-sign-out-alt icon"></i> Logout</a>
    </div>

    <!-- Bagian utama dashboard -->
    <div class="dashboard" id="dashboard">
        <div class="container schedule">
            <h1>Schedule</h1>
            <?php if (!empty($message)): ?>
                <div class="alert alert-info"><?php echo $message; ?></div>
            <?php endif; ?>
            <form method="POST" action="" enctype="multipart/form-data">
                <input type="text" name="name" placeholder="Nama Mahasiswa" required>
                <input type="text" name="nim" placeholder="NIM" required>
                <input type="text" name="dosen1" placeholder="Dosen Pembimbing 1" required>
                <input type="text" name="dosen2" placeholder="Dosen Pembimbing 2" required>
                <input type="text" name="judul_ta" placeholder="Judul Tugas Akhir" required>
                <input type="tel" name="no_hp" placeholder="No. HP" required>
                <label for="file">Upload File:</label>
                <input type="file" name="file" required>
                <label for="schedule_date">Pilih Jadwal Sidang:</label>
                <div id="calendar"></div>
                <input type="hidden" id="schedule_id" name="schedule_id">
                <input type="submit" value="Book Schedule">
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core.min.js"></script>
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
                editable: false,  // Jadikan tidak bisa diedit jika perlu
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

                    // Menyimpan ID jadwal yang dipilih ke dalam input tersembunyi
                    $('#schedule_id').val(selectedDate);
                    // Menandai tanggal yang dipilih untuk memberikan umpan balik visual kepada pengguna
                    $('.fc-day').removeClass('selected-day');
                    $(this).addClass('selected-day');
                }
            });
        });
    </script>
</body>
</html>
