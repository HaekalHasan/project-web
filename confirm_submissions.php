<?php
session_start();
include 'php/config.php';

// Ensure user is logged in and is a 'teacher'
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'teacher') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
// Fetch user information
$sql = "SELECT * FROM users WHERE id='$user_id'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

// Fetch submissions that are still 'pending' or 'approved'
$sql_submissions = "SELECT * FROM schedules WHERE status IN ('pending', 'approved') AND (dosen1 = '{$user['name']}' OR dosen2 = '{$user['name']}')";
$submissions = $conn->query($sql_submissions);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $schedule_id = $_POST['schedule_id'];
    $action = $_POST['action'];

    // Validate and process approval
    if ($action === 'approve') {
        $sql_update = "UPDATE schedules SET status='approved' WHERE schedule_id='$schedule_id'";
        $status_message = "Submission has been approved.";
        
        if ($conn->query($sql_update) === TRUE) {
            $message = $status_message;
        } else {
            $message = "Error updating record: " . $conn->error;
        }
    }

    // Refresh page to reflect latest data
    header("Location: confirm_submissions.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Submissions</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <link rel="stylesheet" href="css/confirm_submissions.css">
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
                <a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </div>
        </div>
    </div>
</div>
<div class="sidebar" id="sidebar">
    <p><img src="https://www.upnvj.ac.id/id/files/thumb/89f8a80e388ced3704b091e21f510755/520"><span> DOSEN</span></p>
    <a href="dosen_dashboard.php"><i class="fas fa-tachometer-alt icon"></i> <span>Dashboard</span></a>
    <a href="confirm_submissions.php"><i class="fas fa-check-circle icon"></i> <span>Confirm</span></a>
    <a href="logout.php"><i class="fas fa-sign-out-alt icon"></i> <span>Logout</span></a>
</div>
<div class="dashboard" id="dashboard">
    <div class="container-fluid">
        <h1>Confirm Submissions</h1>
        <?php if (!empty($message)): ?>
            <div class="alert alert-info"><?php echo $message; ?></div>
        <?php endif; ?>
        <div class="row">
            <?php while($submission = $submissions->fetch_assoc()): ?>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <p class="card-text">
                                <strong>Student           :</strong> <?php echo $submission['name']; ?><br>
                                <strong>NIM               :</strong> <?php echo $submission['nim']; ?><br>
                                <strong>Dosen Pembimbing 1:</strong> <?php echo $submission['dosen1']; ?><br>
                                <strong>Dosen Pembimbing 2:</strong> <?php echo $submission['dosen2']; ?><br>
                                <strong>Phone             :</strong> <?php echo $submission['no_hp']; ?><br>
                                <strong>Date              :</strong> <?php echo $submission['booked_date']; ?><br>
                                <strong>File Title        :</strong> <?php echo $submission['judul_ta']; ?><br>
                                <strong>File              :</strong> <a href="<?php echo $submission['file_path']; ?>" target="_blank">Download</a>
                            </p>
                            <?php if ($submission['status'] === 'pending'): ?>
                                <form method="POST" action="">
                                    <input type="hidden" name="schedule_id" value="<?php echo $submission['schedule_id']; ?>">
                                    <button type="submit" name="action" value="approve" class="btn btn-success">Approve</button>
                                </form>
                            <?php else: ?>
                                <p>Status: <?php echo ucfirst($submission['status']); ?></p>
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
