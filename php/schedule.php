<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'student') {
    header("Location: login.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $schedule_id = $_POST['schedule_id'];
    $student_id = $_SESSION['user_id'];

    $sql = "INSERT INTO schedule_bookings (schedule_id, student_id) VALUES ('$schedule_id', '$student_id')";

    if ($conn->query($sql) === TRUE) {
        echo "Schedule booked successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$sql = "SELECT * FROM schedules";
$schedules = $conn->query($sql);
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Schedule</title>
</head>
<body>
    <h1>Available Schedules</h1>
    <form method="POST" action="">
        <select name="schedule_id">
            <?php while($schedule = $schedules->fetch_assoc()): ?>
                <option value="<?php echo $schedule['id']; ?>"><?php echo $schedule['date_time']; ?></option>
            <?php endwhile; ?>
        </select>
        <input type="submit" value="Book Schedule">
    </form>
</body>
</html>
