<?php
session_start();
include '../config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add_schedule'])) {
        $date_time = $_POST['date_time'];
        $capacity = $_POST['capacity'];
        $duration = $_POST['duration'];
        $room = $_POST['room'];

        $sql = "INSERT INTO schedules (date_time, capacity, duration, room) VALUES ('$date_time', '$capacity', '$duration', '$room')";
        if ($conn->query($sql) === TRUE) {
            $success_message = "Schedule added successfully!";
        } else {
            $error_message = "Error: " . $sql . "<br>" . $conn->error;
        }
    } elseif (isset($_POST['delete_schedule_id'])) {
        $schedule_id = $_POST['delete_schedule_id'];

        $sql = "DELETE FROM bookings WHERE schedule_id = '$schedule_id'";
        if ($conn->query($sql) === TRUE) {
            $sql = "DELETE FROM schedules WHERE id = '$schedule_id'";
            if ($conn->query($sql) === TRUE) {
                $success_message = "Schedule deleted successfully!";
            } else {
                $error_message = "Error: " . $sql . "<br>" . $conn->error;
            }
        }else {
            $error_message = "Error: ". $sql. "<br>". $conn->error;
        }
    }
}

$sql = "SELECT * FROM schedules";
$schedules = $conn->query($sql);
$conn->close();
?>


<!DOCTYPE html>
<html>
<head>
    <title>Manage Schedule</title>
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
        }
    </style>
</head>
<body>
    <div class="container my-5">
        <h1 class="text-center">
            <span class="fw-normal text-dark">Manage</span> <span class="text-primary">Schedule</span>
        </h1>
        <hr>

        <?php if (isset($success_message)): ?>
            <div class="alert alert-success text-center"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger text-center"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <div class="d-flex justify-content-between mb-3">
            <a href="../../admin_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>
        <div class="row">
            <div class="col-md-6 mx-auto">
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="date_time" class="form-label">Date and Time</label>
                        <input type="datetime-local" class="form-control" name="date_time" required>
                    </div>
                    <div class="mb-3">
                        <label for="capacity" class="form-label">Capacity</label>
                        <input type="number" class="form-control" name="capacity" required>
                    </div>
                    <div class="mb-3">
                        <label for="duration" class="form-label">Duration (minutes)</label>
                        <input type="number" class="form-control" name="duration" required>
                    </div>
                    <div class="mb-3">
                        <label for="room" class="form-label">Room</label>
                        <input type="text" class="form-control" name="room" required>
                    </div>
                    <div class="text-center">
                        <input type="submit" class="btn btn-primary" name="add_schedule" value="Add Schedule">
                    </div>
                </form>
            </div>
        </div>
        <hr>
        <h2 class="text-center mb-4">Existing Schedules</h2>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Date and Time</th>
                        <th>Capacity</th>
                        <th>Duration</th>
                        <th>Room</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($schedule = $schedules->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $schedule['id']; ?></td>
                            <td><?php echo $schedule['date_time']; ?></td>
                            <td><?php echo $schedule['capacity']; ?></td>
                            <td><?php echo $schedule['duration']; ?></td>
                            <td><?php echo $schedule['room']; ?></td>
                            <td>
                                <form method="POST" action="" onsubmit="return confirm('Are you sure you want to delete this schedule?');" style="display:inline-block;">
                                    <input type="hidden" name="delete_schedule_id" value="<?php echo $schedule['id']; ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
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