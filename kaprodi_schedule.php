<?php
session_start();
include '../config.php'; 

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'kaprodi') {
    header("Location: ../../login.php"); 
    exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id='$user_id'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_POST['student_id'];
    $examiner1_id = $_POST['examiner1_id'];
    $examiner2_id = $_POST['examiner2_id'];
    $room_id = $_POST['room_id'];
    $exam_time = $_POST['exam_time'];

    $sql = "INSERT INTO exam_schedules (student_id, examiner1_id, examiner2_id, room_id, exam_time) VALUES ('$student_id', '$examiner1_id', '$examiner2_id', '$room_id', '$exam_time')";
    if ($conn->query($sql) === TRUE) {
        $success = "Exam schedule successfully added";
    } else {
        $error = "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Schedule</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h2 class="mt-5">Manage Thesis Schedule</h2>
        <?php
        if (isset($success)) {
            echo "<div class='alert alert-success'>$success</div>";
        }
        if (isset($error)) {
            echo "<div class='alert alert-danger'>$error</div>";
        }
        ?>
        <form method="post" action="">
            <div class="form-group">
                <label for="student_id">Student ID</label>
                <input type="text" class="form-control" id="student_id" name="student_id" required>
            </div>
            <div class="form-group">
                <label for="examiner1_id">Penguji 1</label>
                <input type="text" class="form-control" id="examiner1_id" name="examiner1_id" required>
            </div>
            <div class="form-group">
                <label for="examiner2_id">Penguji 2</label>
                <input type="text" class="form-control" id="examiner2_id" name="examiner2_id" required>
            </div>
            <div class="form-group">
                <label for="room_id">Room Number</label>
                <input type="text" class="form-control" id="room_id" name="room_id" required>
            </div>
            <div class="form-group">
                <label for="exam_time">Time</label>
                <input type="datetime-local" class="form-control" id="exam_time" name="exam_time" required>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>



