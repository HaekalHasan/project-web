<?php
session_start();
include '../config.php';

// Ensure the user is logged in and has the role of admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'kaprodi') {
    header("Location: ../login.php");
    exit();
}

// Query to fetch documents from schedules table
$sql = "SELECT id, student_id, name, nim, judul_ta, dosen1, dosen2, file_path AS file_name, created_at AS uploaded_at, statuses AS approval FROM schedules";
$schedules = $conn->query($sql);
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Schedules</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
        }
        .table-container {
            margin-top: 30px;
        }
    </style>
</head>
<body>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>